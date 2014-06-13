<?php

namespace Tavs\DataTable;
use Traversable;

/**
 * Class DataTableView
 * @package Tavs\DataTable
 */
class DataTableView extends View implements \IteratorAggregate
{
    /**
     * @var array
     */
    public $columns = array();

    /**
     * @return Traversable|void
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->columns);
    }
}