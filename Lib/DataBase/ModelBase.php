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
    const INSERT = 'INSERT INTO {table} ({columns}) VALUES ({data})';
    const UPDATE = 'UPDATE {table} SET {setters} {where}';
    const DELETE = 'DELETE FROM {table} {where}';

    private static $schemas = array();

    private $__loaded__ = false;

    public function save(){
        return $this->__loaded__ ? $this->update() : $this->insert();
    }

    public function delete(){
        if(!Connection::isInited()) throw new \Exception("A conexão com o banco de dados não foi configurada.", 1);
        if(!$this->__loaded__) return true;

        $modelName = get_called_class();
        $tableSchema = self::getTableSchema($modelName);
        $idColumn = null;
        $tableSchema->getIdColumn($idColumn);

        if($idColumn === null) throw new \Exception('Não existe um identificador definido para a entidade "'.$modelName.'".', 1);
        $where = Where::parseArray(array($idColumn => $this->_get($idColumn)));

        $sql = str_replace(
            array('{table}', '{where}'),
            array(
                $tableSchema->getTableName(),
                $where->getSqlSnippet($tableSchema)
            ),
            self::DELETE
        );

        $query = Connection::getConnection()->createQuery($sql);
        if(!$query->execute()) return false;
        $this->_setLoaded(false);
        return true;
    }

    public function update(){

        if(!Connection::isInited()) throw new \Exception("A conexão com o banco de dados não foi configurada.", 1);
        if(!$this->__loaded__) return $this->insert();

        $modelName = get_called_class();
        $tableSchema = self::getTableSchema($modelName);

        $schemaCols = $tableSchema->getColumns();
        $setters = array();
        $idColumn = null;
        $value = null;

        foreach ($schemaCols as $property => $schema){

            if($schema['id']){
                $idColumn = $property;
                continue;
            }

            $value = Types::prepareToQuery($this->_get($property), $schema['type']);

            if($schema['notnull'] && $value === Types::NULL_TYPE){
                throw new \Exception("A propriedade '$property' não pode ser NULL", 1);
            }

            if($schema['length'] !== null && $schema['type'] === 'string' && (strlen($value) - 2) >  $schema['length']){
                throw new \Exception("A propriedade '$property' excedeu o comprimento maximo de ".$schema['length'], 1);
            }

            $setters[] = $schema['name'].' = '.$value;
        }

        if($idColumn === null) throw new \Exception('Não existe um identificador definido para a entidade "'.$modelName.'".', 1);
        $where = Where::parseArray(array($idColumn => $this->_get($idColumn)));

        $sql = str_replace(
            array('{table}', '{setters}', '{where}'),
            array(
                $tableSchema->getTableName(),
                implode(', ', $setters),
                $where->getSqlSnippet($tableSchema)
            ),
            self::UPDATE
        );

        $query = Connection::getConnection()->createQuery($sql);
        return $query->execute() ? true : false;
    }

    public function insert(){
        if(!Connection::isInited()) throw new \Exception("A conexão com o banco de dados não foi configurada.", 1);
        if($this->__loaded__) return $this->update();

        $modelName = get_called_class();
        $tableSchema = self::getTableSchema($modelName);

        $schemaCols = $tableSchema->getColumns();
        $columns = array();
        $data = array();
        $value = null;
        $idColumn = null;

        foreach ($schemaCols as $property => $schema){

            $columns[] = $schema['name'];

            if($schema['id']){
                $idColumn = $property;
                $data[] = Types::prepareToQuery(null, null);
                continue;
            }

            $value = Types::prepareToQuery($this->_get($property), $schema['type']);

            if($schema['notnull'] && $value === Types::NULL_TYPE){
                throw new \Exception("A propriedade '$property' não pode ser NULL", 1);
            }

            if($schema['length'] !== null && $schema['type'] === 'string' && (strlen($value) - 2) >  $schema['length']){
                throw new \Exception("A propriedade '$property' excedeu o comprimento maximo de ".$schema['length'], 1);
            }

            $data[] = $value;
        }

        $sql = str_replace(
            array('{table}', '{columns}', '{data}'),
            array(
                $tableSchema->getTableName(),
                implode(', ', $columns),
                implode(', ', $data)
            ),
            self::INSERT
        );

        $query = Connection::getConnection()->createQuery($sql);
        if(!$query->execute()) return false;

        if($idColumn !== null){
            $this->_set(
                $idColumn,
                Connection::getLastInsertId(),
                $schemaCols[$idColumn]
            );
        }

        $this->_setLoaded(true);
        return true;
    }

    public function toArray(){
        $modelName = get_called_class();
        $tableSchema = self::getTableSchema($modelName);
        $schemaCols = $tableSchema->getColumns();
        $data = array();

        foreach ($schemaCols as $property => $schema)
            $data[$property] = $this->_get($property);

        return $data;
    }

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

    protected static function getModelInstance($modelName, $tableSchema, &$row = null, $loaded = true){

        $model = new $modelName;

        foreach ($row as $name => $value){
            $propertyName = null;
            $columnSchema = $tableSchema->getColumnByName($name, $propertyName);
            $model->_set($propertyName, $value, $columnSchema);

        }

        $model->_setLoaded($loaded);
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

    protected function _get($property){

        //Não faz nada se não existir a propriedade
        if(!property_exists($this, $property)) return null;

        //Utiliza o metodo getter caso exista
        if(method_exists($this, 'get'.ucfirst($property))){
            $getter = 'get'.ucfirst($property);
            return $this->$getter();
        }

        //Ou apenas tenta retornar o valor a propriedade
        return $this->$property;
    }

    protected function _setLoaded($loaded){
        $this->__loaded__ = $loaded;
    }
}
