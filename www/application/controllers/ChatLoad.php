<?php

namespace Application\Controllers;
require_once(dirname(__FILE__) . "/../core/Controller.php");
require_once(dirname(__FILE__) . "/../model/ModelMessages.php");
require_once(dirname(__FILE__) . "/../model/ModelUsers.php");
require_once(dirname(__FILE__) . "/../helpers/JsonHelper.php");

use Application\Core\Controller;
use Application\Model\ModelMessages;
use Application\Helpers\JsonHelper;
use Application\Model\ModelUsers;

class ChatLoad extends Controller {
    private $modelMessages;

    private $modelUsers;

    public function __construct() {
        $this->modelMessages = new ModelMessages();
        $this->modelUsers = new ModelUsers();
    }

    public function loadMessages(array $params): void {
        $timeNow = time();
        $gameId = $params[0];
        $playerCode = $params[1];
        $playerId = $this->modelUsers->getUserId($playerCode);

        if (!$this->getGameInfo($gameId, $playerCode)) {
            JsonHelper::successFalse('Wrong parameters');
            return;
        }

        $timestamp = isset($_GET['lastTime']) ? (int)$_GET['lastTime'] : time();
        $messagesRaw = $this->modelMessages->loadMessages($gameId, date("Y-m-d H:m:s", $timestamp));

        $messagesPretty = array_map(function ($message) use ($playerCode, $playerId) {
            return [
                'my' => $playerId === $message['user_id'],
                'time' => $message['time'],
                'message' => $message['message'],
            ];
        }, $messagesRaw);

        $messagesPretty = [
            'messages' => $messagesPretty,
            'lastTime' => $timeNow,
            'success' => true,
        ];

        JsonHelper::jsonifyAndSend($messagesPretty);
    }
}