<?php

namespace Tavs\DataTable;

use Tavs\DataTable\DataSource\DataSourceInterface;
use Symfony\Component\HttpFoundation\Request;


/**
 * Class DataTable
 * @package Tavs\DataTable
 */
interface DataTableInterface extends \Countable, \IteratorAggregate
{
    /**
     * @param DataSourceInterface $datasource
     * @return $this
     */
    public function setDataSource(DataSourceInterface $datasource = null);

    /**
     * @return DataSourceInterface
     */
    public function getDataSource();

    /**
     * @param Request $request
     * @return void
     */
    public function handleRequest(Request $request);

    /**
     * @param ColumnInterface $column
     * @return DataTableInterface
     */
    public function add(ColumnInterface $column);

    /**
     * @param $name
     * @return null|ColumnInterface
     */
    public function get($name);

    /**
     * @param $name
     * @return boolean
     */
    public function has($name);

    /**
     * @param $name
     * @return boolean
     */
    public function remove($name);

    /**
     * @return DataTableView
     */
    public function createView();
}