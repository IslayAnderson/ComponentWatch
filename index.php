<?php
include_once("./Functions/functions.php");
include_once("./Classes/classes.php");

//check if init has been run (set it global memory I guess) and run init 
//what will be in init?
//the data store and all that jazz :)

$request = $_SERVER['REQUEST_URI'];

switch ($request) {
    case '/' /*|| '/index.php' */:
        require __DIR__ . '/pages/index.php';
        break;
    case 'reporters' :
        require __DIR__ . '/pages/view_reporters.php';
        break;
    default:
        http_response_code(404);
        require __DIR__ . '/pages/404.php';
        break;
}