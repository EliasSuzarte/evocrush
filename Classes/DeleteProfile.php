<?php

class DeleteProfile{
    private $Crud,$SmartAction,$userID;
    public $msgs,$deleted;
    public function __construct($userID){
        $this->userID = $userID;
        $this->deleted = false;
        $this->msgs = [];
        $this->Crud = new Crud();
        $this->SmartAction = new SmartAction();
        self::initDeletion();
    }

    private function initDeletion(){
        /// deletar chat
        $sql = "SELECT id FROM chat WHERE userID = :userID LIMIT 1";
        $binds = ['userID'=>$this->userID];
        if($this->hasData($sql,$binds)){
            self::deleteChat();
        }


        // deletar matches / vou nao vou
        $sql = "SELECT id FROM matches WHERE euID = :userID OR emID = :userID LIMIT 1";
        $binds = ['userID'=>$this->userID];
        if($this->hasData($sql,$binds)){
            self::deleteMatches();
        }


        // deletar mensagens trocadas
        $sql = "SELECT id FROM msgstrocadas WHERE senderID = :userID OR receiverID = :userID LIMIT 1";
        $binds = ['userID'=>$this->userID];
        if($this->hasData($sql,$binds)){
            self::deleteMsgsTrocadas();
        }

        self::deleteFotos();
        self::deleteUsers();
        foreach ($this->msgs as $msg){
            new ErrorReports($msg);
        }





    }


    private function hasData($sql, $binds){
        $result = $this->Crud->select($sql, $binds);
        if($result->rowCount()>0){
            return true;
        }else{
            return false;
        }
    } // hasData

    private function deleteChat(){
        $sql = "DELETE FROM chat WHERE userID = :id";
        $binds = ['id'=>$this->userID];
        $delChat = $this->Crud->delete($sql, $binds);
        if($delChat < 1){
            $this->msgs[] = "Erro ao apagar mensagens de chat {$this->userID}";
        }
    } // end deleteChat


    function deleteUsers(){
        $sql = "DELETE FROM users WHERE id = :uid";
        $binds = ['uid'=>$this->userID];
        $del = $this->Crud->delete($sql, $binds);
        if($del < 1){
            $this->msgs[] = "Erro ao tentar deletar perfil id: {$this->userID}";
        }else{
            $this->deleted = true;
        }

    } /// end deleteUsers




    private function deleteMatches(){
        $sql = "DELETE FROM matches WHERE emID = :id OR  euID = :id";
        $binds = ['id'=>$this->userID];
        $delChat = $this->Crud->delete($sql, $binds);
        if($delChat < 1){
            $this->msgs[] = "Ops, não consiguimos deletar  os matches  -> id {$this->userID}";
        }

    } /// end deleteMatches


    private function deleteMsgsTrocadas(){
        $sql = "DELETE FROM msgstrocadas WHERE receiverID = :id OR  senderID = :id";
        $binds = ['id'=>$this->userID];
        $delChat = $this->Crud->delete($sql, $binds);
        if($delChat < 1){
            $this->msgs[] = "Ops, não consiguimos deletar as mensagens trocadas id {$this->userID}";
        }

    } // end msgstrocadas


    private function deleteFotos(){
        $sql = "SELECT fotoURL FROM fotos WHERE userID = :userID";
        $binds = ['userID'=>$this->userID];
        $result = $this->Crud->select($sql,$binds);
        if($result->rowCount() > 0){
            $datas = $result->fetchAll(PDO::FETCH_ASSOC);
            $fotos = [];
           foreach ($datas as $valor){
               $fotos[] = $valor['fotoURL'];
           }
            self::deleteFromDisk($fotos);

            /// deleta os registro da tabela fotos
            $sql = "DELETE FROM fotos WHERE userID = :id";
            $binds = ['id'=>$this->userID];
            $delChat = $this->Crud->delete($sql, $binds);
            if($delChat < 1){
                $this->msgs[] = "Ops, não consiguimos deletar registro de fotos id {$this->userID}";
            }
        }


    } // end deleteFotos



    private function deleteFromDisk($files, $back = false){
        if(is_array($files)){
            foreach ($files as $file){
                if(substr($file,0,8) == "uploads/"){
                    if($back){
                        $file = "{$back}{$file}";
                    }
                    if(file_exists($file)){
                        $status = unlink($file);
                        if(!$status){
                            $this->msgs[] = "erro ao deletar o arquivo {$file}";
                        }
                    }else{
                        $this->msgs[] = "Arquivo não existe {$file}";
                    }
                }else{
                    $this->msgs[] = "Você não ter permissão para deletar arquivos dessa pasta: {$file}";
                }
            }
        }
    } /// deleteFromDisk




}