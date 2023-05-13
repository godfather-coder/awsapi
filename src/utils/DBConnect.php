<?php

namespace utils;

use PDO;
use Symfony\Component\Dotenv\Dotenv;
class DBConnect {

    private $server;
    private $dbName;
    private $user;
    private $password;

    public function __construct($host,$dbname,$dbusername,$dbpassword)
    {
        $this->server =$host;
        $this->dbName =$dbname;
        $this->user =$dbusername;
        $this->password =$dbpassword;
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