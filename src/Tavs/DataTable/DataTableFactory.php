<?php

namespace Tavs\DataTable;

use Symfony\Component\OptionsResolver\OptionsResolver;
use Tavs\DataTable\DataSource\ArrayDataSource;
use Tavs\DataTable\DataSource\DataSourceInterface;
use Tavs\DataTable\DataSource\QueryBuilderDataSource;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\EventDispatcher\EventDispatcher;

/**
 * Class DataTableFactory
 * @package Tavs\DataTable
 */
class DataTableFactory implements DataTableFactoryInterface
{
    /**
     * @var DataTableRegistryInterface
     */
    private $registry;

    /**
     * @param DataTableRegistryInterface $registry
     */
    public function __construct(DataTableRegistryInterface $registry)
    {
        $this->registry = $registry;
    }

    /**
     * @inheritdoc
     */
    public function getRegistry()
    {
        return $this->registry;
    }

    /**
     * @inheritdoc
     */
    public function createBuilder(DataTableTypeInterface $type = null, array $options = array())
    {
        if (null === $type) {
            $type = new DataTableType();
        }

        $resolver = new OptionsResolver();
        $type->setDefaultOptions($resolver);

        $builder = new DataTableBuilder($this, new EventDispatcher(), $resolver->resolve($options));
        return $builder;
    }

    /**
     * @inheritdoc
     */
    public function createDataTable($type, $dataSource = null, array $options = [])
    {
        if (is_string($type)) {
            $type = $this->registry->getDataTableType($type);
        }

        if (!$type instanceof DataTableTypeInterface) {
            throw new \InvalidArgumentException('$type should be instance of Tavs\DataTable\DataTableTypeInterface');
        }

        // build the datatable's columns
        $builder = $this->createBuilder($type, $options);
        $options = $builder->getOptions();
        $type->buildDataTable($builder, $options);

        // build the datatable
        $dataTable = $builder->getDataTable();
        $dataTable->setOptions($options);
        $dataTable->setType($type);

        if (null !== $dataSource) {
            $dataSource = $this->createDataSource($dataSource);
            $dataTable->setDataSource($dataSource);
        }

        return $dataTable;
    }

    /**
     * @inheritdoc
     */
    public function createDataSource($data)
    {
        switch (true) {

            case ($data instanceof DataSourceInterface):
                $dataSource = $data;
                break;

            case is_array($data):
                $dataSource = new ArrayDataSource($data);
                break;

            case ($data instanceof QueryBuilder):
                $dataSource = new QueryBuilderDataSource($data);
                break;

            default:
                throw new \InvalidArgumentException('no datasource to handle data of ' . (is_object($data) ? get_class($data) : gettype($data)));
        }

        return $dataSource;
    }
}