<?php

namespace utils;

use PDO;

class DBConnect {
    private $server;
    private $dbName;
    private $user;
    private $password;

    public function __construct()
    {
        $this->server = "us-cdbr-east-06.cleardb.net";
        $this->dbName ='heroku_24a64cc2987b36d';
        $this->user ='b24a522522b6a4';
        $this->password ='fb97f863';
    }

    public function connect() {
        try {
            $conn = new PDO('mysql:host=' .$this->server .';dbname=' . $this->dbName, $this->user, $this->password);
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return $conn;
        } catch (\Exception $e) {
            return "Database Error: " . $e->getMessage();
        }
    }
}