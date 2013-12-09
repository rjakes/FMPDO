<?php

/**
 * FMDPO Autoloader
 * Sets required include paths, inits an autoloader
 *
 */


$result = set_include_path(get_include_path() . PATH_SEPARATOR.$DOCUMENT_ROOT . '/library');
$result = set_include_path(get_include_path() . PATH_SEPARATOR.$DOCUMENT_ROOT . '/library/rjakes/FMPDO/classes');

spl_autoload_extensions(".php");

spl_autoload_register('Autoload');

function Autoload($class_name) {
    require_once (str_replace("\\", "/", $class_name) . '.php');
}