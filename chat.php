<?php

/// SELECT users.nome, users.idade, chat.msg, chat.id FROM chat JOIN users on users.id = chat.userID


if (!empty($_COOKIE['mail']) && !empty($_COOKIE['pass'])) {
    require_once("autoload.php");
    $Config = new Config();
    $cacheID = $Config->cacheID;

    $crypMail = $_COOKIE['mail'];
    $senha = $_COOKIE['pass'];

    function doLog($erroDescription)
    {
        new ErrorReports($erroDescription);
    }

    $SmartAction = new SmartAction();

    $access = $SmartAction->verifyLogin($crypMail, $senha);

    /// cuidado, não coloque nada que não seja para usuário logado aqui antes do if $acess

    if ($access == 0) {
        // falha no login
        header("Location: /?chat=loginNeeded");
        die('no');
    }


    $result = $SmartAction->getUserData($crypMail, $senha);
    if ($result->rowCount() > 0) {
        $userData = $result->fetch();
        $nome = strip_tags($userData['nome']);
        $sexo = strip_tags($userData['sexo']);
        $idade = (int) $userData['idade'];
        $perfilURL = strip_tags($userData['perfilURL']);
        $auth = 'granted';
        if (empty($userData['profpic'])) {
            $profpic = '/img/perfil.jpg';
        } else {
            $profpic = $userData['profpic'];
        }

    } else {
        echo "Algo deu ruim, estamos confusos";
        doLog("Houve confirmação de login, mas não foi possível objter dados do usuário em chat.php");
        die();
    }


} else {
    // sem os cookies
    header("Location: /?chat=LoginNeeded");
    die();
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Chat - Evo Crush</title>
    <meta name="description" content="Sala de chat do Evo Crush, bate papo animado com pessoas buscando a evolução espiritual e moral">
    <meta name="keywords" content="namoro,chat, bate papo espiritual,forum espiritualidade, site de namoro,crush, encontar crush">
    <link rel="stylesheet" href="css/style.css?c=<?= $cacheID ?>">
    <link rel="shortcut icon" href="img/favicon.png">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <script async="async" src="js/chat.js?c=<?= $cacheID ?>"></script>
    <script async="async" src="js/generalAjax.js?c=<?= $cacheID ?>"></script>
    <script src="/js/script.js?c=<?= $cacheID ?>"  async="async"></script>
</head>
<body>

<div class="container">
    <section id="secnav">
        <header>
            <nav>
                <div id="toggle"></div>
                <ul>
                    <?php
                    $Config = new Config();
                    $Config->getMenu();
                    ?>
                </ul>
                <div id="x">[X]</div>
            </nav>
        </header> <!-- header nav -->
    </section>

   <div class="content">
       <section class="perfil">
           <div class="img">
               <img title="<?= $nome ?>" src="<?= $profpic ?>">
               <h3>Seu Perfil</h3>
               <div class="name"><?= $nome ?></div>
               <div class="sexo">Sexo: <?= $sexo ?></div>
               <div class="idade">Idade: <?= $idade ?></div>
           </div>
       </section> <!-- perfil end -->

       <section class="online">
           <div class="chat">
               <div class="msgsBlock">

               </div><!-- msgsBlock -->
               <div class="textbar">
                   <form method="post">
                       <textarea></textarea>
                       <button type="submit" id="submitJS">Enviar</button>
                   </form>
               </div>
           </div>
          <div class="perfilsOnline"><h1>Pessoas Online</h1><div id="person"></div> </div>
       </section> <!-- online -->

   </div>

</div> <!-- container-->
<section id="foot">
    <footer>
        <?php
        $Config->getFooter();
        ?>
    </footer>
</section>

<script async="true">

    timer = setInterval(function () {
        // necessário caso o scrip ainda não esteja carregado
        if (typeof doAjax == 'function') {
            clearInterval(timer);
            doAjax('GET', '/ajax/getOnlineChat.php', null, implementOnline)
        }
    }, 500);

    setInterval(function () {
        doAjax('GET', '/ajax/getOnlineChat.php', null, implementOnline);
        // a cada minuto
    },60000);

    function implementOnline(response) {
        response = JSON.parse(response);
        var perfilOnline = document.querySelector("#person");
        perfilOnline.innerHTML = '';
        response.forEach(function (res) {
            var div = document.createElement("div");
            div.classList.add('inChat');
            div.classList.add(res.sexo);
            div.innerHTML = "<a target='_blank' href='/perfil/" + res.perfilURL + "'>" + res.nome + "</a>";
            perfilOnline.append(div);
        })

    }
</script>

</body>
</html>
