<?php

class Crud
{
    private $PDO;

    public function __construct()
    {
        require_once("DataBase.php");
        $DB = new DataBase('evocrush', 'root', '');
        $this->PDO = $DB->getPDO();

    }



    public function select($sql, $binds)
    {
        if (empty($sql)) {
            die("Dear developer, you forget some parameter to select function");
        }

        $stmt = $this->PDO->prepare($sql);
        if (is_array($binds)) {
            foreach ($binds as $key => $value) {
                $stmt->bindValue($key, $value);
            }
        } else {
            die("Bind is not array type");
        }

        $stmt->execute();
        return $stmt;

    } /// end select




    public function insert($sql, $binds)
    {
        if (empty($sql) || empty($binds)) {
            die("Dear develope, you forget some parameter to insert function");
        }

        $stmt = $this->PDO->prepare($sql);
        if (is_array($binds)) {
            foreach ($binds as $key => $value) {
                $stmt->bindValue($key, $value);
            }
        } else {
            die("Bind is not array type");
        }

        $stmt->execute();
        if ($stmt->rowCount() > 0) {
            return true;
        } else {
            return false;
        }
    } /// end insert



    public function delete($sql, $binds)
    {
        // o retorno deve ser sempre número, se false retorne 0 (zero)

        if (empty($sql) || empty($binds)) {
            die("Dear develope, you forget some parameter to delete function");
        }

        $stmt = $this->PDO->prepare($sql);
        if (is_array($binds)) {
            foreach ($binds as $key => $value) {
                $stmt->bindValue($key, $value);
            }
            $stmt->execute();
            return $stmt->rowCount();
        } else {
            return 0;
        }


    } /// end delete




    public function update($sql, $binds)
    {
        // o retorno deve ser sempre número, se false retorne 0 (zero)

        if (empty($sql) || empty($binds)) {
            die("Dear develope, you forget some parameter to delete function");
        }

        $stmt = $this->PDO->prepare($sql);
        if (is_array($binds)) {
            foreach ($binds as $key => $value) {
                $stmt->bindValue($key, $value);
            }
            $stmt->execute();
            return $stmt->rowCount();
        } else {
            return 0;
        }


    } /// end update



} /// end class