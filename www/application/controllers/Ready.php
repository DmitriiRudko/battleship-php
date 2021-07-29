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

    private const SHIPS_AMOUNT = 10;

    private const GAME_HAS_BEGUN_STATUS = 2;

    public function __construct() {
        $this->modelUsers = new ModelUsers();
        $this->modelGames = new ModelGames();
        $this->modelWarships = new ModelWarships();
    }

    public function userReady($params) {
        $gameId = $params[0];
        $playerCode = $params[1];
        $userId = $this->modelUsers->getUserId($playerCode);
        if (!isset($userId)) {
            JsonHelper::successFalse();
            return;
        }

        $ids = $this->modelGames->getPlayersIds($gameId);
        if (!isset($ids)) {
            JsonHelper::successFalse();
            return;
        }

        $playerShips = $this->modelWarships->getPlayerWarships($gameId, $playerCode);
        if (count($playerShips) != self::SHIPS_AMOUNT) {
            JsonHelper::successFalse();
            return;
        }

        $this->modelUsers->userReady($userId);

        switch ($userId) {
            case $ids['initiator_id']:
                $enemyStatus = $this->modelUsers->isReady($ids['invited_id']);
                break;
            case $ids['invited_id']:
                $enemyStatus = $this->modelUsers->isReady($ids['initiator_id']);
                break;
        }

        if($enemyStatus){
            $this->modelGames->setGameStatus($gameId, self::GAME_HAS_BEGUN_STATUS);
        }

        $response = [
            'enemyReady' => $enemyStatus,
            'success' => True,
        ];

        JsonHelper::jsonifyAndSend($response);
    }
}