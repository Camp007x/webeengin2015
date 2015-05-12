<?php 
error_reporting(E_ALL);
include 'controller.php';
include_once 'classes.php';


// route the request to the right place
$url_elements = explode('/', $_SERVER['REQUEST_URI']);
$controller_name = ucfirst($url_elements[2]) . 'Controller';
$method = "doIt";

#$data = file_get_contents("php://input");




if (class_exists($controller_name)) {
    $controller = new $controller_name();
    $controller->$method();
} else {
	RestUtils::sendResponse(501,'failed');  
}