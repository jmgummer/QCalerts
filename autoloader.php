<?php
spl_autoload_register(function($class){
    include_once('public/lib/' . $class . '.php');
});