<?php

namespace Tavs\DataTable;

use Tavs\DataTable\Exception\OptionNotFoundException;
use Tavs\DataTable\ColumnType\ColumnTypeInterface;

/**
 * Class Column
 * @package Tavs\DataTable
 */
class Column implements ColumnInterface
{
    /**
     * @var
     */
    private $name;

    /**
     * @var ResolvedTypeInterface
     */
    private $type;

    /**
     * @var array
     */
    private $options = array();

    /**
     * @param $name
     * @param ResolvedTypeInterface $type
     * @param array $options
     */
    public function __construct($name, ResolvedTypeInterface $type, array $options = array())
    {
        $this->name = $name;
        $this->type = $type;
        $this->options = $options;
    }

    /**
     * @param mixed $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param ColumnTypeInterface $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * @return ColumnTypeInterface
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param array $options
     */
    public function setOptions(array $options)
    {
        $this->options = $options;
    }

    /**
     * @return array
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * @param $name
     * @return bool
     */
    public function hasOption($name)
    {
        return array_key_exists($name, $this->options);
    }

    /**
     * @param $name
     * @return mixed
     * @throws Exception\OptionNotFoundException
     */
    public function getOption($name)
    {
        if ($this->hasOption($name)) {
            return $this->options[$name];
        }

        throw new OptionNotFoundException('option "'.$name.'" not found for column "'.$this->getName().'"');
    }

    /**
     * @inheritdoc
     */
    public function isOrderable()
    {
        return $this->getOption('orderable');
    }

    /**
     * @inheritdoc
     */
    public function getPropertyPath()
    {
        return $this->getOption('property_path');
    }

    /**
     * @inheritdoc
     */
    public function getEntityAlias()
    {
        return $this->getOption('entity_alias');
    }

    /**
     * @inheritdoc
     */
    public function createView(DataTableView $dataTableView)
    {
        $view = new ColumnView();
        $this->getType()->buildView($view, $dataTableView, $this->getOptions());
        return $view;
    }
}