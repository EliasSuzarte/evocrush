<?php
require_once('../autoload.php');
$SmartAction = new SmartAction();

if($SmartAction->verifyLogin()){
   // logado
    $userID = $SmartAction->userID;
    $Crud = new Crud();
    $sql = "SELECT id FROM msgstrocadas WHERE receiverID = :userID AND visto = :visto LIMIT 1";
    $binds = ['userID'=>$userID,'visto'=>0];
    $result = $Crud->select($sql,$binds);
    if($result->rowCount() > 0){
        $arr = ['has'=>true,'msgs'=>true];
        echo json_encode($arr);
    }else{
        $sql = "SELECT id FROM matches WHERE ((emID = :userID AND vistoEM ='0') OR (euID = :userID AND vistoEU ='0')) AND matched = '1' LIMIT 1";
        $binds = ['userID'=>$userID,'visto'=>0];
        $result = $Crud->select($sql,$binds);
        if($result->rowCount() > 0){
            $arr = ['has'=>true,'matches'=>true];
            echo json_encode($arr);
        }else{
            $arr = ['has'=>false];
            echo json_encode($arr);
        }
    }

}else{
    /// não logado
    $arr = ['has'=>false,'login'=>false];
    die(json_encode($arr));
}

