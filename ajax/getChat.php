<?php

if(!empty($_POST['id'])){
    $id = $_POST['id'];
}else{
    die('erro:no id specifid');
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




function getMsgs($id){

    $Crud = new Crud();
    $id = (int) $id;

    $sql = "SELECT chat.msg, chat.tempo, chat.id, users.nome, users.sexo FROM chat JOIN users ON users.id = chat.userID WHERE chat.id > :chatid";
    $binds = ['chatid'=>$id];

    $result = $Crud->select($sql, $binds); // return true or false

    if($result->rowCount()>0){
        $data = $result->fetchAll(PDO::FETCH_ASSOC);
           echo json_encode($data);
    }else{
        echo "[]";
    }


} // end getMsgs




if(!empty($id)){
    getMsgs($id);
}else{
    die(['erro: isso não deveria acontecer']);
    doLog("Verificar chatHandle, essa falha não deveria acontecer");
}
