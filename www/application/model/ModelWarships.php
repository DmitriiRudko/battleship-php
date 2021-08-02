<?php

namespace Application\Model;
require_once(dirname(__FILE__) . "/../Core/Model.php");

use Application\Core\Model;
use Application\Helpers\JsonHelper;

class ModelWarships extends Model {
    public function getPlayerWarships($gameId, $playerCode) {
        $sql = "SELECT `number`, `size`, `x`, `y`, `orientation`
                FROM `warships`
                WHERE `game_id` = :gameId AND `player` = :playerCode";
        $params = [
            'gameId' => $gameId,
            'playerCode' => $playerCode,
        ];
        $warships = $this->db->getMany($sql, $params);
        return $warships;
    }

    public function placeShip($gameId, $playerCode, $size, $x, $y, $orientation, $number) {
        $sql = "INSERT INTO `warships` (`game_id`, `player`, `size`, `x`, `y`, `orientation`, `number`)
                VALUES (:game_id, :playerCode, :size, :x, :y, :orientation, :number)";
        $params = [
            'game_id' => $gameId,
            'playerCode' => $playerCode,
            'size' => $size,
            'x' => $x,
            'y' => $y,
            'orientation' => $orientation,
            'number' => $number,
        ];
        $this->db->produceStatement($sql, $params);
    }

    public function removeShip($gameId, $playerCode, $size, $number) {
        $sql = "DELETE FROM `warships` WHERE 
                (`game_id` = :gameId AND `player` = :playerCode AND 
                 `size` = :size AND `number` = :number)";
        $params = [
            'gameId' => $gameId,
            'playerCode' => $playerCode,
            'size' => $size,
            'number' => $number,
        ];
        $this->db->produceStatement($sql, $params);
    }

    public function clearField($gameId, $playerCode) {
        $sql = "DELETE FROM `warships` WHERE 
                (`game_id` = :gameId AND `player` = :playerCode)";
        $params = [
            'gameId' => $gameId,
            'playerCode' => $playerCode,
        ];
        $this->db->produceStatement($sql, $params);
    }
}