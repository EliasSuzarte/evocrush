<?php
date_default_timezone_set("America/Sao_Paulo");
require_once('../autoload.php');
$Config = new Config();
$cacheID = $Config->cacheID;
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Pessoas Online - Evo Crush</title>
    <meta name="description" content="Pessoas buscando a evolução espiritual e moral online no Evo Crush">
    <meta name="keywords" content="namoro, site de namoro,crush, encontar crush, pessoas online,evolução espiritual, evoluir moralmente">
    <link rel="stylesheet" href="../css/style.css?c=<?= $cacheID ?>">
    <link rel="shortcut icon" href="../img/favicon.png">
    <script async="true" src="/js/script.js?c=<?= $cacheID ?>"></script>
    <meta name="viewport" content="width=device-width, initial-scale=1">
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


    <section class="online" style="margin:0 5% 0 15%;width:80%">
        <?php
        $SmartAction = new SmartAction();
        $result = $SmartAction->getOnline(50);


       if($result->rowCount()>0){
           $totoRe = $result->rowCount();
           $online = $result->fetchAll(PDO::FETCH_ASSOC);
           foreach ($online as $po){
               writeOnlie($po);
           }

       }

       function writeOnlie($po){
           $nome = strip_tags($po['nome']);
           $picurl = strip_tags($po['profpic']);
           $online = strip_tags($po['online']);
           $perfilURL = strip_tags($po['perfilURL']);

           $minusFive = strtotime("-5 minutes", strtotime(date('Y-m-d H:i:s')));

           if(strtotime($online) < $minusFive){
               $online = "notOnline";
           }else{
               $online = "isOnline";
           }
           if($picurl == null){
               $picurl = "../img/perfil.jpg";
           }else{
               $picurl = "../".$picurl;
           }
           echo "<div class='profileBlock'>";
           echo "<div class='{$online}'></div>";
           echo  "<a href='/perfil/{$perfilURL}'>";
           echo "<img title='{$nome}' alt='{$nome}' src='{$picurl}'></a>";
           echo "<div class='info'>{$nome}</div>";
           echo "</div>";
       }

        ?>
    </section>


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
