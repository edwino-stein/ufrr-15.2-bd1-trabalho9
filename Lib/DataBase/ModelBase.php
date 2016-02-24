<?php
namespace DataBase;

use DataBase\Connection;
use DataBase\TableSchema;
use DataBase\Types;
use DataBase\Where;

class ModelBase{

    const ORDER_DIRECTION_ASC = 'ASC';
    const ORDER_DIRECTION_DESC = 'DESC';

    const GENERIC_SELECT = 'SELECT * FROM {table} {order} {direction} {limit}';
    const FIND_SELECT = 'SELECT * FROM {table} {where} {order} {direction} {limit}';
    const FIND_ONE_SELECT = 'SELECT * FROM {table} {where} LIMIT 0,1';

    private static $schemas = array();

    public static function getTableSchema($modelName = null){
        if($modelName === null) $modelName = get_called_class();
        if(isset(self::$schemas[$modelName])) return self::$schemas[$modelName];
        self::$schemas[$modelName] = new TableSchema($modelName);
        return self::$schemas[$modelName];
    }

    public static function fetchAll($options = array()){

        if(!Connection::isInited()) throw new \Exception("A conexão com o banco de dados não foi configurada.", 1);

        $modelName = get_called_class();
        $tableSchema = self::getTableSchema($modelName);
        $options = self::parseOptions($options, $tableSchema);

        $sql = str_replace(
            array('{table}', '{order}', '{direction}', '{limit}'),
            array(
                $tableSchema->getTableName(),
                $options['orderby'],
                $options['direction'],
                $options['limit'],
            ),
            self::GENERIC_SELECT
        );

        $query = Connection::getConnection()->createQuery($sql);

        if(!$query->execute()) return false;

        $data = array();
        while($row = $query->fetch(\PDO::FETCH_OBJ))
            $data[] = self::getModelInstance($modelName, $tableSchema, $row);

        return $data;
    }

    public static function findBy($where, $options = array()){

        if(!Connection::isInited()) throw new \Exception("A conexão com o banco de dados não foi configurada.", 1);

        $modelName = get_called_class();
        $tableSchema = self::getTableSchema($modelName);
        $options = self::parseOptions($options, $tableSchema);

        if(is_array($where)) $where = Where::parseArray($where);
        if(!($where instanceof Where)) throw new \Exception("O parametro \'\$where\' deve ser um array ou uma instancia de DataBase\Where", 1);

        $sql = str_replace(
            array('{table}', '{where}', '{order}', '{direction}', '{limit}'),
            array(
                $tableSchema->getTableName(),
                $where->getSqlSnippet($tableSchema),
                $options['orderby'],
                $options['direction'],
                $options['limit'],
            ),
            self::FIND_SELECT
        );

        $query = Connection::getConnection()->createQuery($sql);
        if(!$query->execute()) return false;

        $data = array();
        while($row = $query->fetch(\PDO::FETCH_OBJ))
            $data[] = self::getModelInstance($modelName, $tableSchema, $row);

        return $data;
    }

    public static function findOneBy($where){

        if(!Connection::isInited()) throw new \Exception("A conexão com o banco de dados não foi configurada.", 1);

        $modelName = get_called_class();
        $tableSchema = self::getTableSchema($modelName);

        if(is_array($where)) $where = Where::parseArray($where);
        if(!($where instanceof Where)) throw new \Exception("O parametro \'\$where\' deve ser um array ou uma instancia de DataBase\Where", 1);

        $sql = str_replace(
            array('{table}', '{where}'),
            array(
                $tableSchema->getTableName(),
                $where->getSqlSnippet($tableSchema),
            ),
            self::FIND_ONE_SELECT
        );

        $query = Connection::getConnection()->createQuery($sql);
        if(!$query->execute()) return false;
        $row = $query->fetch(\PDO::FETCH_OBJ);
        return $row ? self::getModelInstance($modelName, $tableSchema, $row) : null;
    }

    protected static function parseOptions($options, $tableSchema){

        // Se não tiver opçoes, pega a padão
        if(!is_array($options)){
            $options = array();
            $idColumn = $tableSchema->getIdColumn();

            $options['orderby'] = $idColumn === null ? '' : 'ORDER BY '.$idColumn['name'];
            $options['direction'] = $idColumn === null ? '' : self::ORDER_DIRECTION_ASC;
            $options['limit'] = '';
            return $options;
        }

        //Descobre o order by
        if(!isset($options['orderby']) || !$tableSchema->hasColumn($options['orderby'])){
            $idColumn = $tableSchema->getIdColumn();
            $options['orderby'] = $idColumn === null ? '' : 'ORDER BY '.$idColumn['name'];
        }
        else{
            $options['orderby'] = 'ORDER BY '.$tableSchema->getColumn($options['orderby'])['name'];
        }

        //descobre o criterio de ordenação
        if($options['orderby'] === ''){
            $options['direction'] = '';
        }
        else if(!isset($options['direction'])){
            $options['direction'] = self::ORDER_DIRECTION_ASC;
        }

        else if(strtoupper($options['direction']) !== self::ORDER_DIRECTION_ASC && strtoupper($options['direction']) !== self::ORDER_DIRECTION_DESC){
            $options['direction'] = self::ORDER_DIRECTION_ASC;
        }
        else{
            $options['direction'] = strtoupper($options['direction']);
        }

        //Limite e offset
        $limit = isset($options['limit']) ? (int) $options['limit'] : 0;
        $offset = isset($options['offset']) ? (int) $options['offset'] : 0;
        unset($options['offset']);

        if($limit > 0){
            $options['limit'] = 'LIMIT '.($offset >= 0 ? $offset.',' : '0,').$limit;
        }
        else{
            $options['limit'] = '';
        }

        return $options;
    }

    protected static function getModelInstance($modelName, $tableSchema, &$row = null){

        $model = new $modelName;

        foreach ($row as $name => $value){
            $propertyName = null;
            $columnSchema = $tableSchema->getColumnByName($name, $propertyName);
            $model->_set($propertyName, $value, $columnSchema);
        }

        return $model;
    }

    protected function _set($property, $value, $columnSchema){

        //Não faz nada se não existir a propriedade
        if(!property_exists($this, $property)) return;

        //Utiliza o metodo setter caso exista
        if(method_exists($this, 'set'.ucfirst($property))){
            $setter = 'set'.ucfirst($property);
            $this->$setter(Types::casting($value, $columnSchema['type']));
            return;
        }

        //Ou apenas tenta atribuir o valor a propriedade
        $this->$property = Types::casting($value, $columnSchema['type']);
    }

}
