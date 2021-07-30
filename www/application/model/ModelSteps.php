<?php

namespace Application\Model;
require_once(dirname(__FILE__) . "/../Core/Model.php");

use Application\Core\Model;

class ModelSteps extends Model {
    public function getPlayerSteps($gameId, $playerCode) {
        $sql = "SELECT `x`, `y`
                FROM `steps`
                WHERE `game_id` = :id";
        $params = [
            'id' => $gameId,
        ];
        $steps = $this->db->getMany($sql, $params);
        return $steps;
    }

    public function shoot($gameId, $playerCode, $x, $y) {
        $sql = "INSERT INTO 
                `steps` (`player`, `game_id`, `x`, `y`)
                VALUES (:code, :id, :x, :y)";
        $params = [
            'id' => $gameId,
            'code' => $playerCode,
            'x' => $x,
            'y' => $y,
        ];
        $this->db->produceStatement($sql, $params);
    }
}