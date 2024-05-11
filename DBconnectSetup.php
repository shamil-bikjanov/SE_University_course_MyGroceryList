<?php

    class DBconnect {
        protected $dsn = NULL;
        protected $host = 'localhost';
        protected $port = NULL;
        protected $DBname = NULL;
        protected $username = NULL;
        protected $password = NULL;

        public function __construct() {}
/*
        public function setHost($host) {
            $this -> host = $host;
        }

        public function setPort($port) {
            $this -> port = $port;
        }

        public function setDB($DBname) {
            $this -> DBname = $DBname;
        }

        public function setUser($username) {
            $this -> username = $username;
        }

        public function setPassword($password) {
            $this -> password = $password;
        }
*/
        public function connect() {
            $this -> dsn = "mysql:host=" . $this->host . "; port=" . $this->port . "; dbname=" . $this->DBname;
            return new PDO($this->dsn, $this->username, $this->password);
        }
    }

?>