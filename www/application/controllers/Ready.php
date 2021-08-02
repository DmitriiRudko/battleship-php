<?php

namespace Application\Controllers;
require_once(dirname(__FILE__) . "/../helpers/JsonHelper.php");
require_once(dirname(__FILE__) . "/../model/ModelGames.php");
require_once(dirname(__FILE__) . "/../core/Controller.php");
require_once(dirname(__FILE__) . "/../model/ModelUsers.php");
require_once(dirname(__FILE__) . "/../model/ModelWarships.php");

use Application\Helpers\JsonHelper;
use Application\Model\ModelGames;
use Application\Core\Controller;
use Application\Model\ModelUsers;
use Application\Model\ModelWarships;

class Ready extends Controller {
    private $modelUsers;

    private $modelGames;

    private $modelWarships;

    public function __construct() {
        $this->modelUsers = new ModelUsers();
        $this->modelGames = new ModelGames();
        $this->modelWarships = new ModelWarships();
    }

    public function setUserReady(array $params): void {
        $gameId = $params[0];
        $playerCode = $params[1];
        $gameInfo = $this->getGameInfo($gameId, $playerCode);

        if (!$gameInfo) {
            JsonHelper::successFalse('Wrong parameters');
            return;
        }
        $playerShips = $this->modelWarships->getPlayerWarships($gameId, $gameInfo['me']['id']);
        if (count($playerShips) != ModelWarships::SHIPS_AMOUNT) {
            JsonHelper::successFalse('You have not placed all the ships yet');
            return;
        }

        $enemyStatus = null;
        switch ($playerCode) {
            case $gameInfo['initiator']['code']:
                $userId = $gameInfo['initiator']['id'];
                $this->modelUsers->setUserReady($userId);
                $enemyStatus = $gameInfo['invited']['ready'];
                break;
            case $gameInfo['invited']['code']:
                $userId = $gameInfo['invited']['id'];
                $this->modelUsers->setUserReady($userId);
                $enemyStatus = $gameInfo['initiator']['ready'];
                break;
            default:
                JsonHelper::successFalse();
                return;
        }

        if ($enemyStatus) {
            $this->modelGames->setGameStatus($gameId, ModelGames::GAME_HAS_BEGUN_STATUS);
        }

        $response = [
            'enemyReady' => (bool)$enemyStatus,
            'success' => true,
        ];

        JsonHelper::jsonifyAndSend($response);
    }
}