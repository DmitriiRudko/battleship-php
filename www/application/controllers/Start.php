<?php

namespace Application\Controllers;
require_once(dirname(__FILE__) . "/../core/Controller.php");
require_once(dirname(__FILE__) . "/../model/ModelGames.php");
require_once(dirname(__FILE__) . "/../model/ModelUsers.php");


use Application\Model\ModelUsers;
use Application\Model\ModelGames;
use Application\Core\Controller;

class Start extends Controller {
    private $modelGames;
    private $modelUsers;

    public function __construct() {
        $this->modelGames = new ModelGames();
        $this->modelUsers = new ModelUsers();
    }

    public function startGame() {
        $user = $this->modelUsers->newUser();
        $game = $this->modelGames->newGame($user['id']);
        $result = array_merge($game, ['code' => $user['code']]);
        print_r($result);
        return $result;
    }
}