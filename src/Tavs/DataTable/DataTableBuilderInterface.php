<?php

namespace Tavs\DataTable;

/**
 * Class DataTableBuilder
 * @package Tavs\DataTable
 */
interface DataTableBuilderInterface extends \IteratorAggregate
{
    /**
     * @param $name
     * @return boolean
     */
    public function has($name);

    /**
     * @param $name
     * @param $type
     * @param array $options
     * @return $this
     */
    public function add($name, $type, array $options = array());

    /**
     * @return DataTableInterface
     */
    public function getDataTable();

    /**
     * @param $name
     * @return array
     */
    public function get($name);

    /**
     * @param $name
     * @return DataTableBuilderInterface
     */
    public function remove($name);
}