<?php
require_once "DbConnect.php";

class User {
    private $conn;

    public function __construct() {
        $db = new DbConnect();
        $this->conn = $db->getConn();
    }

    public function register() {

    }
    public function login() {
        
    }
}