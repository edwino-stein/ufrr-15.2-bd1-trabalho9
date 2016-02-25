<?php
namespace DataBase;

class Connection {

    protected static $instance = null;
    const DSN_TEMPLATE = '{driver}:host={host};dbname={dbname}';

    protected static function getDefaultConfig(){
        return array(
            'host' => 'localhost',
            'driver' => 'mysql',
            'dbname' => '',
            'user' => 'root',
            'password' => ''
        );
    }

    public $pdoConnection;

    protected function __construct($config){
        $this->pdoConnection = new \PDO(
            $config['dsn'],
            $config['username'],
            $config['password']
        );
    }

    public function query($sql){
        return $this->pdoConnection->exec($sql);
    }

    public function createQuery($sql){
        return $this->pdoConnection->prepare($sql);
    }

    protected function __clone() {}

    public static function getConnection($config = null){
        if(is_array($config)) self::setConnection($config);
        return self::$instance;
    }

    public static function setConnection($config){
        if(!is_array($config)) return;
        self::$instance = new self(self::parseConfg($config));
    }

    public static function getLastInsertId(){
        return self::$instance->pdoConnection->lastInsertId();
    }

    protected static function parseConfg($config){

        $defaultConfig = self::getDefaultConfig();

        return array(
            'dsn' => str_replace(
                array('{driver}', '{host}', '{dbname}'),
                array(
                    isset($config['driver']) ? $config['driver'] : $defaultConfig['driver'],
                    isset($config['host']) ? $config['host'] : $defaultConfig['host'],
                    isset($config['dbname']) ? $config['dbname'] : $defaultConfig['dbname']
                ),
                self::DSN_TEMPLATE
            ),
            'username' => isset($config['user']) ? $config['user'] : $defaultConfig['user'],
            'password' => isset($config['password']) ? $config['password'] : $defaultConfig['password']
        );
    }

    public static function isInited(){
        return self::$instance !== null;
    }
}
