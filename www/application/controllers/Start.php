<?php

namespace Application\Controllers;
require_once(dirname(__FILE__) . "/../core/Controller.php");
require_once(dirname(__FILE__) . "/../model/ModelGames.php");
require_once(dirname(__FILE__) . "/../model/ModelUsers.php");
require_once(dirname(__FILE__) . "/../helpers/JsonHelper.php");

use Application\Model\ModelUsers;
use Application\Model\ModelGames;
use Application\Core\Controller;
use Application\Helpers\JsonHelper;

class Start extends Controller {
    private $modelGames;
    private $modelUsers;

    public function __construct() {
        $this->modelGames = new ModelGames();
        $this->modelUsers = new ModelUsers();
    }

    public function startGame() {
        $initiator = $this->modelUsers->newUser();
        $invited = $this->modelUsers->newUser();
        $game = $this->modelGames->newGame($initiator['id'], $invited['id']);
        $result = array_merge($game, [
            'code' => $initiator['code'],
            'invite' => $invited['code'],
            'success' => True,
        ]);
        JsonHelper::jsonifyAndSend($result);
    }
}