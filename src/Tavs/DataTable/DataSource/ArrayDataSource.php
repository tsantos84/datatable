<?php

namespace Tavs\DataTable\DataSource;

use Tavs\DataTable\DataTableInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class ArrayDataSource
 * @package Tavs\DataTable\DataSource
 */
class ArrayDataSource implements DataSourceInterface
{
    /**
     * @var array
     */
    private $data;

    /**
     * @var int
     */
    private $total;

    /**
     * @var bool
     */
    private $isHandled = false;

    /**
     * @param array $data
     */
    public function __construct(array $data = array())
    {
        $this->data = $data;
        $this->total = count($data);
    }

    /**
     * @inheritdoc
     */
    public function handleRequest(DataTableInterface $datatable, Request $request)
    {
        if ($this->isHandled) {
            throw new \BadMethodCallException('the request already was handled');
        }

        $params = $request->isMethod('post')
            ? $request->request
            : $request->query;

        $length = $params->getInt('length', 10);
        $offset = $params->getInt('start', 0);
        $this->isHandled = true;

        $this->data = array_slice($this->data, $offset, $length);
    }

    /**
     * @inheritdoc
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @inheritdoc
     */
    public function getTotal()
    {
        return $this->total;
    }

    /**
     * @inheritdoc
     */
    public function count()
    {
        return count($this->data);
    }

    /**
     * @inheritdoc
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->data);
    }
}