<?php

ini_set('display_errors', 1);

require __DIR__ . '/../vendor/autoload.php'; // path to composer autoloader

use Tavs\DataTable;

$registry = new DataTable\DataTableRegistry();
$registry->addColumnType(new DataTable\ColumnType\TextColumnType());
$registry->addColumnType(new DataTable\ColumnType\EmailColumnType());
$registry->addColumnType(new DataTable\ColumnType\CheckboxColumnType());
$registry->addColumnType(new DataTable\ColumnType\DateTimeColumnType());
$factory = new DataTable\DataTableFactory($registry);
$request = \Symfony\Component\HttpFoundation\Request::createFromGlobals();

$loader = new Twig_Loader_Filesystem();
$loader->addPath(dirname($request->server->get('SCRIPT_FILENAME')));
$loader->addPath(__DIR__ . '/../src/Tavs/DataTable/Resources/views');

$twig = new Twig_Environment($loader);
$twig->addExtension(new DataTable\Twig\Extension\DataTableExtension('datatable_layout.html.twig'));
$twig->enableStrictVariables();