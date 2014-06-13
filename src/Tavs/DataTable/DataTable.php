<?php

namespace Tavs\DataTable;

use Tavs\DataTable\DataSource\DataSourceInterface;
use Tavs\DataTable\Event\DataTableEvent;
use Tavs\DataTable\Event\DataTableEvents;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class DataTable
 * @package Tavs\DataTable
 */
class DataTable implements DataTableInterface
{
    /**
     * @var ColumnBag
     */
    public $columns;

    /**
     * @var DataSourceInterface
     */
    private $dataSource;

    /**
     * @var EventDispatcherInterface
     */
    private $dispatcher;

    /**
     * @var DataTableTypeInterface
     */
    private $type;

    /**
     * @var array
     */
    private $options;

    /**
     * @param EventDispatcherInterface $dispatcher
     * @param array $options
     */
    public function __construct(EventDispatcherInterface $dispatcher, array $options = array())
    {
        $this->columns = new ColumnBag();
        $this->dispatcher = $dispatcher;
        $this->options = $options;
    }

    /**
     * @param mixed $options
     */
    public function setOptions(array $options)
    {
        $this->options = $options;
    }

    /**
     * @return mixed
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * @inheritdoc
     */
    public function getIterator()
    {
        return $this->columns->getIterator();
    }

    /**
     * @inheritdoc
     */
    public function add(ColumnInterface $column)
    {
        return $this->columns->add($column);
    }

    /**
     * @inheritdoc
     */
    public function get($name)
    {
        return $this->columns->get($name);
    }

    /**
     * @param $name
     * @return mixed
     */
    public function has($name)
    {
        return $this->columns->has($name);
    }

    /**
     * @param $name
     * @return mixed
     */
    public function remove($name)
    {
        return $this->columns->remove($name);
    }

    /**
     * @return int
     */
    public function count()
    {
        return count($this->columns);
    }

    /**
     * @param DataSourceInterface $datasource
     * @return $this
     */
    public function setDataSource(DataSourceInterface $datasource = null)
    {
        $this->dataSource = $datasource;
        return $this;
    }

    /**
     * @return DataSourceInterface
     */
    public function getDataSource()
    {
        return $this->dataSource;
    }

    /**
     * @param Request $request
     * @return $this|void
     */
    public function handleRequest(Request $request)
    {
        $this->dispatcher->dispatch(DataTableEvents::BEFORE_HANDLE_REQUEST, new DataTableEvent($this));
        $datasource = $this->getDataSource();
        $datasource->handleRequest($this, $request);
        return $this;
    }

    /**
     * @return DataTableView
     */
    public function createView()
    {
        $view = new DataTableView();

        if (null !== $this->type) {
            $this->type->buildView($view, $this->getOptions());
        }

        $view['datasource'] = $this->getDataSource();

        /** @var Column $column */
        foreach ($this->columns as $column) {
            $view->columns[$column->getName()] = $column->createView($view);
        }

        return $view;
    }
}