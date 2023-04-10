<?php

class Cnx{
    private $server='localhost';
    private $db='test';
    private $user='root';
    private $pass='';
    function connection(){
        return mysqli_connect($this->server,$this->user,$this->pass,$this->db);
    }
}