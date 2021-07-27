<?php

namespace Application\Model;
require_once(dirname(__FILE__) . "/../Core/Model.php");

use Application\Core\Model;

class ModelGames extends Model {
    public function __construct() {
        parent::__construct();
    }

    public function newGame() {  // НИХУЯ НЕ ПОНЯТНО ЧТО С КОДАМИ
        $user = $this->newUser();
        $sql = "INSERT INTO games (`initiator_id`) 
                VALUE (:initiator)";
        $params = [
            'initiator' => $user['id'],
        ];
         $this->db->produceStatement($sql, $params);
    }
}