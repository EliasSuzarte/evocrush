<?php
require_once("../autoload.php");
$Config = new Config();
$cacheID = $Config->cacheID;
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Suporte - Evo Crush</title>
    <meta name="description" content="Evo Crush suporte, entre em contato com a maior rede social de namoro evolutivo">
    <meta name="keywords" content="namoro, site de namoro,crush, suporte, rede social">
    <link rel="stylesheet" href="/css/style.css?c=<?= $cacheID ?>">
    <link rel="shortcut icon" href="img/favicon.png">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <script src="/js/async.js?c=<?= $cacheID ?>"></script>
    <script src="/js/script.js?c=<?= $cacheID ?>" async="async"></script>
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
        <h2>Suporte - Contato</h2>
            <?php
            if(!empty($_POST)){
                echo "<div style='padding: 6px;border: 1px solid #ff6452'>";
                if(!empty($_POST['nome']) && !empty($_POST['msg']) && !empty($_POST['email'])){
                    $nome = strip_tags(trim($_POST['nome']));
                    $msg  = strip_tags(trim($_POST['msg']));
                    $email = $_POST['email'];
                    if(strlen($nome) < 1 || strlen($msg) < 15 || !filter_var($email,FILTER_VALIDATE_EMAIL)){
                        echo "<p style='color: darkred'>Sua mensagem ou nome são muito pequeno, ou seu e-mail não é válido <a href=\"javascript:history.back()\">Editar Mensagem Anterior</a> .</p></div>";
                    }else{
                        if(strlen($nome) > 80){
                            $nome = substr($nome,0,79).'[...]';
                        }elseif (strlen($msg) > 4860){
                            $msg = substr($msg,0, 4800).'[... mensagem cortada]';
                        }
                        $Crud = new Crud();
                        $sql = "INSERT INTO contato(nome, mensagem, email) VALUES(:nome, :mensagem, :email)";
                        $binds = ['nome'=>$nome, 'mensagem'=>$msg,'email'=>$email];
                        $re = $Crud->insert($sql,$binds);
                        if($re > 0){
                            echo "<p style='color:#1bab59'>Parabéns {$nome} sua mensagem foi  enviada, em breve entraremos em contato.</p></div>";
                            $MailHandler = new MailHandler();
                            $MailHandler->supportSend();
                        }else{
                            echo "{$nome} houve um erro, não consiguimos receber sua mensagem, tente entrar em contto conosco mais tarde</div>";
                            new ErrorReports("Erro ao tentar enviar mensagem pela página de suporte");
                        }
                    }
                }else{
                    echo "Por favor preecha todos os campos</div>";
                }

            }
            ?>

        <form method="post" action="">
            <label for="nome">Nome:</label>
            <input type="text" id="nome" required="true" name="nome" placeholder="Nome">
            <label for="email">Email</label>
            <input type="text" id="email" required="true" name="email" placeholder="Email">
            <label>Sua mensagem: <textarea style="min-height: 120px;min-width: 80%;border:1px solid #ff6452" required="true" placeholder="Escreva sua mensagem aqui" type="text" name="msg"></textarea></label>
            <input type="submit" value="Enviar mensagem">

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

