<?php

namespace Application\Controllers;
require_once(dirname(__FILE__) . "/../core/Controller.php");
require_once(dirname(__FILE__) . "/../model/ModelMessages.php");

use Application\Core\Controller;
use Application\Model\ModelMessages;

class ChatLoad extends Controller {
    private $modelMessages;

    private const OFFSET_SEC = 60;

    public function __construct() {
        $this->modelMessages = new ModelMessages();
    }

    public function loadMessages($params) {
        $gameId = $params[0];
        $playerCode = $params[1];


        //ПРОВЕРКА ЧЕРЕЗ game-status


        isset($_GET['lastTime']) ? $lastTime = (int)$_GET['lastTime'] : $lastTime = time();
        $messagesRaw = $this->modelMessages->loadMessages($gameId, $lastTime - self::OFFSET_SEC, $lastTime);
        $messagesPretty = array_map(function ($message) use ($playerCode) {
            return [
                'my' => $playerCode == $message['sender'],
                'time' => $message['time'],
                'message' => $message['message'],
            ];
        }, $messagesRaw);
        $messagesPretty = [
            'messages' => $messagesPretty,
            'lastTime' => $lastTime - self::OFFSET_SEC,
            'success' => True,
        ];
        print_r(json_encode($messagesPretty));
    }
}