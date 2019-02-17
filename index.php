<?php
if (!empty($_COOKIE['mail']) && !empty($_COOKIE['pass'])) {
      require_once("autoload.php");

    $crypMail = $_COOKIE['mail'];
    $senha = $_COOKIE['pass'];

    function doLog($erroDescription){
        new ErrorReports($erroDescription);
    }

    $SmartAction = new SmartAction();

    $access = $SmartAction->verifyLogin($crypMail, $senha);

    /// cuidado, não coloque nada que não seja para usuário logado aqui antes do if $acess

    if($access == 0){
        // falha no login
        require_once("register.php");
        die('');
    }


    $result = $SmartAction->getUserData($crypMail, $senha);
     if($result->rowCount()>0){
         $userData = $result->fetch();
         $nome = strip_tags($userData['nome']);
         $sexo = strip_tags($userData['sexo']);
         $idade = (int) $userData['idade'];
         $perfilURL = strip_tags($userData['perfilURL']);
         $perfilID = (int) $userData['id'];
         $profpic = strip_tags($userData['profpic']);
         if($profpic ==null){
             $profpic = 'img/perfil.jpg';
         }
         $auth = 'granted';
         require_once('logado.php');

     }else{
         echo "Algo deu ruim, estamos confusos";
         doLog("Houve confirmação de login, mas não foi possível obter dados do usuário");
     }



}else{
    require_once("register.php");
}

