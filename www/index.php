<?php

require_once(dirname(__FILE__) . "/application/model/ModelGames.php");
require_once(dirname(__FILE__) . "/application/core/Controller.php");
require_once(dirname(__FILE__) . "/application/controllers/Status.php");

//$model = new \Application\Model\ModelGames();
//$controller = new \Application\Controllers\Status();
//$res = $controller->gameInfo([22, "6100fbec45e3d"]);
//return;

ini_set('display_errors', '1');
error_reporting(E_ALL);

require_once('application/core/Router.php');

use Application\Core\Router;

Router::Route();