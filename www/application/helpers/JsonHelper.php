<?php

namespace Application\Helpers;

class JsonHelper {
    public static function jsonifyAndSend($data) {
        header('Content-Type: application/json');
//        header('Access-Control-Allow-Origin: *');
//        header('Access-Control-Allow-Methods: GET, POST');
//        header("Access-Control-Allow-Headers: X-Requested-With");
        $json = json_encode($data);
        if (!$json) {
            $json = json_encode(array("jsonError", json_last_error_msg()));
            if (!$json) {
                $json = '{"jsonError": "unknown"}';
            }
            http_response_code(500);
        }
        echo $json;
    }

    public static function successFalse(string $message = 'Smth went wrong', int $code = 400): void {
        header('Content-Type: application/json');
//        header('Access-Control-Allow-Origin: *');
//        header('Access-Control-Allow-Methods: GET, POST');
//        header("Access-Control-Allow-Headers: X-Requested-With");
        http_response_code($code);
        $json = [
            'success' => False,
            'code' => $code,
            'message' => $message,
        ];
        echo json_encode($json);
    }

    public static function successTrue(): void {
        header("Content-Type: application/json");
//        header('Access-Control-Allow-Origin: *');
//        header('Access-Control-Allow-Methods: GET, POST');
//        header("Access-Control-Allow-Headers: X-Requested-With");
        http_response_code(200);
        echo '{"success":true}';
    }
}