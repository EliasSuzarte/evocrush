<?php
if(empty($_POST['delete']) && empty($_POST['deleteProfile'])){
   /// se empty então redireciona para gerenciar
    ///  ação requer duas etapas, se chegou aqui sem o $_POST['delete]
    ///  é por que uma etapa foi pulada ou tem que haver o deleteProfile
    /// que siginifica que já passou pela primeira etapa

    header("Location: /gerenciar");
}

if (!empty($_COOKIE['mail']) && !empty($_COOKIE['pass'])) {
    $senha = $_COOKIE['pass'];
    $email = $_COOKIE['mail'];
}else{
    header("Location: /?loginNeeded");
    die('não autenticado');
}
require_once("autoload.php");
$Config = new Config();
$cacheID = $Config->cacheID;
$SmartAction = new SmartAction();

$access = $SmartAction->verifyLogin($email, $senha);

if ($access == 0) {
    header("Location: /?loginNeeded");
    die();
}else{
    // então o usuário está logado
    $result = $SmartAction->getUserData($email, $senha);
    if ($result->rowCount() > 0) {
        $userData = $result->fetch();
        $nome = strip_tags($userData['nome']);
        $userID = (int) $userData['id'];
        if(!empty($_POST['deleteProfile'])){
            $DP = new DeleteProfile($userID);
            $deleted = $DP->deleted;
        }

    } else {
        die('Ops, houve um falhe no nosso sistema');
    }
}


?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Deletar Perfil - Evo Crush</title>
    <link rel="stylesheet" href="css/style.css?c=<?= $cacheID ?>">
    <link rel="shortcut icon" href="img/favicon.png">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <script async="async" src="/js/script.js?c=<?= $cacheID ?>"></script>
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


    <section class="gerenciar">
        <h2>Gerenciar Perfil</h2>

        <form method="post" action="">
            <fieldset>
                <legend>
                    <?php
                    if(!isset($deleted)):

                    ?>
                    <h3><?= $nome ?> Você Está Prestes a Deleter Seu Perfil</h3>
                   Todos seus dados serão apagados permanentemente, mas você poderá
                       se cadastrar de novo quando quiser.<br>
                    Se tiver certeza disso, clique no botão abaixo.
                    <?php
                    endif;
                    if(isset($deleted)){
                        if($deleted == true){
                            echo "{$nome} Seu perfil deletado com sucesso";
                        }else{
                            echo "{$nome} houve uma falha ao deletar seu perfil. Você pode tentar novamente, caso o erro continue contate nosso <a href='/suporte'>suporte</a>";
                        }
                    }

                    ?>

                </legend>
                <input type="hidden" name="deleteProfile" value="true">
                <button id="delete" class="red" type="submit">Deletar Perfil</button>
            </fieldset>


        </form>


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