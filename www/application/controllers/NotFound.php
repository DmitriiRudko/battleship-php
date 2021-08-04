<?php

namespace Application\Controllers;
require_once(dirname(__FILE__) . "/../core/Controller.php");
require_once(dirname(__FILE__) . "/../helpers/JsonHelper.php");

use Application\Core\Controller;
use Application\Helpers\JsonHelper;

class NotFound extends Controller {
    public static function notFound(): void {
        header('HTTP/1.1 404 Not Found');
        JsonHelper::successFalse("Not Found", 404);
    }
}