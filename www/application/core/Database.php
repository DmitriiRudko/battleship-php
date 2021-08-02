<?php

namespace Application\Core;
require_once("connection-settings.php");

use PDO, Exception;

class Database {
    private static $instance;

    private $db;

    public static function getInstance(): self {
        if (!isset(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        try {
            $this->db = new PDO(DNS, USER, PASSWD, OPT);
        } catch (Exception $ex) {
            echo 'Caught exception: ', $ex->getMessage(), "\n";
        }
    }

    public function getOne(string $sql, array $params = []): array {
        $stm = $this->db->prepare($sql);
        $stm->execute($params);
        $data = $stm->fetch();
        return $data;
    }

    public function getMany(string $sql, array $params = []): array {
        $stm = $this->db->prepare($sql);
        $stm->execute($params);
        $data = $stm->fetchAll();
        return $data;
    }

    public function produceStatement(string $sql, array $params = []): void {
        $stm = $this->db->prepare($sql);
        $stm->execute($params);
    }

    public function lastInsertedId(): int {
        return $this->db->lastInsertId();
    }

    private function __clone() {
    }

    private function __wakeup() {
    }
}