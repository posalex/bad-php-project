<?php

class Database
{
    public $hostname = '127.0.0.1';
    public $username = 'admin';
    public $password = 'solarwinds123';
    public $database = 'books';
    public mysqli $connection;

    public function __construct()
    {
        $this->connection = mysqli_connect($this->hostname, $this->username, $this->password, $this->database);
    }

    public function query($sql)
    {
        $arrResult = array();

        $objMysqliResult = mysqli_query($this->connection, $sql);

        while (true) {
            $row = mysqli_fetch_row($objMysqliResult);

            if ($row === null) {
                break;
            }

            $resultArray[] = $row;
        }

        mysqli_free_result($objMysqliResult);

        return $arrResult;
    }
}