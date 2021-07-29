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

    public function userReady($userId) {
        $sql = "UPDATE `users`
                SET `ready` = 1
                WHERE `id` = :id";
        $params = [
            'id' => $userId,
        ];
        $result = $this->db->produceStatement($sql, $params);
    }

    public function getUserId($userCode) {
        $sql = "SELECT `id` 
                FROM `users` 
                WHERE `code` = :code";
        $params = [
            'code' => $userCode,
        ];
        $id = $this->db->getOne($sql, $params);
        return $id['id'];
    }

    public function isReady($userId) {
        $sql = "SELECT `ready` 
                FROM `users` 
                WHERE `id` = :id";
        $params = [
            'id' => $userId,
        ];
        $status = $this->db->getOne($sql, $params);
        return boolval($status['ready']);
    }
}