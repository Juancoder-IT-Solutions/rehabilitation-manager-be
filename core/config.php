<?php
error_reporting(1);
//ini_set('max_execution_time', '-1');
ini_set('date.timezone',"Asia/Manila");
date_default_timezone_set("Asia/Manila");

require 'env.php';


// THIS WILL LOAD ONLY THE NEEDED CLASS
spl_autoload_register(function ($class) {

    include __DIR__ . '/autoloader.php';

    if (array_key_exists($class, $classes)) {
        require_once $classes[$class];
    }
});
