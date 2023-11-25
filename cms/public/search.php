<?php
declare(strict_types = 1);                                // Use strict types
require 'includes/database-connection.php';               // Create PDO object
//  require 'includes/functions.php';                         // Include functions
include '../src/bootstrap.php';

$data['term'] = filter_input(INPUT_GET, 'term');  //Get search term
$data['show'] = filter_input(INPUT_GET, 'show', FILTER_VALIDATE_INT) ?? 3; //LIMIT
$data['from'] = filter_input(INPUT_GET, 'from', FILTER_VALIDATE_INT) ?? 0; //offset

$data['count'] = 0;                                             //Set count to 0
$data['articles'] = [];                                         //Set articles to empty array

if($data['term']) {                                     //If search term provided
    $data['count'] = $cms->getArticle()->searchCount($data['term']);

    if($data['count'] > 0) {                                  //If articles match term

        $data['articles'] = $cms->getArticle()->search($data['term'], $data['show'], $data['from']);    //Get matches
    }
}

if($data['count'] > $data['show']) {                                //If matches more than show
    $data['total_pages'] = ceil($data['count'] / $data['show']);    //Calculate total pages
    $data['current_page'] = ceil($data['from']/$data['show']) + 1; //Calculate current page
}

$data['navigation'] = $cms->getCategory()->getAll();                //Get categories

echo $twig->render('search.html', $data);








