<?php

namespace Application\Helpers;

class JsonHelper {
    public static function jsonifyAndSend($data) {
        header("Content-Type: application/json");
        $json = json_encode($data);
        if ($json === false) {
            $json = json_encode(array("jsonError", json_last_error_msg()));
            if ($json === false) {
                $json = '{"jsonError": "unknown"}';
            }
            http_response_code(500);
        }
        echo $json;
    }

    public static function successFalse($code = 500) {
        header("Content-Type: application/json");
        http_response_code($code);
        echo '{"success":False}';
    }
}