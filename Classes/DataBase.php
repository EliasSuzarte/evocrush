<?php

class DataBase{
    private  $PDO;
    public function __construct($db,$user,$pass)
    {
        $dsn = "mysql:host=127.0.0.1;dbname=$db;charset=utf8";
        try {
          $this->PDO = new PDO($dsn, $user, $pass);
          //  $this->PDO->setAttribute(PDO::ATTR_ERRMODE,1);

        } catch (PDOException $e) {
            throw new PDOException(die("Houve um erro, tente novamente, código de erro: {$e->getCode()}"));
        }
    } // construct

    public function getPDO(){
        return $this->PDO;
    }



} // DataBase



