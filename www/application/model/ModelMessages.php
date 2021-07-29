<?php

namespace Application\Model;
require_once(dirname(__FILE__) . "/../core/Model.php");

use Application\Core\Model;

class ModelMessages extends Model {
    public function loadMessages($gameId, $from, $to) {
        $sql = "SELECT `sender`, `message`, UNIX_TIMESTAMP(`time`) as time
                FROM `messages` 
                WHERE `game_id` = :gameId AND UNIX_TIMESTAMP(`time`) > :from
                AND UNIX_TIMESTAMP(`time`) <= :to
                ORDER BY `time` ASC";
        $params = [
            'gameId' => $gameId,
            'from' => $from,
            'to' => $to,
        ];
        $messages = $this->db->getMany($sql, $params);
        return $messages;
    }

    public function sendMessage($gameId, $sender, $message) {
        $sql = "INSERT INTO `messages`
                (`game_id`, `sender`, `message`)
                VALUES (:gameId, :sender, :message)";
        $params = [
            'gameId' => $gameId,
            'sender' => $sender,
            'message' => $message,
        ];
        $this->db->produceStatement($sql, $params);
    }
}