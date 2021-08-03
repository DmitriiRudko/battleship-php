<?php

namespace Application\Controllers;
require_once(dirname(__FILE__) . "/../core/Controller.php");
require_once(dirname(__FILE__) . "/../model/ModelMessages.php");

use Application\Helpers\JsonHelper;
use Application\Core\Controller;
use Application\Model\ModelMessages;

class ChatSend extends Controller {
    private $modelMessages;

    public function __construct() {
        $this->modelMessages = new ModelMessages();
    }

    public function sendMessage(array $params): void {
        $gameId = $params[0];
        $playerCode = $params[1];
        $gameInfo = $this->getGameInfo($gameId, $playerCode);

        if (!$gameInfo) {
            JsonHelper::successFalse('Wrong parameters');
            return;
        }

        $message = mb_strimwidth($_POST['message'], 0, ModelMessages::MESSAGE_MAX_LEN);
        $this->modelMessages->sendMessage($gameId, $gameInfo['me']['id'], $message);

        JsonHelper::successTrue();
    }
}