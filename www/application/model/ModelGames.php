<?php

namespace Application\Model;
require_once(dirname(__FILE__) . "/../Core/Model.php");

use Application\Core\Model;

class ModelGames extends Model {
    public function __construct() {
        parent::__construct();
    }

    public function newGame($initiatorId, $invitedId) {
        $sql = "INSERT INTO games (`initiator_id`, `invited_id`, `turn`)
                VALUES (:initiator, :invited, :turn)";
        $turn = random_int(0, 1);
        $turn = [$initiatorId, $invitedId][$turn];
        $params = [
            'initiator' => $initiatorId,
            'invited' => $invitedId,
            'turn' => $turn,
        ];
        $this->db->produceStatement($sql, $params);
        $result = [
            'id' => $this->db->lastInsertedId(),
        ];
        return $result;
    }

    public function getPlayersIds($gameId) {
        $sql = "SELECT `initiator_id`, `invited_id`
                FROM `games`
                WHERE `id` = :id";
        $params = [
            'id' => $gameId,
        ];
        $result = $this->db->getOne($sql, $params);
        return $result;
    }

    public function getGameStatus($gameId) {
        $sql = "SELECT `status`
                FROM `games`
                WHERE `id` = :id";
        $params = [
            'id' => $gameId,
        ];
        $result = $this->db->getOne($sql, $params);
        return $result['status'];
    }

    public function setGameStatus($gameId, $status) {
        $sql = "UPDATE `game`
                SET `status` = :status
                WHERE `id` = :id";
        $params = [
            'id' => $gameId,
            'status' => $status,
        ];
        $this->db->produceStatement($sql, $params);
    }

    public function getEnemy($gameId, $playerCode) {
        $sql = "SELECT `id`, `code` 
                FROM `users` 
                WHERE `code` = :code;";
        $params = [
            'code' => $playerCode,
        ];
        $player = $this->db->getOne($sql, $params);

        $sql = "SELECT CASE 
                WHEN  `initiator_id` = :id_ THEN `invited_id` 
                WHEN  `invited_id` = :id THEN `initiator_id` 
                END AS `enemy_id`
                FROM `games` 
                WHERE `id` = :gameId;";
        $params = [
            'id_' => $player['id'],  // PDO - самая тупая хуйня из всех, что только создовал homo sapiens.
            'id' => $player['id'],  // используя один параметр дважды (73, 74) мы вызываем ошибку wrong parameter number
            'gameId' => $gameId,  // можно, конечно, включить режим эмуляции, но тогда придется вручную привязывать все
        ];                        // не строковые переменные через bind, так что, ну его нахуй.
        $enemyId = $this->db->getOne($sql, $params)['enemy_id'];
        $sql = "SELECT `id`, `code` 
                FROM `users` 
                WHERE `id` = :id;";
        $params = [
            'id' => $enemyId,
        ];
        $enemy = $this->db->getOne($sql, $params);
        return $enemy;
    }

    public function whoIsNext($gameId) {
        $sql = "SELECT `turn` 
                FROM `games` 
                WHERE `id` = :gameId;";
        $params = [
            'gameId' => $gameId,
        ];
        $playerId = $this->db->getOne($sql, $params)['turn'];

        $sql = "SELECT `id`, `code` 
                FROM `users` 
                WHERE `id` = :id;";
        $params = [
            'id' => $playerId,
        ];
        $playerInfo = $this->db->getOne($sql, $params);
        return $playerInfo;
    }

    public function enemysTurn($gameId) {
        $sql = "UPDATE `games` SET `turn` = CASE 
                WHEN `turn` = `initiator_id` THEN `invited_id` 
                WHEN `turn` = `invited_id` THEN `initiator_id`
                END WHERE `id` = :gameId;";
        $params = [
            'gameId' => $gameId,
        ];
        $this->db->produceStatement($sql, $params)['turn'];
    }
}