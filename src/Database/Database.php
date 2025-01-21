<?php

class Database
{
    public mysqli $connection;

    public function __construct($hostname, $username, $password, $database)
    {
        $this->connection = mysqli_connect($hostname, $username, $password, $database);
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