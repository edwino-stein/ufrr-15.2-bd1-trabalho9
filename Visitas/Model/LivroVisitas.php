<?php
namespace Visitas\Model;

use DataBase\ModelBase;

/**
 * @table guestbook
 */
class LivroVisitas extends ModelBase{

    /**
     * @var int
     * @id
     * @column id
     */
    protected $id;

    /**
     * @var string
     * @column nome
     * @length 255
     */
    protected $nome;

    /**
     * @var string
     * @column localizacao
     * @length 50
     */
    protected $localizacao;

    /**
     * @var string
     * @column mensagem
     */
    protected $mensagem;

    /**
     * @var datetime
     * @column data
     */
    protected $data;


    public function setNome($nome){
        $this->nome = $nome;
        return $this;
    }

    public function setLocalizacao($localizacao){
        $this->localizacao = $localizacao;
        return $this;
    }

    public function setMensagem($mensagem){
        $this->mensagem = $mensagem;
        return $this;
    }

    public function setData($data){
        $this->data = $data;
        return $this;
    }

    public function getId(){
        return $this->id;
    }

    public function getNome(){
        return $this->nome;
    }

    public function getLocalizacao(){
        return $this->localizacao;
    }

    public function getMensagem(){
        return $this->mensagem;
    }

    public function getData(){
        return $this->data;
    }

}
