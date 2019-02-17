<?php
// essa é a pagina que usuário logado podem ver
if(empty($auth)){
    die('Sem acesso direto');
}

require_once 'autoload.php';
$Config = new Config();
$cacheID = $Config->cacheID;

?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Evo Crush</title>
    <meta name="description" content="Evo Crush é uma maneira evoluída de encontrar seu par ideal, será que seu crush está aqui? Venha para a rede social evolutiva">
    <meta name="keywords" content="namoro, site de namoro,crush, encontar crush, bate papo espiritual, chat e forum sobre espiritualidade,rede social">
    <link rel="stylesheet" href="css/style.css?c=<?= $cacheID ?>">
    <link rel="shortcut icon" href="img/favicon.png">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <script src="/js/async.js?c=<?= $cacheID ?>"></script>
    <script src="/js/generalAjax.js?c=<?= $cacheID ?>"></script>
    <script src="/js/script.js?c=<?= $cacheID ?>" async="async"></script>
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
       <section class="perfil" <?php if("img/perfil.jpg" == $profpic){echo"style='display:block !important;'";}?>>
           <div class="img">
               <img src="<?= $profpic ?>">
           </div>
           <div class="upload">
               <form method="post" action="fotoupload.php" id="formulario" enctype="multipart/form-data">
                   <label class="up"><input id="upfoto" type="file" name="foto">Enviar Foto</label>
                   <button id="buttonup"  type="submit"></button>
               </form>
           </div>
           <h1>Seu Perfil</h1>
           <div class="name"><?= $nome ?></div>

           <div class="sexo">Sexo: <?= $sexo ?></div>
           <div class="idade">Idade: <?= $idade ?></div>
       </section> <!-- perfil end -->
       <section class="crush">
           <div class="img">
               <img src="img/perfil.jpg">
               <div class="button"> <div class="vou">VOU</div><div class="naovou">NÃO VOU</div></div>
           </div>
           <div class="crushInfo">
               Nome : <span id="crushNome"></span><br>
               Idade: <span id="crushIdade"></span> <br>
               Sexo: <span id="crushSexo"></span>
               <br><a id="perfilURL" href="">Ver Perfil</a>
               <span class="perfilID"></span>
           </div>
       </section> <!-- crush end -->

   </div>




</div> <!-- container-->
<section id="foot">
    <footer>
        <?php
        $Config->getFooter();
        ?>
    </footer>
</section>

<script>
    lastID = 0;
    document.querySelector(".vou").addEventListener("click",function () {
       vote(true);
    });

    document.querySelector(".naovou").addEventListener("click",function () {
        vote(false);
    });

    function vote(boolean){
    /// problem with the match system, then redirect to chat
        document.location ='chat.php';
        var pID = document.querySelector(".perfilID").id;
        if(boolean){
            var url = 'ajax/vnv.php?vote=1&id='+pID;
        }else{
            var url = 'ajax/vnv.php?vote=0&id='+pID;
        }
        doAjax('GET', url, null,function (res) {
            console.log(res);
            voteEngine();
        });
    }


    function loadImages(json) {
        console.log('chamada',json);
        if(typeof(json) != "undefined"){
            var loadImageDiv = document.createElement("div");
            loadImageDiv.style.display ='none';
            json.forEach(function (img) {
                if(img.profpic == null){
                    img.profpic ='/img/perfil.jpg';
                }
                loadImageDiv.innerHTML += "<img src='"+img.profpic+"'>";
            });
            document.body.append(loadImageDiv);

        }
    }

    voteEngine();

    function voteEngine() {

        if(typeof(jsonResponse) != "undefined"){
            if(jsonResponse.length>0){
                nextProfile(jsonResponse);
            }else{
                alert('Crush acabaram, volte amanhã');
            }
        }else{

            getCrush(lastID,15, loadImages);
            if(lastID ==0){
                lastID = 15;
            }else{
                lastID = lastID + 15;
                // lastID = lastID * 2;
            }

            timer = setInterval(function () {
                if(typeof(jsonResponse) != "undefined"){
                    if(jsonResponse.length>0){
                        clearInterval(timer);
                        nextProfile(jsonResponse);
                    }else{
                        clearInterval(timer);
                        alert("Acabou-se os crushes, volte amanhã");
                    }
                }
            },350); // timer
        }


    }

    function nextProfile() {

        if(jsonResponse[0].profpic == null){
            var pic = "/img/perfil.jpg";
        }else{
            pic = jsonResponse[0].profpic;
        }
        var nome = jsonResponse[0].nome;
        var sexo = jsonResponse[0].sexo;
        var idade = jsonResponse[0].idade;
        var perfilURL = jsonResponse[0].perfilURL;
        var perfilID = jsonResponse[0].id;

        document.querySelector("#crushNome").innerHTML = nome;
        document.querySelector("#crushSexo").innerHTML = sexo;
        document.querySelector("#crushIdade").innerHTML = idade;
        document.querySelector(".perfilID").id = perfilID;
        document.querySelector(".crush .img img").src = pic;
        document.querySelector("#perfilURL").href ="perfil/"+perfilURL;
        jsonResponse.shift();

        if(jsonResponse.length==0){
            jsonResponse = undefined;
        }



    } /// end nextProfile



    var upButton = document.querySelector("input#upfoto");
    upButton.addEventListener('click',function () {
       upButton.onchange = function () {
           if(upButton.files.length>0){
               var subButton = document.querySelector("#buttonup");
               subButton.click();
           }
       }
    });



    var JSsubmit = document.querySelector("#formulario");
    JSsubmit.onsubmit = function (e) {
        e.preventDefault();
        var div = document.createElement("div");
        div.style="border-radius:3px;border:2px solid #fff;background-color:black;color:#fff;padding:6px;display:block;";
        div.innerText = "Aguarde, estamos processando sua foto";
        document.querySelector(".upload").append(div);
        var formulario = document.querySelector("#formulario");
        var formdata = new FormData(formulario);

        var ajax = new XMLHttpRequest();

        ajax.open("POST", "fotoupload.php", true);

        ajax.send(formdata);
        ajax.onreadystatechange = function () {
            if (ajax.status == 200 && ajax.readyState == 4) {
                div.remove();
                 var jsonResponse = JSON.parse(ajax.response);
                if(typeof(jsonResponse.url) != "undefined"){
                    var img = document.querySelector(".img img");
                    img.src = jsonResponse.url;
                }else{
                   alert(jsonResponse.error)
                }
            } else if (ajax.status > 400) {
                div.remove();
                alert('Ops,  houve um erro');
            }
        }
    };


    doAjax('get','/ajax/hasNotifications.php',null, notificationAlert);
    setInterval(function () {    doAjax('get','/ajax/hasNotifications.php',null, notificationAlert);},5000);
    function notificationAlert(json) {
        var jsonNoti = JSON.parse(json);
        if(jsonNoti.has){
            // tem notificação
            var notification = document.querySelector(".notification");
            notification.style.display ='inline-block';
            notification.onclick = function(){ window.location ='/notifications.php' };
            notification.style.display = 'block';
            var bgColor = notification.style.backgroundColor;
            setInterval(function () {
                if(notification.style.backgroundColor =='red'){
                    notification.style.backgroundColor = bgColor;
                }else if(notification.style.backgroundColor !='black') {
                    notification.style.backgroundColor = 'black';
                }else{
                    notification.style.backgroundColor = 'red';
                }
            },600);
        }

    } // end notificationAlert

</script>



</body>
</html>
