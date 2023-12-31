<?php

declare(strict_types = 1);                      // Use strict types

use Cms\Validate\Validate;                      // Import validate class

include '../src/bootstrap.php';                  // Setup file
$from = '';                                     //Initialize: from
$message = '';                                  //Message
$errors = [];                                   //Array for errors
$success = '';                                  //Success message

if($_SERVER['REQUEST_METHOD'] == 'POST') {          //If form submitted
    $from = $_POST['email'];                        //Email address
    $message = $_POST['message'];                   //Message
    $errors['email'] = Validate::IsEmail($from)  ? '' : 'Email not valid';
    $errors['message'] = Validate::IsText($message, 1, 1000) ? '' : 'Please enter a 
        message up to 1000 characters';
    $invalid = implode($errors);                  //Join any error messages

    if($invalid) {                                //If there are errors
        $errors['warning'] = 'Please check form errors'; //Warning

    } else {
        $subject = "Contact form message from " . $from;    //Create message body
        $email = new \Cms\Email\Email($email_config);       //Create email object
        $email->sendEmail($email_config['admin_email'], $email_config['admin_email'], $subject, $message);
        $success = 'Your message has been sent';            //Success message
    }

}
$data['navigation'] = $cms->getCategory()->getAll();

// The following values are only created if the user has submitted the form
$data['from'] = $from;                      //From email
$data['message'] = $message;                //Message
$data['errors'] = $errors;                  //Error messages
$data['success'] = $success;                //Success message

echo $twig->render('contact.html', $data);





