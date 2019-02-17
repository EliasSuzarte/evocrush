<?php

if(!empty($_COOKIE)){
    if(!empty($_COOKIE['mail'])){
        setcookie("mail","",time()-3600,'/');
        unset($_COOKIE['mail']);

    }

    if(!empty($_COOKIE['pass'])){
        setcookie("pass","",time()-3600,'/');
        unset($_COOKIE['pass']);
    }

    header("Location: /?logOut=true");

}else{
    header("Location: /?from=sair");

}

?>