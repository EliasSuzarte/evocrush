<?php
class ErrorReports{
    public function __construct($erroDescription){
        $Crud = new Crud();
        $sql = "INSERT INTO erros(description) VALUES(:description)";
        $binds = ['description'=>$erroDescription];
        $Crud->insert($sql,$binds);

    }
}