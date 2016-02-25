<?php

namespace Visitas\Controller;
use Visitas\Model\LivroVisitas;

class VisitasController{

    public function readAction(){

        $visitas = LivroVisitas::fetchAll();
        $data = array();

        foreach ($visitas as $model)
            $data[] = $model->toArray();

        return json_encode(array('success' => true, 'data' => $data, 'total' => count($data)));
    }

    public function createAction(){
        var_dump('create');
    }



}
