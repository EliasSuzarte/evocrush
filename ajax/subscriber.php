<?php
$alerts = [];


if (!empty($_POST['nome']) && !empty($_POST['sexo']) && !empty($_POST['idade']) && !empty($_POST['email']) && !empty($_POST['senha'])) {
    $nome = strip_tags(trim($_POST['nome']));
    $nome = str_replace(array("\"", "'"), "", $nome);
    $idade = (int)$_POST['idade'];
    $sexo = $_POST['sexo'];
    $email = $_POST['email'];
    $cryptMail = md5($email);
    $senha = md5($_POST['senha']);

    require_once("../autoload.php");
    $SmartAction = new SmartAction();
    $profileURL = $SmartAction->genereteProfileURL($nome,$idade);


    $mes =  time() + (30 * 24 * 60 * 60);
    setcookie("mail", $cryptMail, $mes,'/');
    setcookie("pass", $senha, $mes,'/');



    if (strlen($nome) < 3) {
        $alerts[] = "Nome deve ter no mínimo 3 letras";
    }
    if (strlen($nome) > 50) {
        $alerts[] = "Nome Muito Grande";
    }

    if ($idade < 18) {
        $alerts[] = "Você dever ter 18 anos ou mais";
    }

    if ($sexo != 'homem' && $sexo != 'mulher') {
        $alerts[] = "Selecione seu sexo";
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $alerts[] = "E-mail não tem um formato válido";
    }


} else {
    $alerts[] = "Preecha todos os campos";

}


if (count($alerts) > 0) {
/// erro, falta algo
/// sistema pode ser melhorando e exibir todas , mensagen de alerts[], mas requer mudança no DOM
    echo $alerts[0];
} else {
    /// pode proseguir com o cadastro
    /// mais ainda precisa verificar se o e-mail está disponivel
    $Crud = new Crud();

    $sqlSelect = "SELECT email FROM users WHERE email = :email";
    $bindsSelect = ['email' => $email];
    $selectResult = $Crud->select($sqlSelect, $bindsSelect); // retorna em resultado sql
    $count = (int)$selectResult->rowCount();
    if ($count == 0) {
        /// email disponível
        $sql = "INSERT INTO users (nome, email,cryptmail,confmailtoken, senha, sexo, idade,perfilURL) VALUES (:nome, :email,:cryptmail,:confmailtoken, :senha, :sexo, :idade, :perfilurl)";
        $cryptMail = md5($email);
        $preToken = rand(11,966)."x-{$email}";
        $confmailtoken = md5($preToken);
        $binds = ['nome' => $nome, 'email' => $email,'cryptmail'=>$cryptMail,'confmailtoken'=>$confmailtoken, 'sexo' => $sexo, 'senha' => $senha, 'idade' => $idade, 'perfilurl'=>$profileURL];
        $insert = $Crud->insert($sql, $binds);
        if ($insert) {
            echo "Cadastro com sucesso"; // sucesso no cadastro
            $MailHandler = new MailHandler();
            $MailHandler->sendTokenToNewUser($nome, $email, $confmailtoken);
        } else {
            echo "Ops, houve um falha, tente novamente"; // falha no cadastro
            $ER = new ErrorReports("Erro ao tentar cadastrar usuário");
        }

    }else{
        /// já tem alguém usando o e-mail
        echo "E-mail em uso, deseja fazer <a href='?login'>login</a> ?";
        // para evitar expor o usuário isso pode ser melhorado
    }


}


