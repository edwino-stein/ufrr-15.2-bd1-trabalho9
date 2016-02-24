<?php
namespace DataBase;

use DataBase\ModelBase;
use DataBase\Annotations\Parser;
use DataBase\Annotations\Annotation;

class TableSchema{

    const DEFAULT_ATTRBS = array('type' => 'string', 'id' => false, 'notnull' => false, 'length' => null);
    const TYPES = array('int', 'integer', 'float', 'double', 'string', 'char', 'datetime', 'bool', 'boolean');

    protected static function camelCaseToUnderscored($input){
        preg_match_all('!([A-Z][A-Z0-9]*(?=$|[A-Z][a-z0-9])|[A-Za-z][a-z0-9]+)!', $input, $matches);
        $ret = $matches[0];
        foreach ($ret as &$match)
            $match = $match == strtoupper($match) ? strtolower($match) : lcfirst($match);

        return implode('_', $ret);
    }

    protected $name;
    protected $columns;

    public function __construct($model){
        $reflection = new \ReflectionClass($model);

        //Pega o nome da tabela
        $classAnnotation = Parser::getAnnotations($reflection);
        if($classAnnotation !== null && $classAnnotation->hasTag('table')){
            $this->name = $classAnnotation->getTagValue('table');
            if($this->name === true) $this->name = self::camelCaseToUnderscored(end(explode('\\', $reflection->getName())));
        }
        else{
            $this->name = self::camelCaseToUnderscored(end(explode('\\', $reflection->getName())));
        }

        //Pega os atributos das colunas
        $this->columns = array();
        $propertiesAnnotations = Parser::getAnnotationsArray($reflection->getProperties());
        foreach ($propertiesAnnotations as $name => $property)
            $this->addColumn($name, $property);
    }

    public function getTableName(){
        return $this->name;
    }

    public function getColumn($propertyName){
        return isset($this->columns[$propertyName]) ? $this->columns[$propertyName] : null;
    }

    public function getColumns(){
        return $this->columns;
    }

    public function hasColumn($propertyName){
        return isset($this->columns[$propertyName]);
    }

    public function getIdColumn(&$propertyName = null){
        foreach ($this->columns as $property => $column) {
            if($column['id'] === true){
                $propertyName = $property;
                return $column;
            }
        }
    }

    public function getColumnByName($columnName, &$propertyName){

        foreach ($this->columns as $property => $column){
            if($column['name'] === $columnName){
                $propertyName = $property;
                return $column;
            }
        }


        return null;
    }

    protected function addColumn($propertyName, $attributes){

        $colAttrbs = array();

        if(!($attributes instanceof Annotation)){
            $colAttrbs = self::DEFAULT_ATTRBS;
            $colAttrbs['name'] = self::camelCaseToUnderscored($propertyName);
        }
        else{

            //Pega o nome da tabela
            $name = $attributes->getTagValue('column');
            $colAttrbs['name'] = $name === null || $name === true ?
                                 self::camelCaseToUnderscored($propertyName) : $name;

            //Pega o tipo
            $type = strtolower($attributes->getTagValue('var'));
            $colAttrbs['type'] = in_array($type, self::TYPES) ?
                                 $type : self::DEFAULT_ATTRBS['type'];

            //Se for id
            $colAttrbs['id'] = $attributes->hasTag('id');

            //Se for notnull
            $colAttrbs['notnull'] = $attributes->hasTag('notnull');

            //Pega o comprimento
            $length = $attributes->getTagValue('length');
            $colAttrbs['length'] = $length === null || $length === true ? self::DEFAULT_ATTRBS['length'] : (int) $length;
        }

        $this->columns[$propertyName] = $colAttrbs;
    }
}
