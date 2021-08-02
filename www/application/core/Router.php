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
require_once(dirname(__FILE__) . "/../controllers/NotFound.php");

use Application\Controllers\NotFound;
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
            'start' => ['controllerName' => Start::class, 'method' => 'startGame'],
            'ready' => ['controllerName' => Ready::class, 'method' => 'setUserReady'],
            'place-ship' => ['controllerName' => PlaceShip::class, 'method' => 'placeShip'],
            'chat-load' => ['controllerName' => ChatLoad::class, 'method' => 'loadMessages'],
            'chat-send' => ['controllerName' => ChatSend::class, 'method' => 'sendMessage'],
            'clear-field' => ['controllerName' => ClearField::class, 'method' => 'removeAll'],
            'shot' => ['controllerName' => Shot::class, 'method' => 'shot'],
            'status' => ['controllerName' => Status::class, 'method' => 'gameInfo'],
        ]
    ];

    public static function route() {
        $url = trim($_SERVER['REQUEST_URI'], '/');
        $url = explode('?', $url)[0];
        $url = explode('/', $url);
        $handler = self::$routes;
        while (count($url)) {
            $segment = $url[0];
            if ($handler[$segment]) {
                array_shift($url);
                $handler = $handler[$segment];
            } else break;
        }
        extract($handler);
        try {
            $controller = new $controllerName();
            $controller->$method($url);
        } catch (Exception $ex) {
            NotFound::notFound();
        }
    }
}