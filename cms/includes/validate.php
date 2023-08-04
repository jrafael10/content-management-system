<?php

function is_number($number, $min = 0, $max = 100):bool
{
    return ($number >= $min and $number <= $max);
}

function is_text($text, $min = 0, $max = 1000)
{
    $length = mb_strlen($text);
    return ($length >= $min and $length <= $max);
}
