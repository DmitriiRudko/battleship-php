<?php

namespace Application\Controllers;
require_once(dirname(__FILE__) . "/../core/Controller.php");

use Application\Core\Controller;

class NotFound extends Controller {
    public static function notFound(): void {
        header('HTTP/1.1 404 Not Found');
    }
}