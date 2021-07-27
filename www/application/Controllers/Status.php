<?php

namespace Application\Controllers;
require_once(dirname(__FILE__) . "/../core/Controller_.php.php");
require_once(dirname(__FILE__) . "/../model/ModelGames.php");

use Application\Model\ModelGames;
use Application\Core\Controller;

class Status extends Controller {
    private $modelGames;

    public function __construct() {
        $this->modelGames = new ModelGames();
    }

    public function startGame() {

    }
}