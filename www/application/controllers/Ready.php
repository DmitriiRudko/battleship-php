<?php

namespace Application\Controllers;
require_once(dirname(__FILE__) . "/../core/Controller.php.php");
require_once(dirname(__FILE__) . "/../model/ModelGames.php");

use Application\Model\ModelGames;
use Application\Core\Controller;
use Application\Model\ModelUsers;

class Ready extends Controller {
    private $modelUsers;

    public function __construct() {
        $this->modelUsers = new ModelUsers();
    }

    public function userReady($gameId, $userCode) {
        $this->modelUsers->userReady($gameId, $userCode);
    }
}