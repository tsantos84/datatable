<?php

namespace Tavs\DataTable;

use Tavs\DataTable\Exception\ColumnNotFoundException;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Class DataTableBuilder
 * @package Tavs\DataTable
 */
class DataTableBuilder implements DataTableBuilderInterface
{
    /**
     * @var array
     */
    private $columns = array();

    /**
     * @var DataTableFactoryInterface
     */
    private $factory;

    /**
     * @var EventDispatcherInterface
     */
    private $dispatcher;

    /**
     * @var array
     */
    private $options;

    /**
     * @param DataTableFactoryInterface $factory
     * @param EventDispatcherInterface $dispatcher
     * @param array $options
     */
    public function __construct(DataTableFactoryInterface $factory, EventDispatcherInterface $dispatcher, array $options = array())
    {
        $this->factory = $factory;
        $this->dispatcher = $dispatcher;
        $this->options = $options;
    }

    /**
     * @inheritdoc
     */
    public function add($name, $type, array $options = array())
    {
        $this->columns[$name] = array(
            'type' => $type,
            'options' => array_merge($options, array('name' => $name))
        );

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function get($name)
    {
        if ($this->has($name)) {
            return $this->columns[$name];
        }

        throw new ColumnNotFoundException(sprintf('The column with the name "%s" does not exist.', $name));
    }

    /**
     * @inheritdoc
     */
    public function has($name)
    {
        return array_key_exists($name, $this->columns);
    }

    /**
     * @inheritdoc
     */
    public function remove($name)
    {
        if ($this->has($name)) {
            unset($this->columns[$name]);
        }

        return $this;
    }

    /**
     * @return \ArrayIterator|\Traversable
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->columns);
    }

    /**
     * @inheritdoc
     */
    public function getDataTable()
    {
        // constroi o DataTable
        $dataTable = new DataTable($this->dispatcher, $this->options);

        foreach ($this->columns as $name => $config) {

            $type = $this->resolveColumnType($config['type']);

            // resolve as opções da coluna
            $optionsResolver = $type->getOptionsResolver();
            $resolvedOptions = $optionsResolver->resolve($config['options']);

            // adiciona a coluna ao DataTable
            $dataTable->columns->add(new Column($name, $type, $resolvedOptions));
        }

        return $dataTable;
    }

    /**
     * @param array $options
     */
    public function setOptions($options)
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
     * @param $type
     * @return ResolvedTypeInterface
     * @throws Exception\InvalidTypeException
     */
    private function resolveColumnType($type)
    {
        return $this->factory->getRegistry()->resolveColumnType($type);
    }
}