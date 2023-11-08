<?php
declare(strict_types=1);            //Use strict types
use Cms\Validate\Validate;
include '../../src/bootstrap.php'; //Include setup files
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT); //Get and validate id
$article = [];                     //Initialize article array
$errors = [
  'alt' => '',
  'warning' => '',
];                              //Initialize error message

if(!$id) {                      //If no id
    redirect('articles.php', ['failure' => 'Article not found']); //Redirect
}

$article = $cms->getArticle()->get($id, false);   //GEt article
if(!$article['image_file']){
    redirect('article.php', ['id' => $id]);
}

if($_SERVER['REQUEST_METHOD'] == 'POST') {              //If form was submitted
    $article['image_alt'] =  $_POST['image_alt'];       //Get alt text

    $errors['alt'] = (Validate::isText($article['image_alt'], 1, 254))
        ? '' : 'Alt text for image should be 1 - 254 characters.'; //Validate alt text

    if($errors['alt']) {                                //if not valid
        $errors['warning'] = 'Please correct error below';// Create warning message
    } else {
        $cms->getArticle()->altUpdate($article['image_id'], $article['image_alt']); //Update alt text
        redirect('admin/article.php', ['id' => $id]);
    }
}

$data['article'] = $article;
$data['errors'] = $errors;

echo $twig->render('admin/alt-text-edit.html', $data); //  Render template

?>

<?php /*include APP_ROOT . '/public/includes/admin-header.php';?>
    <main class="container admin" id="content">
        <form action="alt-text-edit.php?id=<?= $id ?>" method="POST" class="narrow">
            <h1>Update Alt Text</h1>
            <?php if($errors['warning']) {?><div class="alert alert-danger"><?=$errors['warning']?></div><?php } ?>
            <div class="form-group">
                <label for="image_alt">Alt text: </label>
                <input type="text" name="image_alt" id="image_alt" value="<?= html_escape($article['image_alt']) ?>" class="form-control">
                <span class="errors"> <?= $errors['alt'] ?></span>
            </div>

            <div class="form-group">
                <input type="submit" name="delete" value="Update" class="btn btn-primary btn-save">
            </div>

            <img src="../uploads/<?= html_escape($article['image_file']) ?>" alt="<?= $article['image_alt'] ?>">
        </form>
    </main>
<?php include APP_ROOT . '/public/includes/admin-footer.php'; */?>


