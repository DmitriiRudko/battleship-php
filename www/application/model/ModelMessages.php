<?php

namespace Application\Model;
require_once(dirname(__FILE__) . "/../core/Model.php");

use Application\Core\Model;

class ModelMessages extends Model {
    public const MESSAGE_MAX_LEN = 250;

    public function loadMessages(int $gameId, string $timestamp): array {
        $sql = "SELECT * 
                FROM `messages` 
                WHERE `game_id` = :gameId AND `time` > :timestamp
                ORDER BY `time` ASC";
        $params = [
            'gameId' => $gameId,
            'timestamp' => $timestamp,
        ];

        $messages = $this->db->getMany($sql, $params);

        return $messages;
    }

    public function sendMessage(int $gameId, int $senderId, string $message): void {
        $sql = "INSERT INTO `messages`
                (`game_id`, `user_id`, `message`)
                VALUES (:gameId, :sender, :message)";
        $params = [
            'gameId' => $gameId,
            'sender' => $senderId,
            'message' => $message,
        ];

        $this->db->produceStatement($sql, $params);
    }
}