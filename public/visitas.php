<?php

chdir(dirname(__DIR__));
require_once('Visitas.php');

Application\Application::run(
    new Visitas\Visitas(include('config.php')),
    include('namespaces.php')
);
