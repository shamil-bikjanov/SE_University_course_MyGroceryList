<?php

require_once "DBconnectSetup.php";

class MagebitTask extends DBconnect {
    public function __construct() {
        $this -> port = '3306';
        $this -> DBname = 'magebit';
        $this -> username = 'root';
        $this -> password = '';
    }
}

?>