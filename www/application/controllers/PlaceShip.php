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

    private $modelGames;

    public function __construct() {
        $this->modelWarships = new ModelWarships();
        $this->modelGames = new ModelGames();
    }

    public function placeShip($params) {
        $gameId = $params[0];
        $playerCode = $params[1];
        if ($this->modelGames->getGameStatus($gameId) != 1) return;

        /* ЕБАНУТЬ ПРОВЕРКУ КОРРЕКТНОСТИ ВХОДНЫХ ДАННЫХ ЧЕРЕЗ game-status */

        if (isset($_POST['ships'])) {
            $this->placeMany($gameId, $playerCode);
        } elseif (isset($_POST['x'], $_POST['y'])) {
            $this->placeOne($gameId, $playerCode);
        } else {
            $this->removeOne($gameId, $playerCode);
        }
    }

    public function placeOne($gameId, $playerCode) {
        $placedShips = $this->modelWarships->getPlayerWarships($gameId, $playerCode);
        $field = new FieldHelper($placedShips);

        extract($_POST);
        $size = explode('-', $ship)[0];
        $number = explode('-', $ship)[1];

        if (!$field->isPossibleToPlace($size, $number, $orientation, $x, $y)) {
            JsonHelper::successFalse();
        } else {
            $this->modelWarships->placeShip($gameId, $playerCode, $size, $x, $y, $orientation, $number);
            JsonHelper::successTrue();
        }
    }

    public function placeMany($gameId, $playerCode) {
        $placedShips = $this->modelWarships->getPlayerWarships($gameId, $playerCode);
        $field = new FieldHelper($placedShips);
        $ships = json_decode($_POST['ships']);
        foreach ($ships as $ship) {
            if (!$field->isPossibleToPlace($ship->size, $ship->number, $ship->orientation, $ship->x, $ship->y)) {
                JsonHelper::successFalse();
                return;
            } else {
                $field->placeShip($ship->size, $ship->number, $ship->orientation, $ship->x, $ship->y);
            }
        }
        foreach ($ships as $ship) {
            $this->modelWarships->placeShip($gameId, $playerCode, $ship->size, $ship->x, $ship->y, $ship->orientation, $ship->number);
        }
        JsonHelper::successTrue();
    }

    public function removeOne($gameId, $playerCode) {
        extract($_POST);
        $size = explode('-', $ship)[0];
        $number = explode('-', $ship)[1];
        $this->modelWarships->removeShip($gameId, $playerCode, $size, $number);
        JsonHelper::successTrue();
    }
}