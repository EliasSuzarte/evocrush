<?php
require_once("../autoload.php");
$SmartAction = new SmartAction();
if($SmartAction->verifyLogin()){
    $userID = $SmartAction->userID;
    $sql = "UPDATE users SET online = CURRENT_TIMESTAMP WHERE id = :id";
    $binds = ['id'=>$userID];
    $Crud = new Crud();
    $re = $Crud->update($sql,$binds);
    if($re > 0){
        echo "Atualizado com sucesso";
    }else{
        echo "Não atualizado";
    }
}else{
    echo "Não logado";
}
