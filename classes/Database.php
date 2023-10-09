<?php

class Database
{
    private $server_name = "localhost";
    private $username = "root";
    private $password = "root"; // for mac user, $password = "root";
    private $db_name = "the_company"; // Database name that you create in PHPMyAdmin
    protected $conn; // an object for us to connect database

    public function __construct()
    {
        $this->conn = new mysqli($this->server_name, $this->username, $this->password, $this->db_name); //mysqli() is built in connect us with database

        if($this->conn->connect_error)
        {
            die("Unable to connect to the database: " . $this->conn->connect_error);
        }
    }
}
?>