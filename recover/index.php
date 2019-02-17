<?php
require_once '../autoload.php';
$Config = new Config();
$cacheID = $Config->cacheID;

?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Recuperar Conta - Evo Crush</title>
    <meta name="description" content="Perdeu sua conta, esqueceu sua senha da EvoCrush? recupere aqui">
    <meta name="keywords" content="namoro, site de namoro,crush, recuperar conta, rede social">
    <link rel="stylesheet" href="/css/style.css?c=<?= $cacheID ?>">
    <link rel="shortcut icon" href="/img/favicon.png">
    <meta name="viewport" content="width=device-width, initial-scale=1">
     <script async="async" src="/js/script.js?c=<?= $cacheID ?>"></script>

</head>
<body>

<div class="container">
    <section id="secnav">
        <header>
            <nav>
                <div class="notification">Notificação</div>
                <div id="toggle"></div>
                <ul>
                    <?php
                    $Config->getMenu();
                    ?>
                </ul>
                <div id="x">[X]</div>
            </nav>
        </header> <!-- header nav -->
    </section>

    <div class="content">
        <div class="recover">
            <?php
            if(!empty($_POST['mail'])){
                $MailHandler = new MailHandler();
                $email = trim($_POST['mail']);
                if(filter_var($email,FILTER_VALIDATE_EMAIL)){
                    $sql = "SELECT email FROM users WHERE email = :email LIMIT 1";
                    $binds = ['email'=>$email];
                    $Crud = new Crud();
                    $result = $Crud->select($sql, $binds);
                    if($result->rowCount() > 0){
                        // ok  email existe;
                        // gera novo token
                        $preToken = rand(11,966)."x-{$email}";
                        $token = md5($preToken);
                        $sql = "UPDATE users SET confmailtoken = :token WHERE email = :email LIMIT 1";
                        $binds = ['token'=>$token, 'email'=>$email];
                        $result = $Crud->update($sql,$binds);
                        if($result > 0){
                            echo "<div class='success'>Enviamos um e-mail para {$email}, abra-o e clique no link para criar uma nova senha</div>";
                            $MailHandler->recoverAccount($email,$token);
                        }else{
                            echo "Ops, infelizmente estamos enfretandos problemas, tente mais tarde";
                            new ErrorReports('Falha ao fazer update de token, em recover/index.php');
                        }
                    }else{
                       echo '<div class="alert">Esse email não existe no nosso sistema</div>';
                    }
                }else{
                    echo "<div class='alert'>Esse e-mail não é válido</div>";
                }
            }
            ?>
            <h1>Recuperação de Conta</h1>
            <p>Insira o e-mail usada na hora de criar sua conta</p>
            <form method="post" action="">
                <label><input id="inputrec" type="email" name="mail" placeholder="Email"></label>
                <input id="btnrec" type="submit" value="Recuperar">
            </form>
        </div>
    </div>


</div> <!-- container-->
<section id="foot">
    <footer>
        <?php
        $Config->getFooter();
        ?>
    </footer>
</section>

</body>
</html>
