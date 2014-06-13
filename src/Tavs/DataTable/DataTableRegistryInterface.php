<?php

namespace Tavs\DataTable;

use Tavs\DataTable\ColumnType\ColumnTypeInterface;

/**
 * Class DataTableFactory
 * @package Tavs\DataTable
 */
interface DataTableRegistryInterface
{
    /**
     * @param $name
     * @return mixed
     * @throws Exception\TypeNotFoundException
     */
    public function getColumnType($name);

    /**
     * @param ColumnTypeInterface $type
     * @return $this
     */
    public function addColumnType(ColumnTypeInterface $type);

    /**
     * @param $name
     * @return mixed
     * @throws Exception\InvalidTypeException
     */
    public function getResolvedColumnType($name);

    /**
     * @param $name
     * @return bool
     */
    public function hasResolvedColumnType($name);

    /**
     * @param string|ColumnTypeInterface $type
     * @return ResolvedType|mixed
     */
    public function resolveColumnType($type);

    /**
     * @param DataTableTypeInterface $dataTableType
     * @return mixed
     */
    public function addDataTableType(DataTableTypeInterface $dataTableType);

    /**
     * @param $name
     * @return DataTableTypeInterface
     */
    public function getDataTableType($name);
}