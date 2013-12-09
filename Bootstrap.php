<?php

/**
 * FMDPO Autoloader
 * Sets required include paths, inits an autoloader
 *
 */


set_include_path(get_include_path() . PATH_SEPARATOR. __DIR__ . '/library');
set_include_path(get_include_path() . PATH_SEPARATOR.__DIR__ . '/library/rjakes/Fmpdo');

spl_autoload_register('Autoload');

function Autoload($class_name) {
    require_once (str_replace("\\", DIRECTORY_SEPARATOR, $class_name) . '.php');
}

require_once(__DIR__ . '/library/rjakes/FMPDO/Fmpdo.php');