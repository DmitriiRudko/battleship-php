<?php

namespace Application\Controllers;
require_once(dirname(__FILE__) . "/../core/Controller.php");
require_once(dirname(__FILE__) . "/../model/ModelGames.php");
require_once(dirname(__FILE__) . "/../model/ModelWarships.php");
require_once(dirname(__FILE__) . "/../helpers/FieldHelper.php");

use Application\Helpers\FieldHelper;
use Application\Helpers\JsonHelper;
use Application\Model\ModelGames;
use Application\Core\Controller;
use Application\Model\ModelWarships;

class PlaceShip extends Controller {
    private $modelWarships;

    public function __construct() {
        $this->modelWarships = new ModelWarships();
    }

    public function placeShip(array $params): void {
        $gameId = $params[0];
        $playerCode = $params[1];

        $gameInfo = $this->getGameInfo($gameId, $playerCode);
        if (!$gameInfo) {
            JsonHelper::successFalse('Wrong parameters');
            return;
        }
        if ($gameInfo['status'] != ModelGames::GAME_HAS_NOT_BEGUN_STATUS) {
            JsonHelper::successFalse('Game has already begun');
            return;
        }

        if (isset($_POST['ships'])) {
            $this->placeMany($gameId, $gameInfo['me']['id']);
        } elseif (isset($_POST['x'], $_POST['y'])) {
            $this->placeOne($gameId, $gameInfo['me']['id']);
        } else {
            $this->removeOne($gameId, $gameInfo['me']['id']);
        }
    }

    public function placeOne(int $gameId, int $playerId): void {
        $placedShips = $this->modelWarships->getPlayerWarships($gameId, $playerId);
        $field = new FieldHelper($placedShips);

        extract($_POST);
        $size = explode('-', $ship)[0];
        $number = explode('-', $ship)[1];

        if (!$field->isPossibleToPlace($size, $number, $orientation, $x, $y)) {
            JsonHelper::successFalse('Ship is impossible to place in this position');
        } else {
            $this->modelWarships->placeShip($gameId, $playerId, $size, $x, $y, $orientation, $number);
            JsonHelper::successTrue();
        }
    }

    public function placeMany(int $gameId, int $playerId): void {
        $placedShips = $this->modelWarships->getPlayerWarships($gameId, $playerId);
        $field = new FieldHelper($placedShips);
        $ships = json_decode($_POST['ships']);

        foreach ($ships as $ship) {
            $size = explode('-', $ship->shipType)[0];
            $number = explode('-', $ship->shipType)[1];
            if (!$field->isPossibleToPlace($size, $number, $ship->orientation, $ship->x, $ship->y)) {
                JsonHelper::successFalse('Some ships are impossible to place');
                return;
            } else {
                $field->placeShip($size, $number, $ship->orientation, $ship->x, $ship->y);
            }
        }

        foreach ($ships as $ship) {
            $this->modelWarships->placeShip($gameId, $playerId, $size, $ship->x, $ship->y, $ship->orientation, $number);
        }

        JsonHelper::successTrue();
    }

    public function removeOne(int $gameId, int $playerId): void {
        extract($_POST);
        $size = explode('-', $ship)[0];
        $number = explode('-', $ship)[1];
        $this->modelWarships->removeShip($gameId, $playerId, $size, $number);
        JsonHelper::successTrue();
    }
}