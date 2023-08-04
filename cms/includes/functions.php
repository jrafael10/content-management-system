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









