<?php

namespace Tavs\DataTable;

use Tavs\DataTable\Exception\ColumnNotFoundException;
use Traversable;

/**
 * Class ColumnBag
 * @package Tavs\DataTable
 */
class ColumnBag implements ColumnBagInterface
{
    /**
     * @var array
     */
    private $columns = array();

    /**
     * @param ColumnInterface $column
     * @return $this
     */
    public function add(ColumnInterface $column)
    {
        $this->columns[$column->getName()] = $column;
        return $this;
    }

    /**
     * @param $name
     * @return ColumnInterface
     */
    public function get($name)
    {
        if ($this->has($name)) {
            return $this->columns[$name];
        }

        return null;
    }

    /**
     * @param $name
     * @return bool
     */
    public function has($name)
    {
        return array_key_exists($name, $this->columns);
    }

    /**
     * @param $name
     * @return $this
     * @throws Exception\ColumnNotFoundException
     */
    public function remove($name)
    {
        if ($this->has($name)) {
            unset($this->columns[$name]);
            return $this;
        }

        throw new ColumnNotFoundException('column "' . $name . '" not found');
    }

    /**
     * @return array
     */
    public function all()
    {
        return new \ArrayIterator($this->columns);
    }

    /**
     * @return array|Traversable
     */
    public function getIterator()
    {
        return $this->all();
    }

    /**
     * @inheritdoc
     */
    public function count()
    {
        return count($this->columns);
    }

}