<?php

namespace Application\Model;
require_once(dirname(__FILE__) . "/../Core/Model.php");

use Application\Core\Model;

class ModelGames extends Model {
    public function __construct() {
        parent::__construct();
    }

    public function newGame($initiatorId) { 
        $invite = uniqid();
        $sql = "INSERT INTO games (`initiator_id`, `invite`) 
                VALUE (:initiator, :invite)";
        $params = [
            'initiator' => $initiatorId,
            'invite' => $invite,
        ];
        $this->db->produceStatement($sql, $params);
        $result = [
            'id' => $this->db->lastInsertedId(),
            'invite' => $invite,
        ];
        return $result;
    }
}