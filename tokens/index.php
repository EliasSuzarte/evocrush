<?php
    /// CONFIRMAR EMAIL VIA TOKEN
require_once '../autoload.php';
$Config = new Config();
$cacheID = $Config->cacheID;

?><!DOCTYPE html>
    <html lang="pt-br">
    <head>
        <meta charset="UTF-8">
        <title>Confirmar Email - Evo Crush</title>
        <meta name="description" content="Evo Crush é uma maneira evoluída de encontrar seu par ideal, será que seu crush está aqui?">
        <meta name="keywords" content="namoro, site de namoro,crush, encontar crush">
        <link rel="stylesheet" href="/css/style.css?c=<?= $cacheID ?>">
        <link rel="shortcut icon" href="/img/favicon.png">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="robots" content="noindex">
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
            <div style="width: 80%;margin: 0 10%;text-align: center;font-size:1.5em;background-color: #ff6452;min-height: 150px">
                <?php
//// SISTEMA PARA CONFIRMAR E-MAIL
if(!empty($_GET)){
    $Crud = new Crud();

    if(!empty($_GET['confirmail'])){
        // se houver um token válido, confirma email
        $token = $_GET['confirmail'];
        $sql = "UPDATE users SET confmail = '1' WHERE confmailtoken = :token LIMIT 1";
        $binds = ['token'=>$token];
        $re = $Crud->update($sql, $binds);
        if($re < 1){
            $sql = "SELECT confmail FROM users WHERE confmailtoken = :token";
            $binds = ['token'=>$token];
            $result = $Crud->select($sql, $binds);
            if($result->rowCount() > 0){
                $data = $result->fetch(PDO::FETCH_ASSOC);
                $isConfirmd = (int) $data['confmail'];
                if($isConfirmd == 1){
                   echo "Esse e-mail já foi confirmado";
                }

            }else{
                echo "Esse token é inválido";
            }
        }else{
            echo "Email confirmado com sucesso";
        }

    } /// if do !empty($_GET['confirmail'])

    echo "<br>Volte para página inicial <a href='/'>clique aqui</a>";

}else{
    header('Location: /?token=none');
}
?></div></div>


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