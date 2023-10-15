<?php
declare(strict_types=1);                                //Use strict types
include  '../src/bootstrap.php';                        // Setup file

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT); // Validate id
if(!$id) {                                              //If id was not an integer
    include APP_ROOT . '/public/page-not-found.php';      //Page not found
}

$category = $cms->getCategory()->get($id);                       // Get category data
if(!$category) {                                                //If category is empty
    include APP_ROOT . '/public/page-not-found.php';            //Page not found
}


$data['articles'] = $cms->getArticle()->getAll(true, $id);                 //Get articles
$data['navigation'] = $cms->getCategory()->getAll();                      //Get navigation categories
$data['section'] = $category['id'];                                     //Current category
$data['category'] = $category;

echo $twig->render('category.html', $data);                 //Render template









