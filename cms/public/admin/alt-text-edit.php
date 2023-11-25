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



