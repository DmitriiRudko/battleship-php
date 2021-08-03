<?php

namespace Application\Controllers;
require_once(dirname(__FILE__) . "/../core/Controller.php");
require_once(dirname(__FILE__) . "/../helpers/JsonHelper.php");
require_once(dirname(__FILE__) . "/../helpers/FieldHelper.php");
require_once(dirname(__FILE__) . "/../model/ModelGames.php");
require_once(dirname(__FILE__) . "/../model/ModelWarships.php");
require_once(dirname(__FILE__) . "/../model/ModelSteps.php");

use Application\Core\Controller;
use Application\Helpers\JsonHelper;
use Application\Helpers\FieldHelper;
use Application\Model\ModelGames;
use Application\Model\ModelWarships;
use Application\Model\ModelSteps;

class Shot extends Controller {
    private $modelGames;

    private $modelWarships;

    private $modelSteps;

    public function __construct() {
        $this->modelGames = new ModelGames();
        $this->modelWarships = new ModelWarships();
        $this->modelSteps = new ModelSteps();
    }

    public function shot(array $params): void {
        $gameId = $params[0];
        $playerCode = $params[1];

        $gameInfo = $this->getGameInfo($gameId, $playerCode);

        if ($gameInfo['status'] != ModelGames::GAME_HAS_BEGUN_STATUS) {
            switch ($gameInfo['status']) {
                case ModelGames::GAME_HAS_NOT_BEGUN_STATUS:
                    JsonHelper::successFalse('Game has not begun yet');
                case ModelGames::GAME_OVER_STATUS:
                    JsonHelper::successFalse('Game is already over');
                default:
                    JsonHelper::successFalse();
            }
            return;
        }

        if ($gameInfo['turn'] != $gameInfo['me']['id']) {
            JsonHelper::successFalse('This is not your turn');
            return;
        }

        $steps = $this->modelSteps->getPlayerSteps($gameId, $gameInfo['me']['id']);
        $enemy = $this->modelGames->getEnemy($gameId, $gameInfo['me']['code']);
        $warships = $this->modelWarships->getPlayerWarships($gameId, $enemy['id']);
        $field = new FieldHelper($warships, $steps);
        extract($_POST);
        if (!$field->isPossibleToShoot($y, $x)) {
            JsonHelper::successFalse('You have already shot here');
            return;
        }

        $result = $field->shoot($y, $x);
        $this->modelSteps->shoot($gameId, $gameInfo['me']['id'], $y, $x);

        if ($result) {
            if ($field->isOver()) {
                $this->modelGames->setGameStatus($gameId, ModelGames::GAME_OVER_STATUS);
            }
        } else {
            $this->modelGames->enemysTurn($gameId);
        }
        JsonHelper::successTrue();
    }
}