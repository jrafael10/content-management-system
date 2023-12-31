<?php
define('APP_ROOT', dirname(__FILE__, 2)); //Root directory
require APP_ROOT . '/src/functions.php';        //Functions
require APP_ROOT . '/config/config.php';        //Configuration data
require APP_ROOT . '/vendor/autoload.php';


if(DEV === false) {

    set_exception_handler('handle_exception');
    set_error_handler('handle_error');
    register_shutdown_function('handle_shutdown');
}


$cms = new \Cms\CMS\CMS($dsn, $username, $password); //Create CMS object                                                                                    CMS($dsn, $username, $password);    //Create CMS object
unset($dsn, $username, $password);          //Remove database config data

$twig_options['cache'] = APP_ROOT . '/var/cache';       //Path to Twig cache folder
$twig_options['debug'] = DEV;

$loader = new Twig\Loader\FilesystemLoader(APP_ROOT . '/templates'); //Twig loader
$twig = new Twig\Environment($loader, $twig_options); //Twig environment
$twig->addGlobal('doc_root', DOC_ROOT);     //Document root

if(DEV === true ) {                                    //If in development
    $twig->addExtension(new \Twig\Extension\DebugExtension());//Add Twig debug extension
}



