<?php

namespace Application\Core;
require_once(dirname(__FILE__) . "/../controllers/Start.php");
require_once(dirname(__FILE__) . "/../controllers/Ready.php");
require_once(dirname(__FILE__) . "/../controllers/PlaceShip.php");

use Application\Controllers\Start;
use Application\Controllers\Ready;
use Exception;
use Application\Controllers\PlaceShip;


class Router {
    private static $routes = [
        'api' => [
            'start' => [Start::class, 'startGame'],
            'ready' => [Ready::class, 'userReady'],
            'place-ship' => [PlaceShip::class, 'placeShip'],

        ]
    ];

    public static function Route() {
        $url = rtrim($_SERVER['REQUEST_URI'], '/');
        $urls = array_slice(explode('/', $url), 1);
        $params = array_slice(explode('/', $url), 3);
        $urls = self::$routes[$urls[0]][$urls[1]];
        $controllerName = $urls[0];
        $method = $urls[1];
        try {
            $controller = new $controllerName();
            $controller->$method($params);
        } catch (Exception $ex) {
            header('HTTP/1.1 400 Bad Request');
        }
    }
}