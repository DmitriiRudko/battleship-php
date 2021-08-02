<?php

namespace Application\Model;
require_once(dirname(__FILE__) . "/../сore/Model.php");

use Application\Core\Model;

class ModelSteps extends Model {
    public function getPlayerSteps(int $gameId, int $playerId): array {
        $sql = "SELECT `x`, `y`
                FROM `steps`
                WHERE `game_id` = :id AND `user_id` = :playerId";
        $params = [
            'id' => $gameId,
            'playerId' => $playerId,
        ];
        $steps = $this->db->getMany($sql, $params);
        return $steps;
    }

    public function shoot(int $gameId, int $playerId, int $x, int $y): void {
        $sql = "INSERT INTO 
                `steps` (`user_id`, `game_id`, `x`, `y`)
                VALUES (:playerId, :gameId, :x, :y)";
        $params = [
            'gameId' => $gameId,
            'playerId' => $playerId,
            'x' => $x,
            'y' => $y,
        ];
        $this->db->produceStatement($sql, $params);
    }
}