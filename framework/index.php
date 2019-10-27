<?php
session_start();
require_once("core/load.php");

$url_parameters = $app->page->get_url_parameters();

// Add the data to the class
$controller = count($url_parameters) > 0 ? strtolower($url_parameters[0]) : '';
$action = count($url_parameters) > 1 ? strtolower($url_parameters[1]) : '';

// Get the model file
$app->page->load_model($controller);

// Get the controller file
$app->page->load_controller($controller, $action);

?>