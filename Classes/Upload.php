<?php

class Upload
{
    private $files, $validTypes, $destino, $fileName, $extension,$type,$userID, $pontoBack;
    public $upStatus, $fileLocation;

    public function __construct($files, $destino, $type, $userID, $pontoBack = false, $fileName = false, $validTypes = false)
    {
        $this->files = $files;
        $this->type = $type;
        $this->destino = $destino;
        $this->userID = $userID;
        $this->pontoBack = $pontoBack;
        $this->getExtension(); // deixa this->extension pronto
        if ($fileName) {
            $this->fileName = $fileName;
        } else {
            $mkt = time();
            $rand = rand(1, 99999);
            $rand2 = rand(1, 99999);
            $this->fileName = "uid{$this->userID}-{$mkt}-{$rand}-{$rand2}.{$this->extension}";
        }
        if ($validTypes == false) {
            $this->validTypes = ['png', 'jpg', 'jpeg', 'gif'];
        }

        if ($this->isValidExtension()) {
            $this->cropImage(25);
        } else {
            echo "Não foi possivel fazer o download, formato invalido";
        }

    } /// construct


    private function getExtension()
    {
        $fileOrignalName = $this->files['name'];
        $extension = substr($fileOrignalName, strrpos($fileOrignalName, ".") + 1);
        $this->extension = strtolower($extension);

    } //getExtension


    private function isValidExtension()
    {
        if (array_search($this->extension, $this->validTypes) !== false) {
            if ($this->checkType()) {
                return true;
            } else {
                return false;
            }

        } else {
            return false;
        }
    } // end isValidExtension


    private function checkType()
    {
        $type = $this->files['type'];
        $exType = explode("/", $type);
        $realType = $exType[1];
        if (array_search($realType, $this->validTypes) !== false) {
            return true;
        } else {
            return false;
        }
    } /// end checkType


    private function upload()
    {
        $destino = "{$this->destino}/{$this->fileName}";

        $this->fileLocation = $destino; // se ficar abaixo da alteração de $destino, pode causar erro, pois
        // será registrado no banco de dados com ../ se $pontoBack estiver setado

        if(!is_dir($destino)){
            if($this->pontoBack){
                $destino = "{$this->pontoBack}{$this->destino}/{$this->fileName}";
            }
        }

        $this->upStatus = move_uploaded_file($this->files['tmp_name'], $destino);
    }


    private function cropImage($percentagem = 50){
        $destino = "{$this->destino}/{$this->fileName}";
        $this->fileLocation = $destino;

        if(array_search($this->type,['image/pjpeg','image/jpeg']) !== false){
             $imagem_temporaria = imagecreatefromjpeg($this->files['tmp_name']);
         }elseif (array_search($this->type,['image/png','image/x-png']) !== false){
             $imagem_temporaria = imagecreatefrompng($this->files['tmp_name']);

         }else{
             $this->upload();
         }

         if(array_search($this->type,['image/pjpeg','image/jpeg','image/png','image/x-png']) !== false){
             // entrou aqui, significa que é jpeg ou png, então prosegue

             $largura_original = imagesx($imagem_temporaria);
             $altura_original = imagesy($imagem_temporaria);
             $nova_largura = floor((($percentagem / 100) * $largura_original));
             $nova_altura = floor((($percentagem / 100) * $altura_original));
             $imagem_redimensionada = imagecreatetruecolor($nova_largura, $nova_altura);
             imagecopyresampled($imagem_redimensionada, $imagem_temporaria, 0, 0, 0, 0, $nova_largura, $nova_altura, $largura_original, $altura_original);

             if(!is_dir($destino)){
                 if($this->pontoBack){
                     $destino = "{$this->pontoBack}{$this->destino}/{$this->fileName}";
                 }
             }

             if(array_search($this->type,['image/pjpeg','image/jpeg']) !== false){
                $this->upStatus = imagejpeg($imagem_redimensionada, $destino);
                
             }else{
                $this->upStatus  = imagepng($imagem_redimensionada, $destino);
                
             }

         }




    }  // end cropImage






} // end class

