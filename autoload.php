<?php
spl_autoload_register(function ($ClassName){
    require("Classes/{$ClassName}.php");
});