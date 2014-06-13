<?php

namespace Tavs\DataTable\DataSource;

use Tavs\DataTable\DataTableInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Interface DataSourceInterface
 * @package Tavs\DataTable
 */
interface DataSourceInterface extends \IteratorAggregate, \Countable
{
    /**
     * @return integer
     */
    public function getTotal();

    /**
     * @param DataTableInterface $datatable
     * @param Request $request
     * @throws \BadMethodCallException
     */
    public function handleRequest(DataTableInterface $datatable, Request $request);

    /**
     * @return \Iterator
     */
    public function getData();
}