<?php
class SmartAction{
    private $Crud;
    public $userID;

    function verifyLogin($cryptMail = false, $senha = false){
        if($cryptMail == false || $senha == false){
            if(!empty($_COOKIE['pass']) && !empty($_COOKIE['mail'])){
                $binds = ['cryptmail'=>$_COOKIE['mail'], 'senha'=>$_COOKIE['pass']];
            }else{
                $binds = ['cryptmail'=>'nada', 'senha'=>'nada'];
            }
        }else{
            $binds = ['cryptmail'=>$cryptMail, 'senha'=>$senha];

        }
        $this->setCrud(); // deixa $this->Crud disponível
        //$sql = "SELECT nome, idade, sexo FROM users WHERE cryptmail = :cryptmail AND senha = :senha";
        $sql = "SELECT id FROM users WHERE cryptmail = :cryptmail AND senha = :senha";

        $stmtResult = $this->Crud->select($sql,$binds);
        $access = (int) $stmtResult->rowCount();
        if($access){
            $dados =  $stmtResult->fetch();
            $this->userID = $dados['id'];
        }
        return $access;
    }


    private function setCrud(){
        if(empty($this->Crud)){
            $this->Crud = new Crud();
        }
    } // end setCrud


    public function getUserData($cryptMail, $senha){
        $this->setCrud();
        $sql = "SELECT * FROM users WHERE cryptmail = :cryptmail AND senha = :senha LIMIT 1";
        $binds = ['cryptmail'=>$cryptMail, 'senha'=>$senha];
        return $this->Crud->select($sql,$binds);

    }  // end getUserData




    public function getCrushData($id, $limit = 15){
        if($limit>30) {
            $limit = 15;
        }
        $limit = (int) $limit;
        $this->setCrud();
        $sql = "SELECT nome,idade,sexo, profpic,perfilURL,id FROM users WHERE id > :id AND id != :userID ORDER BY id DESC LIMIT $limit";
        $binds = ['id'=>$id,'userID'=>$this->userID];
        return $this->Crud->select($sql,$binds);
    }




    public function getProfile($perfilURL){
        $this->setCrud();
        $sql = "SELECT nome,idade,sexo, profpic,perfilURL, profpic,id FROM users WHERE perfilURL = :perfilURL LIMIT 1";
        $binds = ['perfilURL'=>$perfilURL];
        return $this->Crud->select($sql,$binds);
    }




    public function getOnline($limit){
        $limit = (int) $limit;
        $this->setCrud();
        $sql = "SELECT nome,idade,sexo, profpic,perfilURL,online,id FROM users WHERE id > :id ORDER BY online DESC LIMIT $limit";
        $binds = ['id'=>0];
        return $this->Crud->select($sql,$binds);
    } /// end getOnline






    public function genereteProfileURL($nome, $idade){

        /// cuidado, esse código é importante e está meio bagunçado
        ///
        $this->setCrud();
        $sql = "SELECT perfilURL from users WHERE perfilURL = :perfilurl LIMIT 1";
        $url = $this->makeUrl($nome);
        $binds = ['perfilurl'=>$url];

        $result = $this->Crud->select($sql, $binds);

        if($result->rowCount()>0){
            $perfil = $this->makeUrl("{$nome}.{$idade}");
            $binds = ['perfilurl'=>$perfil];
            $result2 = $this->Crud->select($sql, $binds);
            if($result2->rowCount()>0){
                $rand  = rand(1,9998);
                $rand2  = rand(1,9998);
                $mkt = mktime();
                $finalTry = $this->makeUrl("{$nome}.{$idade}-{$rand}-{$mkt}-{$rand2}");
                return $finalTry;

            }else{
                return $perfil;
            }

        }else{
            return $url;
        }

    }   // genereteProfileURl end




    public function makeUrl($title){

        $alphabet = "a b c d e f g h i j k l m n o p q r s t u v w x y z 0 1 2 3 4 5 6 7 8 9";
        $ntitle = strtolower($title);
        $ntitle = str_replace(array("ã",'á','à'),'a',$ntitle);
        $ntitle = str_replace("ç",'c',$ntitle);
        $ntitle = str_replace(array('é','è','ë','ê'),'e',$ntitle);
        $ntitle = str_replace(array('í','ì','ï','î'),'i',$ntitle);
        $ntitle = str_replace(array('ó','ò','ô','õ'),'o',$ntitle);
        $ntitle = str_replace(array('ú','ù','ü'),'u',$ntitle);
        $ntitle = str_replace('ñ','n',$ntitle);
        $tam = strlen($ntitle);
        $clear_title =null;
        $permitidos = explode(" ",$alphabet);
        for($i=0;$i<$tam;$i++){
            $caractere = substr($ntitle,$i,1);
            if($caractere == ' ' or in_array($caractere,$permitidos)){
                $clear_title .= $caractere;
            }
        }
        $ntitle = str_replace(array("     ","    ","   ","  "," ","----","---","--"),"-",trim($clear_title));
        return $ntitle;

    } // makeUrl


    public function fileDelete($files, $back = false){
        if(is_array($files)){
            foreach ($files as $file){
                if(substr($file,0,8) == "uploads/"){
                    if($back){
                        $file = "{$back}{$file}";
                    }
                    if(file_exists($file)){
                        $status = unlink($file);
                        if(!$status){
                            echo "erro ao deletar o arquivo {$file}";
                        }else{
                            echo "Arquivo deletado com sucesso";
                        }
                    }else{
                        echo "Arquivo não existe";
                    }
                }else{
                    die("Você não ter permissão para deletar arquivos dessa pasta");
                }
            }
        }
    }







} // end classs SmartAction