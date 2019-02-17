<?php
require_once("../autoload.php");
$Config = new Config();
$cacheID =$Config->cacheID;
$URI = explode("/", $_SERVER['REQUEST_URI']);
if(!empty($URI[2])){
    $userPerfil = $URI[2];
 
}else{
    header("Location: /?wrongProfile");
    die();
}

$SmartAction = new SmartAction();
$SmartAction->verifyLogin(); // mesmo que não usado, pelo menos disponibiliza o $userID
$userID = $SmartAction->userID;
$Crud = new Crud();

function updateMsgsRecebidas($userID, $senderID, $Crud){
    // se houver muitas msgs trocadas não atualizará todas, mas quando
    // o usário ver a mensagem no perfil de quem envou, sim, todas são setadas como visto
    $sql = "UPDATE msgstrocadas SET visto = '1' WHERE receiverID = :userID AND senderID = :senderID AND visto = '0'";
    $binds = ['userID'=>$userID,'senderID'=>$senderID];
    $Crud->update($sql,$binds);
}// end updateMsgsRecebidas



$result = $SmartAction->getProfile($userPerfil);


if ($result->rowCount() > 0) {
    $userData = $result->fetch();
    $nome = strip_tags(trim($userData['nome']));
    $short_name = substr($nome,0, strpos($nome, " "));

    if(strlen($short_name)>15){
        $short_name = substr($short_name, 0,25);
    }elseif (strlen($short_name)<1){
      // caso o nome já fosse unico, sem sobrenome, ele ficará zerado após passar por subtrs acima
        $short_name = $nome;
    }

    $sexo = strip_tags($userData['sexo']);
    $idade = (int) $userData['idade'];
    $perfilURL = strip_tags($userData['perfilURL']);
    $pic = strip_tags($userData['profpic']);
    if($pic == null){
        $pic = 'img/perfil.jpg';
    }
    $receiverID = (int) $userData['id'];
    $perfilID = $receiverID;
    updateMsgsRecebidas($userID,$receiverID, $Crud); /// quando o usuário vistar a pagina do outro
    /// caso ele tenha recebido msgs do mesmo, essas mesagens será setadas como vista, e não ficará mais na
    /// notificação do usuário que recebeu, como mensagens não vista e sim como vista
} else {
    header("HTTP/1.0 404 Pagina Nao Encontrada");
    require_once("../404.php");
    die();

}


?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Perfil de <?= $nome ?> - EvoCrush Rede Social</title>
    <meta name="description" content="Perfil de <?= $nome ?> no Evo Crush, a rede social de quem busca a evolução">
    <meta name="keywords" content="rede social, site de namoro,crush, <?= $nome ?>, encontar crush">
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

        <section class="perfil" style="display: block">
            <div class="img">
                <div class="name"><?= $nome ?></div>
                <img src="../<?= $pic ?>" width="210">
                <div class="sexo">Sexo: <?= $sexo ?></div>
                <div class="idade">Idade: <?= $idade ?></div>
            </div>
        </section> <!-- perfil end -->


        <section class="sendmsg">
            <div class="chatPerfil">
            </div>
            <form method="post" action="chatPerfil.php">
                <input type="hidden" name="receiverID" value="<?= $receiverID ?>">
                <textarea name="msg" placeholder="Escreva uma mensagem para <?= $nome ?>" value=""></textarea>
                <button id="JSsubmit" type="submit">Enviar</button>
            </form>
        </section>
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

    var perfilID = <?= $perfilID ?>;
    var perfilNome = "<?= $short_name ?>";

    var JSsubmit = document.querySelector("#JSsubmit");


    window.onkeydown = function (e) {
        if(e.key == 'Enter' || e.keyCode == 13){
           prepareThings();
        }
    };


    JSsubmit.addEventListener("click", function (e) {
        e.preventDefault();
        prepareThings();
        
    });
    
    function prepareThings() {
        var msg = document.querySelector("textarea").value;
        if (msg.length > 0) {
            var receiverID = <?= $receiverID ?>;

            sendMSG(msg, receiverID);
        } else {
            alert("Mensagem vazia");
        }
    }

    function sendMSG(msg, receiverID) {
        document.querySelector("textarea").value = '';
        var ajax = new XMLHttpRequest();
        if (ajax) {
            ajax.open("POST", "chatPerfil.php", true);
            ajax.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
            ajax.send('msg=' + msg + '&receiverID=' + receiverID);
            ajax.onreadystatechange = function () {
                if (ajax.status == 200 && ajax.readyState == 4) {
                    if(ajax.response == '[Sucesso]'){
                        document.querySelector("textarea").value = '';
                    }
                } else if (ajax.status > 400) {
                    alert('Ops, houve um erro, tente de novo');
                }
            }

        } else {
            alert("Seu navegador não suporta AJAX, atualize-o ou use outro navegador");
        }
    } /// end sendMSG
    
    
    function getMSG(perfilID) {
        var ajax = new XMLHttpRequest();
        if (ajax) {
            ajax.open("POST", "chatPerfil.php", true);
            ajax.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
            ajax.send('getmsg=true&perfilID='+perfilID);
            ajax.onreadystatechange = function () {
                if (ajax.status == 200 && ajax.readyState == 4) {
                    jschild = JSON.parse(ajax.response);
                     if(jschild.error == undefined){
                         implementHTML(jschild)
                     }else{
                        if(jschild.error =='você não está logado'){
                            document.querySelector(".chatPerfil").innerHTML ='<h3>Você precisa logar para enviar mensagem</h3>';
                            clearInterval(timer);
                        }
                     }

                    if(ajax.response == '[Sucesso]'){
                        document.querySelector("textarea").value = '';
                    }
                } else if (ajax.status > 400) {
                    alert('Ops, houve um erro, tente de novo');
                }
            }

        } else {
            alert("Seu navegador não suporta AJAX, atualize-o ou use outro navegador");
        }

    } // end getMSG

    getMSG(perfilID); // start chat

    timer = setInterval(function () {
       if(document.visibilityState =='visible'){
           getMSG(perfilID);
       }
    },2000);


    function implementHTML(json) {
        var chatPerfil = document.querySelector(".chatPerfil");
        chatPerfil.innerHTML = '';
          json.forEach(function (j) {
              if(j.senderID == perfilID){
                  var div = document.createElement("div");
                  var texto = `<b>${perfilNome}</b> disse: ${j.msg}`;
                  div.innerHTML = texto;
                  chatPerfil.append(div);
              }else{
                  var div = document.createElement("div");
                  var texto = `<b>Você</b> disse: ${j.msg}`;
                  div.innerHTML = texto;
                  chatPerfil.append(div);
              }
          });
        document.querySelector(".chatPerfil").scrollTo(1,999999);

    } // end implementHTML
    
</script>

</body>
</html>
