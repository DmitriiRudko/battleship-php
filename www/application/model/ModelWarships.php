<?php

namespace Application\Model;
require_once(dirname(__FILE__) . "/../Core/Model.php");

use Application\Core\Model;

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

    public function placeShip($gameId, $playersCode){

    }
}