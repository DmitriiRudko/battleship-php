<?php

namespace Application\Controllers;
require_once(dirname(__FILE__) . "/../core/Controller.php");
require_once(dirname(__FILE__) . "/../model/ModelMessages.php");

use Application\Helpers\JsonHelper;
use Application\Model\ModelGames;
use Application\Core\Controller;
use Application\Model\ModelMessages;

class ChatSend extends Controller {
    private $modelMessages;

    private const MESSAGE_MAX_LEN = 250;

    public function __construct() {
        $this->modelMessages = new ModelMessages();
    }

    public function sendMessage($params) {
        $gameId = $params[0];
        $playerCode = $params[1];
        if (!$this->getGameInfo($gameId, $playerCode)) {
            JsonHelper::successFalse('Wrong parameters');
            return;
        }
        $message = mb_strimwidth($_POST['message'], 0, self::MESSAGE_MAX_LEN);
        $this->modelMessages->sendMessage($gameId, $playerCode, $message);
        JsonHelper::successTrue();
    }
}