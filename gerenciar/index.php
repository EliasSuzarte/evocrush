<?php
if(!empty($_COOKIE['mail']) && !empty($_COOKIE['pass'])) {
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

    /// acima dessa comentário, nada que um usuário não logado possa ver
    /// a verificação acontece a partir de agora
    $result = $SmartAction->getUserData($email, $senha);
    if ($result->rowCount() > 0) {
        $userData = $result->fetch();
        $nome = strip_tags($userData['nome']);
        $idade = (int) $userData['idade'];
        $sexo = strip_tags($userData['sexo']);
        $dataEmail = strip_tags($userData['email']);
       
        $id = strip_tags($userData['id']);
    } else {
        die('Ops, houve um falhe no nosso sistema');
    }
}else{
    header('Location: /?loginNeeded=gerenciar');
    die('não autenticado');
}

?>
<?php
if(!empty($_POST['nome']) && !empty($_POST['idade'])){
    $nome = strip_tags($_POST['nome']);
    $idade = (int) $_POST['idade'];
    if($idade>=18 && strlen($nome)>2 && strlen($nome) < 35 && $idade < 105){
        $sql = "UPDATE users SET nome = :nome, idade = :idade WHERE id = :id LIMIT 1";
        $binds = ['nome'=>$nome,'idade'=>$idade, 'id'=>$id];
        $Crud = new Crud();
        $result = $Crud->update($sql, $binds);
        if($result>0){
            echo "<script>alert('Atualizado com sucesso')</script>";
        }else{
            echo "<script>alert('Erro ao tentar atualizar')</script>";
        }
    }else{
        echo "<script>alert('Seu nome deve ter no mínimo 3 letras e idade superior a 17')</script>";
    }


}

/// update senha

if(!empty($_POST['npass']) && !empty($_POST['npassconfirm'])){
    $senha = $_POST['npass'];
    $npassconfirm = $_POST['npassconfirm'];
    if($npassconfirm == $senha){
            $senha = md5(trim($senha));
            $sql = "UPDATE users SET senha = :senha WHERE id = :id LIMIT 1";
            $binds = ['senha'=>$senha, 'id'=>$id];
            $Crud = new Crud();
            $result = $Crud->update($sql, $binds);
            if($result>0){
                $mes = time() + (30 * 24 * 60 * 60);
                setcookie("pass",$senha, $mes,"/");
                echo "<script>alert('Senha atualizada com sucesso')</script>";
            }else{
                echo "<script>alert('Erro ao tentar atualizar senha')</script>";
            }

    }else{
        echo "<script>alert('Ops, tenha atenção, a senha foi digitada incorretamente')</script>";

    }


}

if(!empty($_POST['nmail']) && !empty($_POST['nmailconfirm'])){
    $pMail = trim($_POST['nmail']);
    $nmailconfirm = trim($_POST['nmailconfirm']);

    if($pMail == $nmailconfirm){
        if(filter_var($pMail,FILTER_VALIDATE_EMAIL) && isMailAvailable($pMail)){
            $encryptedMail = md5($pMail);
            $sql = "UPDATE users SET email = :mail, confmail = null, cryptmail = :cryptmail WHERE id = :id LIMIT 1";
            $binds = ['mail'=>$pMail,'cryptmail'=>$encryptedMail, 'id'=>$id];
            $Crud = new Crud();
            $result = $Crud->update($sql, $binds);
            if($result > 0){
                $mes = time() + (30 * 24 * 60 * 60);
                setcookie("mail",$encryptedMail, $mes,"/");
                echo "<script>alert('Email atualizada com sucesso')</script>";
            }else{
                echo "<script>alert('Erro ao tentar atualizar email')</script>";
            }
        }else{
            echo "<script>alert('Esse email não é válido')</script>";
        }
    }else{
        echo "<script>alert('Atenção: Digite o email corretamente')</script>";
    }
}

function isMailAvailable($mail){
    $sql = "SELECT email FROM users WHERE email = :email";
    $binds = ['email'=>$mail];
    $Crud = new Crud();
    $re = $Crud->select($sql,$binds);
    if($re->rowCount()>0){
        return false; // email não disponível
    }else{
        return true;
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
    <link rel="stylesheet" href="../css/style.css?c=?c=<?= $cacheID ?>">
    <link rel="shortcut icon" href="../img/favicon.png">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <script src="../js/async.js?c=<?= $cacheID ?>"></script>
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
        <p>Aqui você pode melhorar seu perfil ou mesmo excluir sua conta</p>
        <p style="background-color: red;display: inline-block;padding: 2px"> Para adicionar novas fotos ou excluir <a style="background-color: #030e3d;color: #fff;border-radius: 3px" href="fotos.php">clique aqui</a></p>
        <form method="post" action="">
            <input type="hidden" name="nome-idade">
           <label>Nome:  <input type="text" name="nome" value="<?= $nome ?>"></label>
            <label>Idade: <input type="number" name="idade" value="<?= $idade ?>"></label>
            <button type="submit">Atualizar</button>
        </form>

        <form method="post" action="">
            <fieldset>
                <legend><h3>Atualizar Senha?</h3></legend>
                <input type="hidden" name="pass">
                <label>Nova senha: <input type="text" required="true" name="npass"></label>
                <label>Nova senha: <input type="text" required="true" name="npassconfirm"></label>
                <button type="submit">Atualizar Senha</button>
            </fieldset>
        </form>

        <form method="post" action="">
            <fieldset>
                <legend><h3>Atualizar Email <?= $dataEmail ?> ?</h3></legend>
                <input type="hidden" name="pass">
                <label>Novo Email: <input type="text" required="true" name="nmail"></label>
                <label>Nova Email: <input type="text" required="true" name="nmailconfirm"></label>
                <button type="submit">Atualizar Senha</button>
            </fieldset>
        </form>

        <form method="post" action="../delete.php">
            <fieldset>
                <legend>
                    <h3>Cuidado - Ação  Permanente</h3>
                    Todos seus dados serão apagados permanentemente, mas você poderá se cadastrar de novo quando quiser.
                </legend>
                <input type="hidden" name="delete"  value="true">
                <button  id="delete" class="red" type="submit">Deletar Perfil</button>
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