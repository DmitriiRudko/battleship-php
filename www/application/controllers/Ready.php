<?php

namespace Application\Controllers;
require_once(dirname(__FILE__) . "/../core/Controller.php");
require_once(dirname(__FILE__) . "/../model/ModelGames.php");

use Application\Helpers\JsonHelper;
use Application\Model\ModelGames;
use Application\Core\Controller;
use Application\Model\ModelUsers;
use http\Params;

class Ready extends Controller {
    private $modelUsers;

    private $modelGames;

    public function __construct() {
        $this->modelUsers = new ModelUsers();
        $this->modelGames = new ModelGames();
    }

    public function userReady($params) {
        $gameId = $params[0];
        $userCode = $params[1];
        $userId = $this->modelUsers->getUserId($userCode);
        if (!isset($userId)) {
            JsonHelper::successFalse();
            return;
        }

        $ids = $this->modelGames->getPlayersIds($gameId);
        if (!isset($ids)) {
            JsonHelper::successFalse();
            return;
        }

        ///ПРОВЕРИТЬ, РАССТАВИЛ ЛИ ПОЛЬЗОВАТЕЛЬ КОРАБЛИ

        $this->modelUsers->userReady($userId);

        switch ($userId) {
            case $ids['initiator_id']:
                $enemyStatus = $this->modelUsers->isReady($ids['invited_id']);
                break;
            case $ids['invited_id']:
                $enemyStatus = $this->modelUsers->isReady($ids['initiator_id']);
                break;
        }

        $response = [
            'enemyReady' => $enemyStatus,
            'success' => True,
        ];

        JsonHelper::jsonifyAndSend($response);
    }
}