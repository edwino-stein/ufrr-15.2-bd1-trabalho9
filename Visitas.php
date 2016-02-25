<?php
namespace Visitas;
require_once('Lib/Application/Application.php');
use Application\Application;
use DataBase\Connection;

class Visitas extends Application {

    protected $dataBaseCfg;
    protected $controllers;

    public function __construct($config){
        $this->dataBaseCfg = $config['dataBase'];
        $this->controllers = array();
        foreach ($config['controllers'] as $key => $namespace)
            $this->controllers[strtolower($key)] = $namespace;
    }

    protected function init(){
        //Configura a conexão com o bando de dados
        Connection::setConnection($this->dataBaseCfg);

        //Pega o controller e a action
        $controller = isset($_GET['controller']) ? strtolower($_GET['controller']) : null;
        $action = isset($_GET['action']) ? strtolower($_GET['action']) : null;

        if($controller === null || !isset($this->controllers[$controller])){
            throw new \Exception("Nenhum controller foi encontrado.", 1);
        }

        $controller = new $this->controllers[$controller];
        $action = $action.'Action';

        if(!method_exists($controller, $action)){
            throw new \Exception("Nenhuma action foi encontrado.", 1);
        }

        //Executa a action
        $result = $controller->$action();
        if(is_string($result)) echo $result;
    }
}
