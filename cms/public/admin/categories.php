<?php
declare(strict_types = 1);                             // Use strict types
include '../../src/bootstrap.php';                      //Include setup file

$data['success'] = $_GET['success'] ?? null;
$data['failure'] = $_GET['failure'] ?? null;

$data['categories'] = $cms->getCategory()->getAll();            //Get all categories

echo $twig->render('admin/categories.html', $data);   //render template

?>



