<?php
    function DbConnect()
    {
        try {
            $con = new PDO('mysql:host=localhost;charset=utf8;dbname=transportcompany;', 'root', '');
            $con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            echo("Невозможно выполнить подключение к базе данных! ".$e->getMessage());
        }
        return $con;
    }

?>