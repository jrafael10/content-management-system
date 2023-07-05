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







