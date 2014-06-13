<?php
/**
 * Created by PhpStorm.
 * User: c0339564
 * Date: 22/05/14
 * Time: 13:16
 */
namespace Tavs\DataTable;
use Tavs\DataTable\ColumnType\ColumnTypeInterface;


/**
 * Class Column
 * @package Tavs\DataTable
 */
interface ColumnInterface
{
    /**
     * @return mixed
     */
    public function getName();

    /**
     * @param mixed $name
     */
    public function setName($name);

    /**
     * @param ColumnTypeInterface $type
     */
    public function setType($type);

    /**
     * @return ColumnTypeInterface
     */
    public function getType();

    /**
     * @param array $options
     */
    public function setOptions(array $options);

    /**
     * @return array
     */
    public function getOptions();

    /**
     * @param $name
     * @return bool
     */
    public function hasOption($name);

    /**
     * @param $name
     * @return mixed
     * @throws Exception\OptionNotFoundException
     */
    public function getOption($name);

    /**
     * @return boolean
     */
    public function isOrderable();

    /**
     * @return string
     */
    public function getPropertyPath();

    /**
     * @inheritdoc
     */
    public function getEntityAlias();

    /**
     * @param DataTableView $dataTableView
     * @return ColumnView
     */
    public function createView(DataTableView $dataTableView);
}