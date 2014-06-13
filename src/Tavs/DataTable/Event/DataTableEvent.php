<?php

namespace Tavs\DataTable\Event;

use Tavs\DataTable\DataTableInterface;
use Symfony\Component\EventDispatcher\Event;

/**
 * Class DataTableEvent
 * @package Tavs\DataTable\Event
 */
class DataTableEvent extends Event
{
    /**
     * @var DataTableInterface
     */
    private $dataTable;

    /**
     * @param DataTableInterface $dataTable
     */
    public function __construct(DataTableInterface $dataTable)
    {
        $this->dataTable = $dataTable;
    }

    /**
     * @return DataTableInterface
     */
    public function getDataTable()
    {
        return $this->dataTable;
    }
}