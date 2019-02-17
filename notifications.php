<?php
require_once("autoload.php");
$Config = new Config();
$cacheID = $Config->cacheID;

$SmartAction = new SmartAction();
if($SmartAction->verifyLogin()){
  $userID = $SmartAction->userID;
}else{
    header('Location: /?notofications=loginNeeded');
    die();
}

?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Notificações - Evo Crush</title>
    <meta name="description" content="Evo Crush é uma maneira evoluída de encontrar seu par ideal, será que seu crush está aqui?">
    <meta name="keywords" content="namoro, site de namoro,crush, encontar crush">
    <link rel="stylesheet" href="../css/style.css?c=<?= $cacheID  ?>">
    <link rel="shortcut icon" href="../img/favicon.png">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <script async="async" src="/js/script.js?c=<?= $cacheID  ?>"></script>
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
                    $Crud = new Crud();
                    function getMatches($Crud,$userID){
                         $sql = "SELECT u.nome,u.perfilURL, u.profpic, u.id, m.euID, m.emID FROM users u INNER JOIN matches m ON m.euID = u.id WHERE m.emID = :userID AND m.matched = :matched ORDER BY id DESC LIMIT 10";
                         $binds = ['userID'=> $userID,'matched'=>1];
                         $result = $Crud->select($sql,$binds);
                         if($result->rowCount() > 0){
                             $data = $result->fetchAll(PDO::FETCH_ASSOC);
                            foreach ($data as $valor){
                                $profpic = strip_tags( $valor['profpic']);
                                $perfilURL = strip_tags($valor['perfilURL']);
                                $nome = strip_tags( $valor['nome']);
                                if($profpic == null){
                                    $profpic = '/img/perfil.jpg';
                                }
                                echo "<div class='matchBlock'><img src='{$profpic}'><p>{$nome} Também gosta de você <a href='/perfil/{$perfilURL}'>visitar perfil</a> </p></div>";
                            }

                         }else{
                              echo "<h3>Você ainda não tem match</h3>";
                            }
                    } //end getMatches



                    function getReceivedMsgs($Crud,$userID){
                        $sql = "SELECT u.nome,u.profpic,u.perfilURL FROM msgstrocadas m INNER JOIN users u ON u.id = m.senderID WHERE m.receiverID = :userID GROUP BY m.senderID ORDER BY m.visto ASC LIMIT 10";
                        $binds = ['userID'=>$userID];
                        $result = $Crud->select($sql, $binds);
                        if($result->rowCount() > 0){
                            $data = $result->fetchAll(PDO::FETCH_ASSOC);
                            foreach ($data as $valor){
                                $nome = strip_tags($valor['nome']);
                                $perfilURL =  strip_tags($valor['perfilURL']);
                                $propic = strip_tags($valor['profpic']);
                                if($propic == null){
                                    $propic = '/img/perfil.jpg';
                                }
                                echo "<div class='blockMsgNotify'> <img src='{$propic}'> <p>{$nome} te enviou uma mensagem <a href='/perfil/{$perfilURL}'>clique aqui</a> para ver</p></div>";
                            }

                        }else{
                            echo "<h3>Sem mensagens</h3>";

                        }

                    } // end getReceiverMsgs



                    function updateMatches($userID,$Crud){
                        // configura como visto
                        $sql = "UPDATE matches SET vistoEM = '1' WHERE emID = :userID AND matched = '1' AND vistoEM = '0' LIMIT 10";
                        $binds = ['userID'=>$userID];
                        $Crud->update($sql,$binds);
                        // outro
                        $sql = "UPDATE matches SET vistoEU = '1' WHERE euID = :userID AND matched = '1' AND vistoEU = '0' LIMIT 10";
                        $binds = ['userID'=>$userID];
                        $Crud->update($sql,$binds);


                    } // end updateMatches

                    function updateMsgsRecebidas($userID,$Crud){
                        // se houver muitas msgs trocadas não atualizará todas, mas quando
                        // o usário ver a mensagem no perfil de quem envou, sim, todas são setadas como visto
                        $sql = "UPDATE msgstrocadas SET visto = '1' WHERE receiverID = :userID AND visto ='0' LIMIT 10";
                        $binds = ['userID'=>$userID];
                       $re = $Crud->update($sql,$binds);
                    }// end updateMsgsRecebidas


                    updateMatches($userID,$Crud);
                    updateMsgsRecebidas($userID,$Crud);

                    ?>
                </ul>
                <div id="x">[X]</div>
            </nav>
        </header> <!-- header nav -->
    </section>
    <div class="content">
        <div class="notify">
            <div class="matches">
                <?php
                getMatches($Crud,$userID);
                ?>
            </div>
            <div class="receivedMsgs">
                <?php
                getReceivedMsgs($Crud,$userID);
                ?>
            </div>
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
