<?php
class DbConnect {
    private $host = "localhost";
    private $username = "root";
    private $password = "";
    private $dbname = "FirmaKurierska";
    private $conn;

    public function __construct() {
        mysqli_report(MYSQLI_REPORT_OFF);
        $this->conn = new mysqli($this->host, $this->username, $this->password, $this->dbname);
        if ($this->conn->connect_error) {
            die("Błąd połączenia z serwerem bazy danych: " . $this->conn->connect_error);
        }
    }

    public function getConn() {
        return $this->conn;
    }
}