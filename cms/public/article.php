<?php
declare(strict_types = 1);                      //Use strict types
include '../src/bootstrap.php';

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT); // Validate id

if(!$id) {                                          //if no valid id
    include APP_ROOT . '/public/page-not-found.php'; //Page not found
}


$article = $cms->getArticle()->get($id);            //Get article data
if(!$article) {                                     //If article not found
    include APP_ROOT . '/public/page-not-found.php'; //Page not found
}

$data['navigation'] = $cms->getCategory()->getAll();            // Get categories
$data['article'] = $article;                                    //Article
$data['section'] = $article['category_id'];                     //current category

echo $twig->render('article.html', $data);              //Render template
?>












