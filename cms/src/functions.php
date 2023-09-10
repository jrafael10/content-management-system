<?php

//DATABASE FUNCTIOn
function pdo(PDO $pdo, string $sql, array $arguments = null)
{
    if(!$arguments) {               //If no arguments
        return $pdo->query($sql);   //Run SQL and return PDOstatement object
    }

    $statement = $pdo->prepare($sql); //If arguments prepare statement
    $statement->execute($arguments); //Execute statement
    return $statement;              //Return PDOStatement object
}

// FORMATTING FUNCTIONS
function html_escape($text): string
{
    return htmlspecialchars($text,ENT_QUOTES, 'UTF-8', false ); //Return escaped string
}

function format_date(string $string):string
{
    $date = date_create_from_format('Y-m-d H:i:s', $string); //Convert to DateTime object
    return $date->format('F d, Y');                     //Return in format Jan 31, 2023
}

//UTILITY FUNCTIONS

function redirect(string $location, array $parameters = [], $response_code = 302)
{
    $qs = $parameters ? '?' . http_build_query($parameters) : ''; //Create query string
    $location = $location .$qs;                                 //Create new Path
    header('Location: ' . $location, $response_code ); // Redirect to new page
    exit;
}

function create_filename(string $filename): string
{
    $basename = pathinfo($filename, PATHINFO_FILENAME);
    $extension = pathinfo($filename, PATHINFO_EXTENSION);
    $cleanname = preg_replace("/[^A-z0-9]/", "-", $basename); //clean basename
    $filename = $cleanname . '.' . $extension; //Destination
    $i = 0;                                     //Counter
    while(file_exists($filename)) {
        $i = $i +1;
        $filename = $cleanname . $i . "." . $extension;
    }
    return  $filename;
}

// ERROR AND EXCEPTION HANDLING FUNCTIONS
// Convert errors to exceptions

function handle_error($error_type, $error_message, $error_file, $error_line){
    throw new ErrorException($error_message, 0, $error_type, $error_file, $error_line); //Turn into ErrorException

}

// Handle exceptions - log exception and show error message (if server does not send error page listed in .htaccess)
function handle_exception($e)
{
    error_log($e);
    http_response_code(500);
    echo "<h1>Sorry, a problem occurred</h1>   
          The site's owners have been informed. Please try again later.";
}

/*function handle_shutdown()
{
    $error = error_get_last(); //Check for error in script
    if($error !== null) {

    }
}

*/









