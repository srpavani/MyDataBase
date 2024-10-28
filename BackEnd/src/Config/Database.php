<?php
namespace App\Config;

use PDO;
use PDOException;

class Database {
    private $host = 'localhost';
    private $db_name = 'mydatabasecom';
    private $username = 'root';
    private $password = '';

    public function getConnection() {
        try {
            $conn = new PDO("mysql:host={$this->host};dbname={$this->db_name}", $this->username, $this->password);
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return $conn;
        } catch (PDOException $exception) {
            echo "Connection error: " . $exception->getMessage();
            return null;
        }
    }
}