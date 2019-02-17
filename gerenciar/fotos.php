<?php
if (!empty($_COOKIE['mail']) && !empty($_COOKIE['pass'])) {
    $senha = $_COOKIE['pass'];
    $email = $_COOKIE['mail'];
    require_once("../autoload.php");
    $Config = new Config();
    $cacheID = $Config->cacheID;

    $SmartAction = new SmartAction();

    $access = $SmartAction->verifyLogin($email, $senha);
    if ($access == 0) {
        header("Location: /?loginNeeded");
        die();
    }

    $sql = "SELECT profpic FROM users WHERE cryptmail = :mail AND senha = :pass";
    $binds = ['mail' => $email, 'pass' => $senha];
    $Crud = new Crud();
    $result = $Crud->select($sql, $binds);
    if ($result->rowCount() > 0) {
        $dados = $result->fetch(PDO::FETCH_ASSOC);
        $profpic = strip_tags($dados['profpic']);
    } else {
        $profpic = 'nada0';
    }
}else{
    header('Location: /?loginNeeded=geranciar/foto');
    die('sem autorização');
}
/// acima dessa comentário, nada que um usuário não logado possa ver
/// a verificação acontece a partir de agora
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Gerenciar Fotos - Evo Crush</title>
    <meta name="description" content="Evo Crush é uma maneira evoluída de encontrar seu par ideal, será que seu crush está aqui?">
    <meta name="keywords" content="namoro, site de namoro,crush, encontar crush">
    <link rel="stylesheet" href="../css/style.css?c=<?= $cacheID ?>">
    <link rel="shortcut icon" href="../img/favicon.png">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <script src="/js/async.js?c=<?= $cacheID ?>"></script>
    <script async="async" src="/js/script.js?c=<?= $cacheID ?>"></script>

    <script>
        function displayMSG(msg,duration = 5) {
            duration = duration * 1000;
            var divu  = document.createElement("div");
            divu.style = 'min-width:250px;padding:8px;position:relative;background-color:#653496;text-align:center;float:left';
            divu.innerHTML = msg;
            document.body.prepend(divu);
            setInterval(function () {
                divu.remove();
            },duration);
            divu.onclick = function () {  divu.remove()};
        } // end displayMSG
    </script>

    <style>
        .gerenciar .fotos{
            background-color: red;
        }
        .fotos .img{
            background-color: #00c300;
            width: 150px;
            height: 150px;
            padding: 5px;
            float: left;
        }

        .fotos .img img{
            width: 150px;
            height: 150px;
        }

        #delFoto{
            padding: 5px;
            display: block;
            background-color: black;
            margin-top: -30px;
            position: absolute;
            border-radius: 3px;
        }
        #delFoto a{
            color: #fff;
            text-decoration: none;
        }

    </style>

</head>
<body>



<?php
$result = $SmartAction->getUserData($email, $senha);
if ($result->rowCount() > 0) {
    $userData = $result->fetch();
    $nome = strip_tags($userData['nome']);
    $userID = (int) $userData['id'];
} else {
    die('Ops, houve um falhe no nosso sistema');
}

$Crud = new Crud();

if(!empty($_GET['delete'])){
    $address = $_GET['delete'];
    if($address == $profpic){
        // foto do perfil - nesse necessário updata também da users
        $sql = "UPDATE users SET profpic = :profpic WHERE id = :userID";
        $binds = ['profpic'=>null,'userID'=>$userID];
       $result =  $Crud->update($sql,$binds);
       if($result < 1){
           new ErrorReports("Erro ao tentar apagar foto do perfil do banco de dados {$address}");
       }
    }

    fotoDelete($address,$userID,$Crud);
}

function fotoDelete($address,$userID,$Crud){
    // antes de deleter é necessário verificar se a foto existe
    // e principalmente se pertence ao usuário que está deletando;
    $permition = hasPermitionToDelete($address,$userID, $Crud);
    if($permition){
        $addressBack = "../{$address}";
        if(file_exists($addressBack)){
            $s = unlink($addressBack);
            if($s){
                // agora deleta do banco de dados;
                deleteFromDB($address,$userID,$Crud);
            }else{
                echo "Erro ao deletar";
            }

        }else{
            echo "<script> displayMSG('Ops, não encontramos esse arquivo');</script>";

        }
    }else{
        echo "<script> displayMSG('Ops, não encontramos esse arquivo!');</script>";
    }
}

function hasPermitionToDelete($address, $userID,$Crud){
    $sql = "SELECT fotoURL FROM fotos WHERE userID = :userID AND fotoURL = :fotoURL";
    $binds = ['userID'=>$userID, 'fotoURL'=>$address];
    $result = $Crud->select($sql, $binds);
    if($result->rowCount()>0){
        return true;
    }else{
        return false;
    }
}

function deleteFromDB($address, $userID, $Crud){
    $sql = "DELETE FROM fotos WHERE userID = :userID AND fotoURL = :fotoURL";
    $binds = ['fotoURL'=>$address,'userID'=>$userID];
    $result = $Crud->delete($sql, $binds);
    if($result < 1){
        $erro  = "Erro: foto foi deletada do servidor, mas não foi possível remover do banco de dados foto : [$address]";
        new ErrorReports($erro);
    }else{
        echo "<script> displayMSG('Foto deletada com sucesso');</script>";
    }
}



?>


<div class="container">
    <section id="secnav">
        <header>
            <nav>
                <div id="toggle"></div>
                <ul>
                    <?php
                    require_once('../autoload.php');
                    $Config = new Config();
                    $Config->getMenu();
                    ?>
                </ul>
                <div id="x">[X]</div>
            </nav>
        </header> <!-- header nav -->
    </section>


    <section class="gerenciar">
        <h2>Gerenciar Fotos</h2>
        <p>Aqui você poderá enviar novas fotos ou apagar as que desejar.</p>
            <form method="post" action="fotoUpload.php" id="formulario" enctype="multipart/form-data">
                <label><input type="file" name="foto"></label>
                <button type="submit">Enviar Foto</button>
            </form>


        <div class="fotos">
                <?php
                 $sql = "SELECT fotoURL FROM fotos WHERE userID = :userID order by id desc ";
                 $binds = ['userID'=>$userID];
                 $result = $Crud->select($sql,$binds);
                 $total = $result->rowCount();
                 if($total > 0){
                     $fotos = $result->fetchAll(PDO::FETCH_ASSOC);
                     for ($i = 0; $i < $total;$i++){
                         $foto = strip_tags($fotos[$i]['fotoURL']);
                         echo "<div class='img'>";
                         echo "<img src='/{$foto}'>";
                         if($foto == $profpic){
                             echo "<div id='delFoto' style='background-color: red;'><a href='?delete={$foto}'>Deletar Foto de Perfil</a></div>";

                         }else{
                             echo "<div id='delFoto'><a href='?delete={$foto}'>Delete</a></div>";
                         }
                         echo "</div>";
                     }
                 }else{
                     // sem fotos
                 }

                ?>

        </div>
    </section> <!-- gerenciar -->


</div> <!-- container-->

<section id="foot">
    <footer>
        <?php
        $Config->getFooter();
        ?>
    </footer>
</section>

<script>

    var upButton = document.querySelector("input[type='file']");
    upButton.addEventListener('click',function () {
        upButton.onchange = function () {
            if(upButton.files.length>0){
                var subButton = document.querySelector("button");
                subButton.click();
            }
        }
    });



    var JSsubmit = document.querySelector("#formulario");
    JSsubmit.onsubmit = function (e) {
        e.preventDefault();
        var div = document.createElement("div");
        div.style="position:absolute;border-radius:3px;border:2px solid #fff;background-color:black;color:#fff;padding:6px;margin-top:50px";
        div.innerText = "Aguarde, estamos processando sua foto";
        document.body.prepend(div);
        var formulario = document.querySelector("#formulario");
        var formdata = new FormData(formulario);

        var ajax = new XMLHttpRequest();

        ajax.open("POST", "uploadFoto.php", true);

        ajax.send(formdata);
        ajax.onreadystatechange = function () {
            if (ajax.status == 200 && ajax.readyState == 4) {
                div.remove();
                var jsonResponse = JSON.parse(ajax.response);
                if(typeof(jsonResponse.url) != "undefined"){
                    var divImg = document.createElement('div');
                    divImg.classList.add('img');
                    divImg.innerHTML = "<img src='/"+jsonResponse.url+"'>";
                    var fotos = document.querySelector(".fotos");
                    fotos.prepend(divImg);
                }else{
                    alert(jsonResponse.error)
                }
            } else if (ajax.status > 400) {
                div.remove();
                alert('Ops,  houve um erro');
            }
        }
    };

</script>
</body>
</html>