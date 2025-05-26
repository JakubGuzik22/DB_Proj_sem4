<?php
class DbConnect {
    private $host = "localhost";
    private $username = "root";
    private $password = "";
    private $dbname = "FirmaKurierska";
    private $conn;

    public function __construct() {
        $this->conn = new mysqli($this->host, $this->username, $this->password, $this->dbname) or die ("Błąd połączenia z serwerem bazy danych");
    }

    public function getConn() {
        return $this->conn;
    }
}