<?php
require_once("autoload.php");
$Config = new Config();
$cacheID = $Config->cacheID;
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Evo Crush</title>
    <meta name="description" content="Evo Crush é uma maneira evoluída de encontrar seu par ideal, será que seu crush está aqui?">
    <meta name="keywords" content="namoro, site de namoro,crush, encontar crush">
    <link rel="stylesheet" href="css/style.css?c=<?= $cacheID  ?>">
    <link rel="shortcut icon" href="img/favicon.png">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, follow">
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


    <section class="subscriber" style="min-width: 600px">
        <h2>Politica de Privacidade e Termos de Uso</h2>
        <p>Ao usar nosso site você concorda em seguir nossos termos de uso.<br></p>
        <p>Todos os usuário do EvoCrush devem se comportarem de forma adequada ao se comunicarem com outros usuários,
            nunca  faça uso de palavrões e xingamentos.<br> Ofensas são totalmente proibidas.<br>
        </p>
        <p>
            Ao submter sua foto para nosso servidor, certifique que a mesma não contenha nudez.<br>
        </p>
        <p>Nosso site é apenas para maiores de idade, caso você seja considerada(o) menor de idade de acordo as leis de seu país
        por favor não se cadastre em nosso site, e caso já tenha um cadastro, delete-o imediatamente na página de gerenciamento de perfil.<br></p>

        <p>
            Ao utilizar a EvoCrush, saiba que precisamos armazenar algumas de suas informações, como nome e email, do contrário não seria
            possível manter seu perfil em nossa rede.<br>
        </p>
        <p>Nosso site faz uso de cookies para identificar você dentro da EvoCrush.</p>

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