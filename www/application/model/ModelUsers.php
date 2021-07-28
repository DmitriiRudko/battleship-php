<?php

namespace Application\Model;
require_once(dirname(__FILE__) . "/../Core/Model.php");

use Application\Core\Model;

class ModelUsers extends Model {
    public function __construct() {
        parent::__construct();
    }

    public function newUser() {
        $userCode = uniqid();
        $sql = "INSERT INTO users (`code`) 
                VALUE (:code)";
        $params = [
            'code' => $userCode,
        ];
        $this->db->produceStatement($sql, $params);
        $result = [
            'id' => $this->db->lastInsertedId(),
            'code' => $userCode,
        ];
        return $result;
    }

    public function userReady($gameId, $userCode) {
        $sql = "SELECT  `users`
                SET `ready` = 1
                WHERE `code` = :code";


        $id = $this->getUserId($userCode);
        $sql = "UPDATE `users`
                SET `ready` = 1
                WHERE `code` = :code";
        $params = [
            'code' => $userCode,
        ];
        $this->db->produceStatement($sql, $params);
        print_r($this->db->lastInsertedId());
//        $sql = "SELECT `initiator_id`, `guest_id`
//                IF ()
//                FROM `games`
//                WHERE `id` = :gmaeId";
//        $params = [
//            'gameId' => $gameId,
//        ];
//        $playersIds = $this->db->getOne($sql, $params);
    }

    public function getUserId($userCode) {
        $sql = "SELECT `id` FROM `users` WHERE `code` = :code";
        $params = [
            'code' => $userCode,
        ];
        $id = $this->db->getOne($sql, $params);
        return $id;
    }
}