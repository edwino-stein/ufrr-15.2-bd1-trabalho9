<?php
namespace Visitas;
require_once('Lib/Application/Application.php');
use Application\Application;
use DataBase\Connection;
use Visitas\Model\LivroVisitas;

class Visitas extends Application {

    protected $config;

    public function __construct($config){
        $this->config = $config;
    }

    protected function init(){
        Connection::setConnection($this->config['dataBase']);

        var_dump(LivroVisitas::fetchAll());
    }
}
