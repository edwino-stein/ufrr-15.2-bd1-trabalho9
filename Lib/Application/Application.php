<?php
namespace Application;

include_once(__DIR__.'/autoload.php');

abstract class Application {

    abstract protected function init();

    private static $instance;
    private static $namespaces;

    public static function run(Application $instance, $namespaces){
        if(self::$instance !== null) return;
        self::$namespaces = $namespaces;
        self::$instance = $instance;
        self::$instance->init();
    }

    public static function app(){
        return self::$instance;
    }

    public static function autoLoad($className){

        $namespace = explode('\\', $className);
        $base = array_shift($namespace);

        if(empty($namespace)) return;
        if(!isset(self::$namespaces[$base]))
            throw new Exception('O Namespace "'.$base.'\\'.implode('\\', $namespace).'" não foi registrado.', 1);

        require_once(self::$namespaces[$base].implode('/', $namespace).'.php');
    }

}
