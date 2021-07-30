<?php

namespace Application\Controllers;
require_once(dirname(__FILE__) . "/../core/Controller.php");
require_once(dirname(__FILE__) . "/../model/ModelGames.php");

use Application\Helpers\JsonHelper;
use Application\Model\ModelGames;
use Application\Core\Controller;
use Application\Model\ModelWarships;

class ClearField extends Controller {
    private $modelWarships;

    public function __construct() {
        $this->modelWarships = new ModelWarships();
    }

    public function removeAll($params) {
        $gameId = $params[0];
        $playerCode = $params[1];
        //ПРОВЕРКА!!!
        $this->modelWarships->clearField($gameId, $playerCode);
        JsonHelper::successTrue();
    }
}