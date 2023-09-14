<?php
define('APP_ROOT', dirname(__FILE__, 2)); //Root directory
require APP_ROOT . '/src/functions.php';        //Functions
require APP_ROOT . '/config/config.php';        //Configuration data


if(DEV === false) {
    set_exception_handler('handle_exception');
    set_error_handler('handle_error');
    register_shutdown_function('handle_shutdown');

}


spl_autoload_register(function($class)          //Set autoload function
{
    $path = APP_ROOT . '/src/classes/';         //Path to class definition
    require $path . $class . '.php';            //Include class definition

});

$cms = new CMS($dsn, $username, $password);    //Create CMS object
unset($dsn, $username, $password);          //Remove database config data



