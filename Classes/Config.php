<?php

class Config{
    private $menu;
    public $cacheID;
    public function __construct()
    {
        $this->cacheID = 2; // rand(1,999999); // mude esse valor quando desejar que o navegador não use cahce no js e css
        self::controller();
    }



    public function setMenu($menu = false){
        if(!$menu){
            $this->menu = ['/'=>'Home','/chat.php'=>'Chat Público','/notifications.php'=>'Notificações','/online'=>'Pessoas Online','/sair.php'=>'Sair','/gerenciar'=>'Gerenciar Perfil'];
        }else{
            $this->menu = $menu;
        }
    } // setMenu



    public function getMenu(){

      foreach ($this->menu as $key=> $m){
          echo "<li><a href=\"{$key}\">{$m}</a></li>";
      }


    } // getMenu


    public function controller(){

        $page = $_SERVER['PHP_SELF'];
        $arr = ['/index.php','/delete.php','/login.php','/online/index.php','/perfil/index.php','/gerenciar/fotos.php','/gerenciar/index.php','/chat.php'];
        self::setMenu();

    } // end controller


    function getFooter(){
        echo '<span>&copy <a href="/">Evo Crush</a> - 2019</span><br>';
        echo '<span><a target="_blank" href="/privacidade.php">Termos de Privacidade</a> </span> --||--';
        echo '<span><a target="_blank" href="/suporte">Suporte</a> </span>';
    }




} // end Config class
