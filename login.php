<?php
require_once("autoload.php");
$Config = new Config();
$cacheID = $Config->cacheID;
if(!empty($_POST['senha']) && !empty($_POST['email'])){
    $mail = md5(trim($_POST['email']));
    $senha = md5(trim($_POST['senha']));
    $SmartAction = new SmartAction();
    $result = $SmartAction->verifyLogin($mail, $senha);

    if($result>0){
        $mes =  time() + (30 * 24 * 60 * 60);
        setcookie("mail",$mail,$mes,"/");
        setcookie("pass",$senha,$mes,"/");
        header("Location: /");
    }else{
        $msg = "Dados incorretos, tentente novamente!";
    }
}

?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Evo Crush</title>
    <meta name="description" content="Evo Crush é uma maneira evoluída de encontrar seu par ideal, será que seu crush está aqui?">
    <meta name="keywords" content="namoro, site de namoro,crush, encontar crush">
    <meta property="og:image" content="https://evocrush.com/img/social.jpg">
    <link rel="stylesheet" href="css/style.css?c=<?= $cacheID  ?>">
    <link rel="shortcut icon" href="img/favicon.png">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, follow">
    <script src="/js/script.js?c=<?= $cacheID  ?>" async="async"></script>
</head>
<body>

<div class="container">
    <section id="secnav">
        <header>
            <nav>
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


    <section class="subscriber">
        <h2>Cadastro Rápido</h2>
        <div id="showmsg"></div>
        <div class="goLogin">Ainda sem uma conta? <a href="/">Cadastre-se</a><br><br></div>
        <?php
        if(!empty($msg)){
            echo "<div>{$msg}</div>";
        }
        ?>
        <form method="post" action="#">
            <label>E-mail: <input id="mailJS" required="true" type="email" placeholder="E-email" name="email"></label>
            <label>Senha: <input id="senhaJS" placeholder="Senha" type="password" name="senha"></label>
            <input id="cadJS" type="submit" value="Entrar">
        </form>
        <a rel="nofollow" class="forgetPass" href="/recover">Perdeu a senha?</a>
    </section> <!-- subs -->

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