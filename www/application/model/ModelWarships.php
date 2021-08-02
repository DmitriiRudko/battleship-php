<?php

namespace Application\Model;
require_once(dirname(__FILE__) . "/../core/Model.php");

use Application\Core\Model;

class ModelWarships extends Model {
    public const SHIPS_AMOUNT = 10;

    public function getPlayerWarships(int $gameId, int $playerId): array {
        $sql = "SELECT `number`, `size`, `x`, `y`, `orientation`
                FROM `warships`
                WHERE `game_id` = :gameId AND `user_id` = :userId";
        $params = [
            'gameId' => $gameId,
            'userId' => $playerId,
        ];
        $warships = $this->db->getMany($sql, $params);
        return $warships;
    }

    public function placeShip(int $gameId, int $playerId, int $size, int $x, int $y, string $orientation, int $number): void {
        $sql = "INSERT INTO `warships` (`game_id`, `user_id`, `size`, `x`, `y`, `orientation`, `number`)
                VALUES (:gameId, :playerId, :size, :x, :y, :orientation, :number)";
        $params = [
            'gameId' => $gameId,
            'playerId' => $playerId,
            'size' => $size,
            'x' => $x,
            'y' => $y,
            'orientation' => $orientation,
            'number' => $number,
        ];
        $this->db->produceStatement($sql, $params);
    }

    public function removeShip(int $gameId, int $playerId, int $size, int $number): void {
        $sql = "DELETE FROM `warships` WHERE 
                (`game_id` = :gameId AND `user_id` = :playerId AND 
                 `size` = :size AND `number` = :number)";
        $params = [
            'gameId' => $gameId,
            'playerId' => $playerId,
            'size' => $size,
            'number' => $number,
        ];
        $this->db->produceStatement($sql, $params);
    }

    public function clearField(int $gameId, int $playerId): void {
        $sql = "DELETE FROM `warships` WHERE 
                (`game_id` = :gameId AND `user_id` = :playerId)";
        $params = [
            'gameId' => $gameId,
            'playerId' => $playerId,
        ];
        $this->db->produceStatement($sql, $params);
    }
}