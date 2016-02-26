<?php

namespace Visitas\Controller;
use Visitas\Model\LivroVisitas;

class VisitasController{

    public function readAction(){

        $visitas = LivroVisitas::fetchAll();

        if($visitas === false)
            json_encode(array('success' => false, 'message' => 'Os dados não foram informados corretamente.'));

        $data = array();
        foreach ($visitas as $model)
            $data[] = $model->toArray();

        return json_encode(array('success' => true, 'data' => $data, 'total' => count($data)));
    }

    public function createAction(){

        //Verifica se os valores foram informados
        $erro = false;
        if(!isset($_POST['nome']) || empty($_POST['nome'])) $erro = true;
        if(!isset($_POST['localizacao']) || empty($_POST['localizacao'])) $erro = true;
        if(!isset($_POST['mensagem']) || empty($_POST['localizacao'])) $erro = true;


        if($erro){
            return json_encode(array('success' => false, 'message' => 'Os dados não foram informados corretamente.'));
        }

        $visita = new LivroVisitas();
        $visita->setNome($_POST['nome']);
        $visita->setLocalizacao($_POST['localizacao']);
        $visita->setMensagem($_POST['mensagem']);
        $visita->setData(new \DateTime());

        if(!$visita->save())
            return json_encode(array('success' => false, 'message' => 'Erro na gravção do bano de dados.'));

        return json_encode(array('success' => true, 'data' => $visita->toArray()));
    }



}
