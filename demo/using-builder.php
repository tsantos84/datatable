<?php

require 'bootstrap.php';

use Tavs\DataTable;

$dataTable = $factory->createBuilder()
    ->add('id', 'text')
    ->add('name', 'text')
    ->add('email', 'email')
    ->add('created_at', 'date_time')
    ->add('active', 'checkbox')
    ->getDataTable();

$dataSource = $factory->createDataSource(include 'data.php');
$dataTable->setDataSource($dataSource)->handleRequest($request);

echo $twig->render('index.twig', [
    'dataTable' => $dataTable->createView()
]);