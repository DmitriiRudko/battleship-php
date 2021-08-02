<?php

namespace Application\Core;
require_once(dirname(__FILE__) . "/../model/ModelGames.php");

use Application\Core\Database;
use Application\Model\ModelGames;
use http\Exception;

class Controller {
    public function getGameInfo($gameId, $playerCode) {
        $model = new ModelGames();
        $gameInfo = $model->getGameInfo($gameId);
        if (!$gameInfo) return null;
        if ($gameInfo['invited']['code'] == $playerCode ||
            $gameInfo['initiator']['code'] == $playerCode) {
            return $gameInfo;
        } else {
            return null;
        }
    }
}