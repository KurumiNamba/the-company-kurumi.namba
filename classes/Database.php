<?php 

class Database {
    private $server_name = "localhost";
    private $username = "root";
    private $password = "";
    private $db_name = "the_company";
    protected $conn;

    // differences between "private" and "protected"
    // private --> cannot be accessed from outside the class
    // protect --> can be accessed from outside the class

    # Define constructor
    public function __construct() {
        $this->conn = new mysqli($this->server_name, $this->username, $this->password, $this->db_name);

        #check connection for any error
        if ($this->conn->connect_error) {
            #if there is an error
            die("Unable to connect to the database: ". $this->conn->connect_error);
        }
    }
}

?>