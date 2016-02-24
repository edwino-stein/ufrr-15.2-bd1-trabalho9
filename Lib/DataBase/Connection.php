<?php
namespace DataBase;

class Connection {

    protected static $instance = null;
    const DSN_TEMPLATE = '{driver}:host={host};dbname={dbname}';
    const DEFAULT_CONFIG = array(
        'host' => 'localhost',
        'driver' => 'mysql',
        'dbname' => '',
        'user' => 'root',
        'password' => ''
    );

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

    protected static function parseConfg($config){
        return array(
            'dsn' => str_replace(
                array('{driver}', '{host}', '{dbname}'),
                array(
                    isset($config['driver']) ? $config['driver'] : self::DEFAULT_CONFIG['driver'],
                    isset($config['host']) ? $config['host'] : self::DEFAULT_CONFIG['host'],
                    isset($config['dbname']) ? $config['dbname'] : self::DEFAULT_CONFIG['dbname']
                ),
                self::DSN_TEMPLATE
            ),
            'username' => isset($config['user']) ? $config['user'] : self::DEFAULT_CONFIG['user'],
            'password' => isset($config['password']) ? $config['password'] : self::DEFAULT_CONFIG['password']
        );
    }

    public static function isInited(){
        return self::$instance !== null;
    }
}
