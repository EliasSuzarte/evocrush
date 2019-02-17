<?php
/// CRIA NOVA SENHA - RECUPERAÇÂO DE CONTA
if(!empty($_GET['token'])){
 $token = $_GET['token'];

}else{
   $token = 0;
}

if(!empty($_POST['token'])){
    $token = $_POST['token'];
}

/// ATENÇÃO O CÓDIGO ACIMA PODE SER CONSUFOSO, MAS DEVE SER MANTIDO ASSIM
/// PRIMEIRAMENTE O TOKEN É PEGO PELO $_GET DEPOIS PELO $_POST, SE ELE NÃO FOR PASSADO INICIALMENTE PELO GET
/// SERÁ SETADO COM 0

?>
<?php
require_once '../autoload.php';
$Config = new Config();
$cacheID = $Config->cacheID;

?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Recuperar - Evo Crush</title>
    <meta name="description" content="Evo Crush recupere sua conta, gere uma nova senha e acesso ao EvoCrush.">
    <meta name="keywords" content="namoro, site de namoro,recuperar conta, encontar crush">
    <link rel="stylesheet" href="/css/style.css?c=<?= $cacheID ?>">
    <link rel="shortcut icon" href="/img/favicon.png">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex">
    <script src="/js/script.js?c=<?= $cacheID ?>"  async="async"></script>

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
        <div class="recover">
            <?php

              if(!empty($_POST['senha']) && !empty($_POST['nsenha'])){
                   $senha = trim($_POST['senha']);
                   $nsenha = trim($_POST['nsenha']);
                   if($senha == $nsenha){
                      if($token == 0){
                          echo "Ops, seu token não á válido";
                      }else{
                          $truePass = md5($senha);
                          $sql = "UPDATE users set senha = :senha, confmail = '1' WHERE confmailtoken = :token LIMIT 1";
                          $binds = ['token'=>$token, 'senha'=>$truePass];
                          $Crud = new Crud();
                          $re = $Crud->update($sql, $binds);
                          if($re > 0){
                              echo "<div class='success'>Senha atualizada com sucesso <a href='/login.php'>Clique Aqui</a> para fazer login</div>";
                              $sql = "UPDATE users set confmailtoken = :newToken WHERE confmailtoken = :token LIMIT 1";
                              $newToken = md5("{$token}".rand(1,999)."oxer");
                              $binds = ['newToken'=>$newToken, 'token'=>$token];
                              $Crud->update($sql, $binds);
                           
                          }else{
                              // pode acontecer porque o token é invalido ou a senha é igual a anterior
                              echo "<div class='alert'>Ops, parece que seu token não é válido</div>";
                          }

                      }
                   }else{
                       echo "<div class='alert'>Ops, a senha devem ser digitada igualmente nos dois campos</div>";
                   }
               }elseif(!empty($_POST['senha']) || !empty($_POST['nsenha'])){
                  echo "<div class='alert'>Ops, você precisa digitar e repetir a mesma senha escolhida</div>";
              }

            ?>
            <h1>Nova Senha</h1>
            <p>Escolha uma nova senha</p>
            <form method="post" action="">
                <input type="hidden" value="<?= $token ?>" name="token">
                <label style="display: block;margin: 2px">Digite a nova senha: <input required="true" type="password" name="senha" placeholder="Senha"></label>
                <label style="display: block;margin: 2px">Digite a nova senha: <input type="password" name="nsenha" placeholder="Digite novamente"></label>
                <input id="btnrec" type="submit" value="Mudar Senha">
            </form>
        </div>
    </div>


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

