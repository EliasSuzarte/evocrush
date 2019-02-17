<?php
if(!empty($_COOKIE['senha'])){
        $senha = $_COOKIE['senha'];
        if($senha !='senha123'){
            die('Pagina em desenvolvimento');
        }
}else{
    die('Pagina em desenvolvimento');
}

if(!empty($_POST['perfil'])){
    $perfil = $_POST['perfil'];
    require 'autoload.php';
    $Crud = new Crud();
    $sql = "SELECT id FROM users WHERE perfilURL = :perfil LIMIT 1";
    $binds = ['perfil'=>$perfil];
    $re = $Crud->select($sql,$binds);
    if($re->rowCount() > 0){
        $datas  = $re->fetch();
        $id = (int) $datas['id'];
        $DeletePerfil = new DeleteProfile($id);
        if($DeletePerfil->deleted){
            echo "<h2>Perfil deletado com sucesso</h2>";
            if(!empty($DeletePerfil->msg)){
                echo "<br>Tem mensagens";
                foreach ($DeletePerfil->msg as $m){
                    echo "{$m}<br>";
                }
            }
        }else{
            echo "Erro ao tentar deletar perfil";
        }
    }else{
        echo "Não foi encontrado o perfil {$perfil}";
    }


}
?>
<html>
<head>
    <meta charset="UTF-8">
    <title>Admin Panel</title>
</head>
<body>
<form method="post" action="">
    <input type="perfil" name="perfil">
    <button>Deletar</button>
</form>
</body>
</html>
