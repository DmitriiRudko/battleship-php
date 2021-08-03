<?php
ini_set('display_errors', '1');
error_reporting(E_ALL);

require_once('application/core/Router.php');

use Application\Core\Router;
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

Router::route();
