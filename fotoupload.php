<?php

if (empty($_FILES['foto'])) {
    die('Ops, você não enviou nenhum arquivo de foto');
} elseif ($_FILES['foto']['error'] > 0) {
     $error  = ['error'=>'Houve um erro no arquivo'];
     die(json_encode($error));
}

$type = $_FILES['foto']['type'];

if ($_FILES['foto']) {
    $foto = $_FILES['foto'];
    if ($foto['size'] > 6280083) {
        // se maior que 5.98 MB //6280083
        $erroSize = ['erro' => 'Arquivo muito grande'];
        die(json_encode($erroSize));
    }
}

if (!empty($_COOKIE['mail']) && !empty($_COOKIE['pass'])) {

    $crypMail = $_COOKIE['mail'];
    $senha = $_COOKIE['pass'];

    require_once("autoload.php");


    $SmartAction = new SmartAction();

    $access = $SmartAction->verifyLogin($crypMail, $senha);

    if ($access == 0) {
        header("Location: ?loginRequired=fotoupload");
        die(); //importante;
    }

} else {
    header("Location: ?loginRequired=fotoupload2");
    die();
}

//// se chegar até aqui é porqu etá logado

$Crud = new Crud();
$sqlSelect = "SELECT id FROM users WHERE cryptmail = :cryptmail AND senha = :senha";

$bindsSelect = ['cryptmail' => $crypMail, 'senha' => $senha];

$result = $Crud->select($sqlSelect, $bindsSelect);

if ($result->rowCount() > 0) {
    $userData = $result->fetch();
    $id = (int)$userData['id'];
    if ($id == 0) {
        die('Ops, houve um erro incomum');
    }
}else{
    new ErrorReports('Não foi possivel obter ID de usuario em fotoupload');
    die('Ops, não fou possivel obter ID de usuário');
}

$Up = new Upload($_FILES['foto'], "uploads", $type,$SmartAction->userID);

if ($Up->upStatus == true) {
    $picURL = $Up->fileLocation;

    $sql = "UPDATE users SET profpic = :picurl WHERE id = :id";
    $binds = ['picurl' => $picURL, 'id' => $id];
    updateToExPerfil($SmartAction->userID, $Crud);
    addFoto($SmartAction->userID, $picURL, $Crud); // registra a foto do perfil tbm na tabela foto

    $updateResult = $Crud->update($sql, $binds);
    if ($updateResult > 0) {
        $arr = ['url' => $picURL];
        echo json_encode($arr); // retorn url em json
    }


} else {
    $erro = ['erro' => 'Ops, não foi possível fazer o upload'];
    echo json_encode($erro);
}


function addFoto($userID, $profpic, $Crud){

    /// adciona novo foto ao banco, na tabela fotos tbm
    $sql = "INSERT INTO fotos(userID,fotoURL,tipo) VALUES(:userID,:fotoURL,:tipo)";
    $binds = ['userID' => $userID, 'fotoURL' => $profpic, 'tipo' => 'perfil'];
    $result = $Crud->insert($sql, $binds);
    if ($result > 0) {
        return true;
    } else {
        return false;
    }


} // addFoto


function updateToExPerfil($userID,$Crud){

        $sql = "UPDATE fotos SET tipo = :setTipo WHERE tipo = :tipo AND userID = :userID";
        $binds = ['setTipo' => 'experfil', 'tipo' => 'perfil', 'userID' => $userID];
        $Crud->update($sql, $binds);

}




?>