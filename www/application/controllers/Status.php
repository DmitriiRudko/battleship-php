<?php

namespace Application\Controllers;
require_once(dirname(__FILE__) . "/../core/Controller.php");
require_once(dirname(__FILE__) . "/../model/ModelGames.php");
require_once(dirname(__FILE__) . "/../model/ModelUsers.php");
require_once(dirname(__FILE__) . "/../model/ModelWarships.php");
require_once(dirname(__FILE__) . "/../model/ModelSteps.php");
require_once(dirname(__FILE__) . "/../helpers/FieldHelper.php");

use Application\Helpers\JsonHelper;
use Application\Model\ModelGames;
use Application\Core\Controller;
use Application\Model\ModelUsers;
use Application\Model\ModelWarships;
use Application\Model\ModelSteps;
use Application\Helpers\FieldHelper;

class Status extends Controller {
    private $modelGames;

    private $modelUsers;

    private $modelWarships;

    private $modelSteps;

    private $fieldHelper;

    public function __construct() {
        $this->modelGames = new ModelGames();
        $this->modelUsers = new ModelUsers();
        $this->fieldHelper = new FieldHelper();
        $this->modelWarships = new ModelWarships();
        $this->modelSteps = new ModelSteps();

    }

    public function gameInfo(array $params): void {
        $gameId = $params[0];
        $playerCode = $params[1];
        $gameInfo = $this->getGameInfo($gameId, $playerCode);
        if (!$gameInfo) {
            JsonHelper::successFalse('Wrong parameters');
            return;
        }

        $info = [
            'game' => [
                'id' => $gameId,
                'status' => $gameInfo['status'],
                'invite' => $gameInfo['invited']['code'],
                'myTurn' => $gameInfo['me']['id'] == $gameInfo['turn'],
            ]
        ];
        if ($info['game']['status'] === ModelGames::GAME_HAS_NOT_BEGUN_STATUS)
            $info['game'] = array_merge($info['game'], [
                'meReady' => $this->modelUsers->isReady($this->modelUsers->getUserId($playerCode)),
            ]);
        if (!isset($params['short'])) {
            $enemy = $this->modelGames->getEnemy($gameId, $playerCode);
            $myShips = $this->modelWarships->getPlayerWarships($gameId, $gameInfo['me']['id']);
            $enemyShips = $this->modelWarships->getPlayerWarships($gameId, $enemy['id']);
            $mySteps = $this->modelSteps->getPlayerSteps($gameId, $gameInfo['me']['id']);
            $enemySteps = $this->modelSteps->getPlayerSteps($gameId, $enemy['id']);
            $usedPlaces = array_map(function ($ship) {
                return $ship['size'] . '-' . $ship['number'];
            }, $myShips);
            $fieldsInfo = $this->fieldHelper::getFieldsInfo($myShips, $enemyShips, $mySteps, $enemySteps);
            $info['fieldMy'] = self::transpose($info['fieldMy']);
            $info['fieldEnemy'] = self::transpose($info['fieldEnemy']);
            $info = array_merge($info, $fieldsInfo);
            $info = array_merge($info, ['usedPlaces' => $usedPlaces,]);
        }
        $info = array_merge_recursive($info, [
            'success' => true,
        ]);

        JsonHelper::jsonifyAndSend($info);
    }

    private static function transpose($array) {
        array_unshift($array, null);
        return call_user_func_array('array_map', $array);
    }
}