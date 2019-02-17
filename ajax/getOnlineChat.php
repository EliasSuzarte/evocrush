<?php
date_default_timezone_set("America/Sao_Paulo");

if (!empty($_COOKIE['mail']) && !empty($_COOKIE['pass'])) {
    require_once("../autoload.php");

    $crypMail = $_COOKIE['mail'];
    $senha = $_COOKIE['pass'];


    $SmartAction = new SmartAction();

    $access = $SmartAction->verifyLogin($crypMail, $senha);
    //cuidado, não coloque nada que não seja para usuário logado aqui antes do if $acess

    if ($access == 0) {
        // falha no login
        die('[erro: login error]');
    }



}else{
    //sem os cookies
    die('[erro:no session]');
}




function onlineOnChat(){

    $Crud = new Crud();
    $plusFive = strtotime("-5 minutes",strtotime( date('Y-m-d H:i:s')));
    $five = date("Y-m-d H:i:s", $plusFive);

    $sql = "SELECT users.perfilURL, users.nome,users.sexo FROM chat JOIN users ON chat.userID = users.id WHERE chat.tempo >= :tempo GROUP BY users.perfilURL ORDER BY chat.tempo DESC LIMIT 40";
    $binds = ['tempo'=>$five];

    $result = $Crud->select($sql, $binds); // return true or false

    if($result->rowCount()>0){
        $data = $result->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($data);
    }else{
        echo "[]";
    }


} // end onlineOnChat


onlineOnChat();

