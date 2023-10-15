<?php
declare(strict_types = 1);                                 // Use strict types
include '../src/bootstrap.php';                          // Setup file

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if(!$id){                                                   //If no valid id
    include APP_ROOT . '/public/page-not-found.php';        //Page not found
    //include 'page-not-found.php';                           //Page not found
}

$member = $cms->getMember()->get($id);                           //GET member data

if(!$member){                                                   //If array is empty
    include APP_ROOT . '/public/page-not-found.php';           //Page not found
}

$data['navigation'] = $cms->getCategory()->getAll();                //Get categories
$data['member'] = $member;                                          //member data
$data['articles'] = $cms->getArticle()->getAll(true, null, $id);     // Get member's articles

echo $twig->render('member.html', $data);





