<?php

namespace Application\Core;
require_once(dirname(__FILE__) . "/../model/ModelGames.php");

use Application\Model\ModelGames;

class Controller {
    public function getGameInfo(int $gameId, string $playerCode): ?array {
        $model = new ModelGames();
        $gameInfo = $model->getGameInfo($gameId);

        if (!$gameInfo) return null;

        $isCurrentGameUser = $gameInfo['invited']['code'] === $playerCode
            || $gameInfo['initiator']['code'] === $playerCode;

        if ($isCurrentGameUser) {
            $gameInfo['me'] = ($gameInfo['invited']['code'] === $playerCode ? $gameInfo['invited'] : $gameInfo['initiator']);
            return $gameInfo;
        }

        return null;
    }
}