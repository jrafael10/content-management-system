<?php

//Part A: Setup
declare(strict_types = 1);//Use strict types
use Cms\Validate\Validate;
include '../../src/bootstrap.php';      //Include setup file

// Initialize variables that the PHP code needs
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);  // Get id + validate
$temp = $_FILES['image']['tmp_name'] ?? '';   //Temporary image
$destination = '';                          //Where to save file
$saved = null;

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
    $article =  $cms->getArticle()->get($id, false);

    if(!$article){                                                  //If article empty
        redirect('admin/articles.php', ['failure' => 'Article not found']); //Redirect
    }
}

$saved_image = $article['image_file'] ? true :  false; //Has an image been uploaded
$authors = $cms->getMember()->getAll();        //Get all members
$categories = $cms->getCategory()->getAll();

// Part B: Get and validate form data

if($_SERVER['REQUEST_METHOD'] == 'POST') { //If form submitted
    // If file bigger than limit in php.ini or .htaccess store error message
    $errors['image_file'] = ($_FILES['image']['error'] === 1) ? 'File too big ' : '';
    // If image was uploaded, get image data and validate
    if($temp and $_FILES['image']['error'] == 0) {                 //If file uploaded and no error
        $article['image_alt'] = $_POST['image_alt'];                //Get all text

        //Validate image file
        $errors['image_file'] = in_array(mime_content_type($temp), MEDIA_TYPES) ? '': 'Wrong file type. '; // Check file type
        $extension = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION)); // File extension in lowercase
        $errors['image_file'] .= in_array($extension,FILE_EXTENSIONS) ? '' : 'Wrong file extension. '; //Check file extension
        $errors['image_file'] .= ($_FILES['image']['size'] <= MAX_SIZE) ? '' : 'File too big. ';       //Check size
        $errors['image_alt'] = (Validate::isText($article['image_alt'], 1, 254)) ? '' : 'Alt text must be 1-254 characters.'; //Check alt text

        // If image file is valid, specify the location to save it
        if($errors['image_file'] === '' and $errors['image_alt'] === '') { //If valid
            $article['image_file'] = create_filename($_FILES['image']['name'], UPLOADS);
            $destination = UPLOADS . $article['image_file'];
        }

    }

    //Get article data
    $article['title'] = $_POST['title'];                            //Get title
    $article['summary'] = $_POST['summary'];                        // Get summary
    $article['content'] = $_POST['content'];                        // Get content
    $article['member_id'] = $_POST['member_id'];                    // Get member_id
    $article['category_id'] = $_POST['category_id'];                // Get category_id
    $article['published']  = (isset($_POST['published']) and ($_POST['published'] == 1)) ? 1 : 0; // Get navigation

    $purifier = new HTMLPurifier();
    $purifier->config->set('HTML.Allowed', 'p,br,strong,em,a[href],img[src|alt]'); //Allowed tags and attributes
    $article['content'] = $purifier->purify($article['content']);
    // Validate article data and create error messages if it is invalid
    $errors['title'] = Validate::isText($article['title'], 1, 80) ? '' : 'Title must be 1-80 characters';
    $errors['summary'] = Validate::isText($article['summary'], 1, 254) ? '' : 'Summary must be 1-254 characters';
    $errors['content'] = Validate::isText($article['content'], 1, 100000) ? '' : 'Article must be 1-100,000 characters';
    $errors['member'] = Validate::isMemberId($article['member_id'], $authors  ) ? '' : 'Please select an author';
    $errors['category'] = Validate::isCategoryId($article['category_id'], $categories) ? '' : 'Please select a category';

    $invalid = implode($errors);                                        // Join errors
    //Part C: Check if data is valid, if so update database
    if($invalid) {                                               //If invalid
        $errors['warning'] = 'Please correct the errors below';  //Store message
    } else {                                                     //otherwise
        $arguments = $article;                                  //Save data as $arguments

        if($id) {
            $saved = $cms->getArticle()->update($arguments, $temp, $destination);  //Update article
        } else {
            unset($arguments['id']);

            $saved = $cms->getArticle()->create($arguments, $temp, $destination); //Create article
        }

        if($saved == true) {                                                  //If updated
            redirect('admin/articles.php' , ['success' => 'Article saved']); //Redirect
        } else {                                                            //Otherwise
            $errors['warning'] = 'Article title already in use';            //Store message
        }

    }

    $article['image_file'] = $saved_image ? $article['image_file'] : '';
}

$data['article'] = $article;
$data['categories'] = $categories;
$data['authors'] = $authors;
$data['errors'] = $errors;

echo $twig->render('admin/article.html', $data);        //Render this template

?>
