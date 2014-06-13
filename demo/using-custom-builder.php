<?php

require 'bootstrap.php';

use Tavs\DataTable;

class UserDataTable extends DataTable\DataTableType
{
    /**
     * @param DataTable\DataTableBuilderInterface $builder
     * @param array $options
     */
    public function buildDataTable(DataTable\DataTableBuilderInterface $builder, array $options = array())
    {
        $builder
            ->add('id', 'text')
            ->add('name', 'text')
            ->add('email', 'email');
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'user_datatable';
    }
}

$dataTable = $factory->createDataTable(new UserDataTable());

$dataSource = $factory->createDataSource(include 'data.php');
$dataTable->setDataSource($dataSource)->handleRequest($request);

echo $twig->render('index.twig', [
    'dataTable' => $dataTable->createView()
]);