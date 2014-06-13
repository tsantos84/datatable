<?php
/**
 * Created by PhpStorm.
 * User: c0339564
 * Date: 22/05/14
 * Time: 13:20
 */
namespace Tavs\DataTable;

/**
 * Class ColumnBag
 * @package Tavs\DataTable
 */
interface ColumnBagInterface extends \IteratorAggregate, \Countable
{
    /**
     * @param $name
     */
    public function has($name);

    /**
     * @return array
     */
    public function all();

    /**
     * @param ColumnInterface $column
     */
    public function add(ColumnInterface $column);

    /**
     * @param $name
     */
    public function get($name);

    /**
     * @param $name
     */
    public function remove($name);
}