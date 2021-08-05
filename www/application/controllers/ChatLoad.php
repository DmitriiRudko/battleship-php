<?php

namespace Application\Controllers;
require_once(dirname(__FILE__) . "/../core/Controller.php");
require_once(dirname(__FILE__) . "/../model/ModelMessages.php");
require_once(dirname(__FILE__) . "/../helpers/JsonHelper.php");

use Application\Core\Controller;
use Application\Model\ModelMessages;
use Application\Helpers\JsonHelper;

class ChatLoad extends Controller {
    private $modelMessages;

    public function __construct() {
        $this->modelMessages = new ModelMessages();
    }

    public function loadMessages(array $params): void {
        $gameId = $params[0];
        $playerCode = $params[1];

        if (!$this->getGameInfo($gameId, $playerCode)) {
            JsonHelper::successFalse('Wrong parameters');
            return;
        }

        $lastTime = isset($_GET['lastTime']) ? (int)$_GET['lastTime'] : time();
        $messagesRaw = $this->modelMessages->loadMessages($gameId,
            date("Y-m-d H:m:s", $lastTime - ModelMessages::OFFSET_SEC), date("Y-m-d H:m:s", $lastTime));
        $messagesPretty = array_map(function ($message) use ($playerCode) {
            return [
                'my' => $playerCode == $message['sender'],
                'time' => $message['time'],
                'message' => $message['message'],
            ];
        }, $messagesRaw);

        $messagesPretty = [
            'messages' => $messagesPretty,
            'lastTime' => $lastTime - ModelMessages::OFFSET_SEC,
            'success' => true,
        ];

        JsonHelper::jsonifyAndSend($messagesPretty);
    }
}