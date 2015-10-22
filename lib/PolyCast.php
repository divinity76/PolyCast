<?php

namespace theodorejb\polycast;

// conditionally define PHP_INT_MIN since PHP 5.x doesn't
// include it and it's necessary for validating integers.
if (!defined("PHP_INT_MIN")) {
    define("PHP_INT_MIN", ~PHP_INT_MAX);
}

/**
 * Returns true if the value can be safely converted to an integer
 * @param mixed $val
 * @return bool
 */
function safe_int($val)
{
    switch (gettype($val)) {
        case "integer":
            return true;
        case "double":
            return $val === (float)(int)$val;
        case "string":
            $losslessCast = (string)(int)$val;

            if ($val !== $losslessCast && $val !== "+$losslessCast") {
                return false;
            }

            return $val <= PHP_INT_MAX && $val >= PHP_INT_MIN;
        default:
            return false;
    }
}

/**
 * Returns true if the value can be safely converted to a float
 * @param mixed $val
 * @return bool
 */
function safe_float($val)
{
    switch (gettype($val)) {
        case "double":
        case "integer":
            return true;
        case "string":
            // reject leading zeros unless they are followed by a decimal point
            if (strlen($val) > 1 && $val[0] === "0" && $val[1] !== ".") {
                return false;
            }

            // Use regular expressions since FILTER_VALIDATE_FLOAT allows trailing whitespace
            // Based on http://php.net/manual/en/language.types.float.php
            $lnum    = "[0-9]+";
            $dnum    = "([0-9]*[\.]{$lnum})|({$lnum}[\.][0-9]*)";
            $expDnum = "/^[+-]?(({$lnum}|{$dnum})[eE][+-]?{$lnum})$/";

            return
                preg_match("/^[+-]?{$lnum}$/", $val) ||
                preg_match("/^[+-]?{$dnum}$/", $val) ||
                preg_match($expDnum, $val);
        default:
            return false;
    }
}

/**
 * Returns true if the value can be safely converted to a string
 * @param mixed $val
 * @return bool
 */
function safe_string($val)
{
    switch (gettype($val)) {
        case "string":
        case "integer":
        case "double":
            return true;
        case "object":
            return method_exists($val, "__toString");
        default:
            return false;
    }
}

/**
 * Returns the value as an integer
 * @param mixed $val
 * @return int
 * @throws CastException if the value cannot be safely cast to an integer
 */
function to_int($val)
{
    if (!safe_int($val)) {
        throw new CastException("Value could not be converted to int");
    } else {
        return (int)$val;
    }
}

/**
 * Returns the value as a float
 * @param mixed $val
 * @return float
 * @throws CastException if the value cannot be safely cast to a float
 */
function to_float($val)
{
    if (!safe_float($val)) {
        throw new CastException("Value could not be converted to float");
    } else {
        return (float)$val;
    }
}

/**
 * Returns the value as a string
 * @param mixed $val
 * @return string
 * @throws CastException if the value cannot be safely cast to a string
 */
function to_string($val)
{
    if (!safe_string($val)) {
        throw new CastException("Value could not be converted to string");
    } else {
        return (string)$val;
    }
}
