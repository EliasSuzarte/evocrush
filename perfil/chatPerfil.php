<?php
if(!empty($_POST['msg']) && !empty($_POST['receiverID'])){
    require_once "../autoload.php";
    $msg = strip_tags(trim($_POST['msg']));
    $receiverID = $_POST['receiverID'];
    if(!empty($_COOKIE['mail']) && !empty($_COOKIE['pass'])){
        $pass = $_COOKIE['pass'];
        $mail = $_COOKIE['mail'];
        $sql = "SELECT id FROM users WHERE cryptmail = :cryptmail AND senha = :senha";
        $binds = ['cryptmail'=>$mail, 'senha'=>$pass];
        $Crud = new Crud();
        $result = $Crud->select($sql, $binds);
        if($result->rowCount()>0){
            // se entrou no if é porque o usuário está autenticado
            $dados = $result->fetch();
            $senderID = $dados['id'];
            if(strlen($msg)>0){
                sendMSG($msg,$receiverID, $senderID, $Crud);
            }else{
                echo "Mensagem muito pequena";
            }
        }
    }
}else if(!empty($_POST['getmsg']) && !empty($_POST['perfilID'])){
    // então não é uma submissão de mensagem e sim uma requisição
    if(!empty($_COOKIE['pass']) && !empty($_COOKIE['mail'])){
        $senha = $_COOKIE['pass'];
        $cryptmail = $_COOKIE['mail'];
        $perfilID = $_POST['perfilID'];
        getMSG($cryptmail, $senha, $perfilID);

    }

}else{
    die("Você não enviou mensagem");
}


function sendMSG($msg,$receiverID,$senderID, $Crud){
    if($receiverID != $senderID){
        $sql = "INSERT INTO msgstrocadas(msg, senderID, receiverID, visto) VALUES(:msg, :senderID, :receiverID, :visto)";
        $binds  = ['msg'=>$msg,'senderID'=>$senderID, 'receiverID'=>$receiverID,'visto'=>0];
        $result = $Crud->insert($sql, $binds);
        if($result > 0){
            echo "[Sucesso]";
        }else{
            echo "[Falha no envio]";
        }
    }else{
        echo "Você não pode enviar mensagem para você mesmo";
    }

}


function getMSG($cryptmail, $senha, $perfilID){
    require_once("../autoload.php");
    $perfilID = (int) $perfilID;
    // primeiro verfifica se o usuário está logado
    $sql = "SELECT id FROM users WHERE cryptmail = :cryptmail AND senha = :senha";
    $binds = ['cryptmail'=>$cryptmail, 'senha'=>$senha];
    $Crud = new Crud();
    $result = $Crud->select($sql, $binds);
    if($result->rowCount()>0){
        $data = $result->fetch();
        $userID = $data['id'];
 $sql = "SELECT m.msg, m.senderID, m.tempo, u.email,u.nome FROM msgstrocadas m INNER JOIN users u ON ((m.senderID = :perfilID AND m.receiverID = :userID) OR (m.senderID = :userID AND m.receiverID = :perfilID)) WHERE  u.id = :userID ORDER BY m.id DESC LIMIT 30";
        $binds = ['perfilID'=>$perfilID,'userID'=>$userID];
        $result = $Crud->select($sql, $binds);
        if($result->rowCount()>0){
            $data = array_reverse($result->fetchAll(PDO::FETCH_ASSOC));
            echo json_encode($data);

        }else{
            $error = ['error'=>'Você ainda não tem mensagens'];
            echo json_encode($error);
        }

    }else{
        $error = ['error'=>'você não está logado'];
        die(json_encode($error));
    }


}



?>


