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
}