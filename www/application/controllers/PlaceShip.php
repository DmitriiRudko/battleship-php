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
        $ships = $this->modelWarships->getPlayerWarships($gameId, $playerCode);
        $field = new FieldHelper($ships);

        extract($_POST);
        $size = explode('-', $ship)[0];
        $number = explode('-', $ship)[1];


        if (!$field->isPossibleToPlace($size, $number, $orientation, $x, $y)) {
            JsonHelper::successFalse();
        } else {
            $this->modelWarships->placeShip($gameId, $playerCode, $size, x, $y, $orientation, $number);
            JsonHelper::successTrue();
        }
    }
}