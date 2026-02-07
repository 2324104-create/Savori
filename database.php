<?php
class Database {
    private $host = "localhost";
    private $db_name = "savori_db";
    private $username = "root";
    private $password = "";
    public $conn;

    public function getConnection() {
        $this->conn = null;
        try {
            $this->conn = new PDO("mysql:host=$this->host", $this->username, $this->password);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            // Create database if not exists
            $this->conn->exec("CREATE DATABASE IF NOT EXISTS $this->db_name CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
            $this->conn->exec("USE $this->db_name");
            
        } catch(PDOException $e) {
            die("Connection error: " . $e->getMessage());
        }
        return $this->conn;
    }
}
?>