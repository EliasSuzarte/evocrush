<?php
require_once 'autoload.php';
$Config = new Config();
$cacheID = $Config->cacheID;
?><!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Evo Crush</title>
    <meta name="description" content="Evo Crush é uma maneira evoluída de encontrar seu par ideal, será que seu crush está aqui?">
    <meta name="keywords" content="namoro, site de namoro,crush, encontar crush">
    <link rel="stylesheet" href="css/style.css?c=<?= $cacheID ?>">
    <link rel="shortcut icon" href="img/favicon.png">
    <link rel="canonical" href="https://evocrush.com">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta property="og:image" content="https://evocrush.com/img/social.jpg">
    <script src="js/async.js?c=<?= $cacheID ?>"></script>
    <script src="js/script.js?c=<?= $cacheID ?>" async="async"></script>
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
        <div class="goLogin">Já tem uma conta? <a href="login.php">Faça login</a><br><br></div>
        <form method="post" action="#">
            <label for="nome">Nome:</label>
            <input type="text" id="nome" required="true" name="nome" placeholder="Nome">
            <span style="display: block">Você é...</span>
            <label class="sexo" id="sexOne"><input id="sexhJS" type="radio" required="true" name="sexo" value="homem">Homem</label>
            <label class="sexo"><input id="sexmJS" type="radio" name="sexo" value="mulher">Mulher</label>
            <label style="display: block">Sua idade:<input id="idadeJS" style="width: 49px" type="number" required="true" name="idade"> </label>
            <label>E-mail: <input id="mailJS" required="true" type="email" placeholder="E-email" name="email"></label>
            <label>Senha: <input id="senhaJS" placeholder="Senha" type="password" name="senha"></label>
            <input id="cadJS" type="submit" value="Cadastrar">

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

<script>
    var formCad = document.querySelector("form");
    formCad.addEventListener("submit",function (e) {
        e.preventDefault();
        var sexo;
        var canContinue = true;
        var errosMSG = [];


        var nome = document.querySelector("#nome").value;
        if(nome.length<3){
            canContinue = false;
            errosMSG.push("Nome deve ter no mínimo 3 letras");
        }


        if(document.querySelector("#sexhJS").checked == true){
            sexo = "homem";
        }else if(document.querySelector("#sexmJS").checked == true){
            sexo = "mulher";
        }else{
            canContinue = false;
            errosMSG.push("Selecione seu sexo");
        }


        var idadeJS = document.querySelector("#idadeJS").value;
        if(idadeJS >= 18 && idadeJS < 125){
            var idade = idadeJS;
        }else{
            canContinue = false;
            errosMSG.push("Você precisa ter 18 ou mais, preencha o campo idade com sua idade real!");
        }

        var email = document.querySelector("#mailJS").value;
        if(email.length<5){
            canContinue = false;
            errosMSG.push("Por favor, informe um e-mail válido");
        }

        var senha = document.querySelector("#senhaJS").value;
        if(senha.length<3){
            canContinue = false;
            errosMSG.push("Digite uma senha com no mínimo 3 caracteres");
        }

        if(canContinue == true){
            document.querySelector("#showmsg").style.display = "none";
            doCad(nome,sexo,idade,email,senha);
        }else{
            var theError = "Erro: "+errosMSG[0]+"";
            domStatus(theError);
        }



    }) // click event end
</script>

</body>
</html>