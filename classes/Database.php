<?php

class Database
{

    /**
     * Get the database connection
     *
     * @return PDO object Connection to the database server
     */
    /**
     * Hostname
     * @var string
     */
    protected $db_host;

    /**
     * Database name
     * @var string
     */
    protected $db_name;

    /**
     * Username
     * @var string
     */
    protected $db_user;

    /**
     * Password
     * @var string
     */
    protected $db_pass;

    /**
     * Constructor
     *
     * @param string $host Hostname
     * @param string $name Database name
     * @param string $user Username
     * @param string $password Password
     *
     * @return void
     */
    public function __construct($host, $name, $user, $password)
    {
        $this->db_host = $host;
        $this->db_name = $name;
        $this->db_user = $user;
        $this->db_pass = $password;
        /* we construct this variables out of the argument being passed in when initiating this class.
         */
    }

    public function getConn()
    {
        $dsn = 'mysql:host=' . $this->db_host . ';dbname=' . $this->db_name . ';charset=utf8';
        /*  we need to create the data source name variable as per above. the string ones seems
        necessary 
        notice how we access the consturted properties from above into the dsn.*/
        try {
            $db = new PDO($dsn, $this->db_user, $this->db_pass);
            /* we return the connection as per above. this PDO is class as well. given one from
            built in php. */
            $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            /*  We set the error mode to throw an exception so that we can use the
            try catch block to handle it. */
            return $db;
        } catch (PDOException $e) {
            echo $e->getMessage(); //we have the getMessage method on the $error object.(array whatever..)
            exit;
        }
    }
}
