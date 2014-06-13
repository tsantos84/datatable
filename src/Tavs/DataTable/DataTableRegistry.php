<?php

namespace Tavs\DataTable;

use Tavs\DataTable\Exception\InvalidTypeException;
use Tavs\DataTable\Exception\TypeNotFoundException;
use Tavs\DataTable\ColumnType\ColumnTypeInterface;

/**
 * Class DataTableFactory
 * @package Tavs\DataTable
 */
class DataTableRegistry implements DataTableRegistryInterface
{
    /**
     * @var array
     */
    private $columnTypes = array();

    /**
     * @var array
     */
    private $resolvedTypes = array();

    /**
     * @var array
     */
    private $dataTables = array();

    /**
     * @inheritdoc
     */
    public function addColumnType(ColumnTypeInterface $type)
    {
        $this->columnTypes[$type->getName()] = $type;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getColumnType($name)
    {
        if (array_key_exists($name, $this->columnTypes)) {
            return $this->columnTypes[$name];
        }

        throw new TypeNotFoundException('type "'.$name.'" not found');
    }

    /**
     * @inheritdoc
     */
    public function addDataTableType(DataTableTypeInterface $dataTableType)
    {
        $this->dataTables[$dataTableType->getName()] = $dataTableType;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getDataTableType($name)
    {
        return $this->dataTables[$name];
    }

    /**
     * @inheritdoc
     */
    public function getResolvedColumnType($name)
    {
        if ($this->hasResolvedColumnType($name)) {
            return $this->resolvedTypes[$name];
        }

        throw new InvalidTypeException('resolved type "'.$name.'" not found');
    }

    /**
     * @inheritdoc
     */
    public function hasResolvedColumnType($name)
    {
        return array_key_exists($name, $this->resolvedTypes);
    }

    /**
     * @inheritdoc
     */
    public function resolveColumnType($type)
    {
        if (is_string($type)) {
            $type = $this->getColumnType($type);
        }

        $name = $type->getName();

        if ($this->hasResolvedColumnType($name)) {
            return $this->getResolvedColumnType($name);
        }

        $parent = $type->getParent();

        if ($parent instanceof ColumnTypeInterface) {
            $parent = $this->resolveColumnType($parent);
        } elseif (is_string($parent)) {
            $parent = $this->resolveColumnType($this->getColumnType($parent));
        }

        $resolvedType = new ResolvedType($type, $parent);
        $this->resolvedTypes[$name] = $resolvedType;

        return $resolvedType;
    }
}