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



