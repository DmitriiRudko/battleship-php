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

    private const GAME_HAS_NOT_BEGUN_STATUS = 1;

    private const GAME_HAS_BEGUN_STATUS = 2;

    private const GAME_OVER_STATUS = 3;

    public function __construct() {
        $this->modelGames = new ModelGames();
        $this->modelWarships = new ModelWarships();
        $this->modelSteps = new ModelSteps();
    }

    public function shot($params) {
        $gameId = $params[0];
        $playerCode = $params[1];

        $gameInfo = $this->getGameInfo($gameId, $playerCode);

        if ($gameInfo['status'] != self::GAME_HAS_BEGUN_STATUS) {
            switch ($gameInfo['status']) {
                case self::GAME_HAS_NOT_BEGUN_STATUS:
                    JsonHelper::successFalse('Game has not begun yet');
                    return;
                case self::GAME_OVER_STATUS:
                    JsonHelper::successFalse('Game is already over');
                    return;
            }
        }

        $nextWhoGoes = $this->modelGames->whoIsNext($gameId);
        if ($nextWhoGoes['code'] != $playerCode) {
            JsonHelper::successFalse('This is not your turn');
            return;
        }

        $steps = $this->modelSteps->getPlayerSteps($gameId, $playerCode);
        $enemy = $this->modelGames->getEnemy($gameId, $playerCode);
        $warships = $this->modelWarships->getPlayerWarships($gameId, $enemy['code']);
        $field = new FieldHelper($warships, $steps);
        extract($_POST);
        if (!$field->isPossibleToShoot($x, $y)) {
            JsonHelper::successFalse('You have already shot here');
            return;
        }

        $result = $field->shoot($x, $y);
        $this->modelSteps->shoot($gameId, $playerCode, $x, $y);

        if ($result) {
            if ($field->isOver()) {
                $this->modelGames->setGameStatus($gameId, self::GAME_OVER_STATUS);
            }
        } else {
            $this->modelGames->enemysTurn($gameId);
        }
        JsonHelper::successTrue();
    }
}