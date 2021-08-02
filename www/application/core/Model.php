<?php

namespace Application\Core;
require_once(dirname(__FILE__) . "/../core/Database.php");

use Application\Core\Database;


class Model {
    protected $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

}