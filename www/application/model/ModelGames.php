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

    public function getEnemy($gameId, $playerCode) {
        $gameInfo = $this->getGameInfo($gameId);
        switch ($playerCode) {
            case $gameInfo['invited']['id']:
                return $gameInfo['initiator'];
            case $gameInfo['initiator']['id']:
                return $gameInfo['invited'];
        }
    }

    public function whoIsNext($gameId) {
        $gameInfo = $this->getGameInfo($gameId);
        switch ($gameInfo['next']) {
            case $gameInfo['invited']['id']:
                return $gameInfo['invited'];
            case $gameInfo['initiator']['id']:
                return $gameInfo['initiator'];
        }
    }

    public function getInvited($gameId) {
        $gameInfo = $this->getGameInfo($gameId);
        return $gameInfo['invited'];
    }

    public function getGameInfo($gameId) {
        $sql = "SELECT *
                FROM `games`
                WHERE `id` = :id";
        $params = [
            'id' => $gameId,
        ];
        $gameInfo = $this->db->getOne($sql, $params);
        $sql = "SELECT *
                FROM `users`
                WHERE `id` = :initiatorId OR
                `id` = :invitedId";
        $params = [
            'initiatorId' => $gameInfo['initiator_id'],
            'invitedId' => $gameInfo['invited_id'],
        ];
        $usersInfo = $this->db->getMany($sql, $params);
        if ($gameInfo['initiator_id'] == $usersInfo[0]['id']) {
            $gameInfo = array_merge($gameInfo, [
                'initiator' => $usersInfo[0],
                'invited' => $usersInfo[1]
            ]);
        } else {
            $gameInfo = array_merge($gameInfo, [
                'initiator' => $usersInfo[1],
                'invited' => $usersInfo[0]
            ]);
        }
        unset($gameInfo['initiator_id'], $gameInfo['invited_id']);
        return $gameInfo;
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