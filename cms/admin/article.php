<?php

//Part A: Setup
declare(strict_types = 1);

//Use strict types
include '../includes/database-connection.php'; //Database connection
include '../includes/functions.php';        //Functions
include '../includes/validate.php';       // Validate functions
//File upload settings
$uploads = dirname(__DIR__, 1). DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR;  // Image upload folder
$file_types = ['image/jpeg', 'image/png', 'image/gif'];             // Allowed file types
$file_extensions = ['jpg', 'jpeg', 'png', 'gif'];               //Allowed Extensions
$max_size = '5242880';                                          //Max file size

// Initialize variables that the PHP code needs
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);  // Get id + validate
$temp = $_FILES['image']['tmp_name'] ?? '';   //Temporary image
$destination = '';                          //Where to save file

// Initialize variables that the HTML page needs
$article = [
  'id'           => $id,
  'title'        => '',
  'summary'      => '',
  'content'      => '',
  'member_id'    => 0,
  'category_id'  => 0,
  'image_id'     => null,
  'published'    => false,
  'image_file'   => '',
  'image_alt'    => '',
];

$errors = [
  'warning'      => '',
  'title'        => '',
  'summary'      => '',
  'content'      => '',
  'member'      => '',
  'category'    => '',
  'image_file'  => '',
  'image_alt'   => '',

];

// If there was an id, page is editing an article, so get current article data
if($id) {
    $sql = "SELECT a.id, a.title, a.summary, a.content,
                   a.category_id, a.member_id, a.image_id, a.published,
                   i.file AS image_file,
                   i.alt AS image_alt
             FROM article as a
             LEFT JOIN image AS i ON a.image_id = i.id
             WHERE a.id = :id";

    $article = pdo($pdo, $sql, [$id])->fetch();

    if(!$article){                                                  //If article empty
        redirect('articles.php', ['failure' => 'Article not found']); //Redirect
    }

}

//echo '<pre>';
//print_r($article);
//echo '</pre>';

$saved_image = $article['image_file'] ? true :  false; //Has an image been uploaded

//Get all members and all categories
$sql = "SELECT id, forename, surname FROM member;";    //SQL to get all members
$authors = pdo($pdo, $sql)->fetchAll();                 //Get all members

$sql = "SELECT id, name FROM category;";              //SQL to get all categories
$categories = pdo($pdo, $sql)->fetchAll();             //Get all categories

// Part B: Get and validate form data

if($_SERVER['REQUEST_METHOD'] == 'POST') { //If form submitted
    // If file bigger than limit in php.ini or .htaccess store error message
    $errors['image_file'] = ($_FILES['image']['error'] === 1) ? 'Files too big ' : '';
    // If image was uploaded, get image data and validate
    if($temp and $_FILES['image']['error'] === 0) {                 //If file uploaded and no error
        $article['image_alt'] = $_POST['image_alt'];                //Get all text

        //Validate image file
        $errors['image_file'] = in_array(mime_content_type($temp), $file_types) ? '': 'Wrong file type. '; // Check file type
        $extension = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION)); // File extension in lowercase
        $errors['image_file'] .= in_array($extension, $file_extensions) ? '' : 'Wrong file extension. '; //Check file extension
        $errors['image_file'] .= ($_FILES['image']['size'] <= $max_size) ? '' : 'File too big. ';       //Check size
        $errors['image_alt'] = (is_text($article['image_alt'], 1, 254)) ? '' : 'Alt text must be 1-254 characters.'; //Check alt text

        // If image file is valid, specify the location to save it
        if($errors['image_file'] === '' and $errors['image_alt'] === '') { //If valid
            $article['image_file'] = create_filename($_FILES['image']['name'], $uploads);
            $destination = $uploads . $article['image_file'];
        }

    }

    //Get article data
    $article['title'] = $_POST['title'];
    $article['summary'] = $_POST['summary'];
    $article['content'] = $_POST['content'];
    $article['member_id'] = $_POST['member_id'];
    $article['category_id'] = $_POST['category_id'];
    $article['published']  = (isset($_POST['published']) and ($_POST['published'] == 1)) ? 1 : 0; //Is it published?

    // Validate article data and create error messages if it is invalid
    $errors['title'] = is_text($article['title'], 1, 80) ? '' : 'Title must be 1-80 characters';
    $errors['summary'] = is_text($article['summary'], 1, 254) ? '' : 'Summary must be 1-254 characters';
    $errors['content'] = is_text($article['content'], 1, 100000) ? '' : 'Article must be 1-100,000 characters';
    $errors['member'] = is_member_id($article['member_id'], $authors  ) ? '' : 'Please select an author';
    $errors['category'] = is_category_id($article['category_id'], $categories) ? '' : 'Please select a category';

    $invalid = implode($errors);

}

?>
<?php include '../includes/admin-header.php'; ?>

<form action="article.php?id=<?=$id?>" method="POST" enctype="multipart/form-data">
    <main class="container admin" id="content">

        <h1>Edit Article</h1>
        <?php if($errors['warning']) { ?>
            <div class="alert alert-danger"><?= $errors['warning'] ?></div>
        <?php } ?>

        <div class="admin-article">
            <section class="image">
                <?php if(!$article['image_file']) { ?>
                    <label for="image">Upload image:</label>
                    <div class="form-group image-placeholder">
                        <input type="file" name="image" class="form-control-file" id="image"><br>
                        <span class="errors"><?= $errors['image_file']?></span>
                    </div>
                    <div class="form-group">
                        <label for="image_alt">Alt text: </label>
                        <input type="text" name="image_alt" id="image_alt" value="" class="form-control">
                        <span class="errors"><?= $errors['image_alt'] ?></span>
                    </div>
                <?php } else { ?>
                    <label>Image:</label>
                    <img src="../uploads/<?= html_escape($article['image_file']) ?>"
                         alt="<?= html_escape($article['image_alt']) ?>">
                    <p class="alt"><strong>Alt text:</strong>  <?=html_escape($article['image_alt']) ?></p>
                    <a href="alt-text-edit.php?id=<?= $article['id'] ?>" class="btn btn-secondary">Edit alt text</a>
                    <a href="image-delete.php?id=<?= $id ?>" class="btn btn-secondary">Delete image</a><br><br>
                <?php }?>
            </section>

            <section class="text">
                <div class="form-group">
                    <label for="title">Title: </label>
                    <input type="text" name="title" id="title" value="<?= html_escape($article['title']) ?>"
                           class="form-control">
                    <span class="errors"<?= $errors['title'] ?>></span>
                </div>
                <div class="form-group">
                    <label for="summary">Summary: </label>
                    <textarea name="summary" id="summary"
                              class="form-control"></textarea>
                    <span class="errors"></span>
                </div>
                <div class="form-group">
                    <label for="content">Content: </label>
                    <textarea name="content" id="content"
                              class="form-control"></textarea>
                    <span class="errors"></span>
                </div>
                <div class="form-group">
                    <label for="member_id">Author: </label>
                    <select name="member_id" id="member_id">
                        <option value="">
                        </option>

                    </select>
                    <span class="errors"></span>
                </div>
                <div class="form-group">
                    <label for="category">Category: </label>
                    <select name="category_id" id="category">

                        <option value=""></option>

                    </select>
                    <span class="errors"></span>
                </div>
                <div class="form-check">
                    <input type="checkbox" name="published" value="1" class="form-check-input" id="published">
                    <label for="published" class="form-check-label">Published</label>
                </div>
                <input type="submit" name="update" value="Save" class="btn btn-primary">
            </section><!-- /.text -->
        </div>
    </main>
</form>

<?php include '../includes/admin-footer.php'; ?>
