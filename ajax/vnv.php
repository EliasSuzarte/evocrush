<?php



if(isset($_GET['vote']) && !empty($_GET['id'])){
    $vote = (int) $_GET['vote'];
    $vote_em = (int) $_GET['id'];
    if($vote_em  == 0){
        die('Não existe usuário com id zero');
    }

    if($vote !=0 && $vote !=1){
        die("Voto deve ser um ou zero");
    }

}else{
    die('Você não votou em ninguém');
}

require_once "../autoload.php";
$SmartAction = new SmartAction();

$login = $SmartAction->verifyLogin();

if(!$login){
    die("Você não tem acesso");
}
 /// a partir daqui é por que está logado

$votes_limit = 120;
$userID = $SmartAction->userID;
if($userID == $vote_em){
    die('Você não pode votar em si mesmo');
}else{
    echo "uid {$userID} vote em {$vote_em}";
}


$Crud = new Crud();

$data = date("Y-m-d H:i:s");
$dozeHoras = strtotime("-12 hours",strtotime($data));
$lastHours = date("Y-m-d H:i:s",$dozeHoras);

$sql = "SELECT count(id) AS total FROM matches WHERE euID = :euID AND tempo > :tempo";
$binds = ['euID'=>$userID, 'tempo'=>$lastHours];

$result = $Crud->select($sql, $binds);
if($result->rowCount()>0){
    $dados = $result->fetch();
    $total = $dados['total'];
    if($total>= $votes_limit){
        echo "Você alcançou o limite de votos de hoje";
    }else{
      if(userExist($vote_em,$Crud) && !alreadyVoted($userID,$vote_em,$Crud)){

          if(!giveMatch($vote_em, $userID,$Crud)){
              $sql = "INSERT INTO matches(euID, vou, emID, vistoEU,vistoEM,matched) VALUES(:euID, :vou, :emID, '0','0','0')";
              $binds = ['euID'=>$userID,'vou'=>$vote,'emID'=>$vote_em];
              $result = $Crud->insert($sql,$binds);
              if($result > 0){
                  echo "votou com sucesso";
              }else{
                  echo "Erro ao tentar votar";
              }
          }else{
              // deu match, faz o update
              if($vote == 1){
                  $sql = "UPDATE matches SET matched = :matched WHERE emID = :emID AND euID = :euID";
                  $binds = ['emID'=>$userID,'matched'=>1,'euID'=>$vote_em];
                  $result = $Crud->insert($sql,$binds);
                  if($result > 0){
                      echo "matched com sucesso";
                  }else{
                      echo "Erro ao tentar criar match";
                  }
              }

              /// insert tooo
              $sql = "INSERT INTO matches(euID, vou, emID, vistoEU,vistoEM, matched) VALUES(:euID, :vou, :emID, '0','0','1')";
              $binds = ['euID'=>$userID,'vou'=>$vote,'emID'=>$vote_em];
              $result = $Crud->insert($sql,$binds);
              if($result > 0){
                  echo "votou com sucesso";
              }else{
                  echo "Erro ao tentar votar";
              }
          }

      }else{
          echo "Você tentou votar em um usuário que não existe ou em alguém que já votou antes";
      }


    }
}else{
    echo "Erro: SQL não retornou resultado";
}



function userExist($id,$Crud){
    $sql = "SELECT id FROM users WHERE id = :id";
    $binds  = ['id'=>$id];
    $result = $Crud->select($sql, $binds);
    if($result->rowCount()>0){
        return true;
    }else{
        return false;
    }
}

function alreadyVoted($euID,$em,$Crud){
    $sql = "SELECT vou FROM matches WHERE euID = :euID AND emID = :emID LIMIT 1";
    $binds = ['euID'=>$euID, 'emID'=>$em];
    $result = $Crud->select($sql,$binds);
    if($result->rowCount()>0){
        return true;
    }else{
        return false;
    }
}


function giveMatch($euID, $emID,$Crud){
    $sql = "SELECT id FROM matches WHERE euID = :euID AND emID = :emID AND vou = '1' LIMIT 1";
    $binds = ['euID'=>$euID, 'emID'=>$emID];
    $result = $Crud->select($sql,$binds);
    if($result->rowCount() > 0){
        return true;
    }else{
        return false;
    }

}


?>
