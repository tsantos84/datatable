<?php

namespace Tavs\DataTable;
use Tavs\DataTable\DataSource\DataSourceInterface;

/**
 * Class DataTableFactory
 * @package Tavs\DataTable
 */
interface DataTableFactoryInterface
{
    /**
     * @return DataTableRegistryInterface
     */
    public function getRegistry();

    /**
     * @param DataTableTypeInterface $type
     * @param array $options
     * @return mixed
     */
    public function createBuilder(DataTableTypeInterface $type = null, array $options = array());

    /**
     * @param string|DataTableTypeInterface $type
     * @param DataSourceInterface $dataSource
     * @param array $options
     * @return mixed
     */
    public function createDataTable($type, $dataSource = null, array $options = array());

    /**
     * @param mixed $data
     * @return DataSourceInterface
     */
    public function createDataSource($data);
}