<?php

/// os sistema de postagem de mensagem deve ser separado do sistema de envio
///  pois isso causa conflito na requisições ajax

if(!empty($_POST['msg'])){
    $msg = strip_tags($_POST['msg']);
    $msg = trim($msg);
    if(strlen($msg)<1){
        die('[erro: short mensagem]');
    }

}else{
    die('erro:no message specifid');
}


if (!empty($_COOKIE['mail']) && !empty($_COOKIE['pass'])) {
    require_once("../autoload.php");

    $crypMail = $_COOKIE['mail'];
    $senha = $_COOKIE['pass'];

    function doLog($erroDescription)
    {
        new ErrorReports($erroDescription);
    }

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



function postMsg($msg,$cryptmail, $senha){
    $Crud = new Crud();

    /// pegamos o id do usuário
    $select_sql = "SELECT id FROM users WHERE cryptmail = :cryptmail AND senha = :senha";
    $select_binds = ['cryptmail'=>$cryptmail, 'senha'=>$senha];

    $select_result = $Crud->select($select_sql, $select_binds);
    if($select_result->rowCount()>0){
        $data = $select_result->fetch();
        $userID = $data['id'];
    }else{
        die('[error:user not identify]');
    }

    $msg = strip_tags($msg);
    $userID = (int) $userID;

    $sql = "INSERT INTO chat SET msg = :msg, userID = :userID";
    $binds = ['msg'=>$msg,'userID'=>$userID];

    $result = $Crud->insert($sql, $binds); // return true or false


    if($result){
        echo "[success]";
    }else{
        echo "[erro: message not sent]";
    }
}







if(!empty($msg)){
    postMsg($msg,$crypMail,$senha);
}else{
    die(['erro: isso não deveria acontecer']);
    doLog("Verificar chatHandle, essa falha não deveria acontecer");
}
