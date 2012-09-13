<?php

    define('PATH_ROOT', str_replace('\\','/',realpath(dirname(__FILE__))));
    
    require('ActiveRecord.php');
    
    // Autoloader
    function default_autoload($class_name) {
        $namespaced  = str_replace('\\', '/', $class_name);
        $underscored = str_replace('_',  '/', $class_name);
        if(     file_exists($file = PATH_ROOT."/".$namespaced.".php"))        require($file);
        else if(file_exists($file = PATH_ROOT."/models/".$namespaced.".php")) require($file);
    }
    spl_autoload_register('default_autoload');
    
    ActiveRecord\Config::initialize(function($cfg){
        $cfg->set_model_directory(PATH_ROOT."/models");
        $cfg->set_connections(array('development' => 'mysql://tix:restfest2012@localhost/tix'));
    });
    
    define('XML', preg_match('/^api-/', $_SERVER['HTTP_HOST']));

    if(XML) {
        header('Content-Type: application/vnd.org.restfest.2012.hackday+xml');
    }
