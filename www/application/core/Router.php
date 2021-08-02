<?php

namespace Application\Core;
require_once(dirname(__FILE__) . "/../controllers/Start.php");
require_once(dirname(__FILE__) . "/../controllers/Ready.php");
require_once(dirname(__FILE__) . "/../controllers/PlaceShip.php");
require_once(dirname(__FILE__) . "/../controllers/ChatLoad.php");
require_once(dirname(__FILE__) . "/../controllers/ChatSend.php");
require_once(dirname(__FILE__) . "/../controllers/ClearField.php");
require_once(dirname(__FILE__) . "/../controllers/Shot.php");
require_once(dirname(__FILE__) . "/../controllers/Status.php");

use Application\Controllers\Start;
use Application\Controllers\Ready;
use Application\Controllers\PlaceShip;
use Application\Controllers\ChatLoad;
use Application\Controllers\ChatSend;
use Application\Controllers\ClearField;
use Application\Controllers\Shot;
use Application\Controllers\Status;
use Exception;

class Router {
    private static $routes = [
        'api' => [
            'start' => [Start::class, 'startGame'],
            'ready' => [Ready::class, 'userReady'],
            'place-ship' => [PlaceShip::class, 'placeShip'],
            'chat-load' => [ChatLoad::class, 'loadMessages'],
            'chat-send' => [ChatSend::class, 'sendMessage'],
            'clear-field' => [ClearField::class, 'removeAll'],
            'shot' => [Shot::class, 'shot'],
            'status' => [Status::class, 'gameInfo'],
        ]
    ];

    public static function Route() {
        $url = rtrim($_SERVER['REQUEST_URI'], '/');
        $urls = array_slice(explode('/', $url), 1);
        $params = array_slice(explode('/', $url), 3);
        $params[1] = explode('?', $params[1])[0];
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