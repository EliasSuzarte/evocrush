<?php
require_once("../autoload.php");
$SmartAction = new SmartAction();
$SmartAction->verifyLogin();

if(!empty($_POST['id'])){
    $id = (int) $_POST['id'];
}else{
    $id = 0;
}

if(!empty($_POST['limit'])){
    $limit = (int) $_POST['limit'];
}else{
    $limit = 0;
}


$re = $SmartAction->getCrushData($id,$limit);
$stmtResult = $re->fetchAll(PDO::FETCH_ASSOC);

$totUsers = count($stmtResult);

echo json_encode($stmtResult);

