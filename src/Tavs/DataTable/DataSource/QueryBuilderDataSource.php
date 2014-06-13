<?php

namespace Tavs\DataTable\DataSource;

use Tavs\DataTable\ColumnInterface;
use Tavs\DataTable\DataTableInterface;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class QueryBuilderDataSource
 * @package Tavs\DataTable\DataSource
 */
class QueryBuilderDataSource implements DataSourceInterface
{
    /**
     * @var
     */
    private $query;

    /**
     * @var Paginator
     */
    private $paginator;

    /**
     * @param QueryBuilder $query
     */
    public function __construct(QueryBuilder $query)
    {
        $this->query = $query;
    }

    /**
     * @inheritdoc
     */
    public function getIterator()
    {
        return $this->paginator->getIterator();
    }

    /**
     * @inheritdoc
     */
    public function getTotal()
    {
        return $this->count();
    }

    /**
     * @inheritdoc
     */
    public function handleRequest(DataTableInterface $datatable, Request $request)
    {
        $params = $request->isMethod('post')
            ? $request->request
            : $request->query;

        $this->handleLimits($datatable, $params);
        $this->handleCriterias($datatable, $params);
        $this->handleOrdering($datatable, $params);

        $query = $this->query->getQuery();
        $query->setHydrationMode(AbstractQuery::HYDRATE_ARRAY);

        $this->paginator = new Paginator($this->query);
    }

    /**
     * @param DataTableInterface $datatable
     * @param ParameterBag $params
     */
    private function handleLimits(DataTableInterface $datatable, ParameterBag $params)
    {
        $length = $params->getInt('length', 10);
        $offset = $params->getInt('start', 0);

        $this->query
            ->setFirstResult($offset)
            ->setMaxResults($length);
    }

    /**
     * @param DataTableInterface $datatable
     * @param ParameterBag $params
     */
    private function handleCriterias(DataTableInterface $datatable, ParameterBag $params)
    {

    }

    /**
     * @param DataTableInterface $datatable
     * @param ParameterBag $params
     */
    private function handleOrdering(DataTableInterface $datatable, ParameterBag $params)
    {
        foreach ($params->get('order') as $spec) {

            $name = $params->get('columns['.$spec['column'].'][name]', null, true);

            $column = $datatable->get($name);

            if ($column->isOrderable()) {
                $this->query->orderBy($this->getColumnFullName($column), $spec['dir']);
            }
        }
    }

    /**
     * @param ColumnInterface $column
     * @return string
     */
    private function getColumnFullName(ColumnInterface $column)
    {
        if (null === ($alias = $column->getEntityAlias())) {
            $alias = $this->getRootAlias();
        }

        $fullColumnName = sprintf('%s.%s', $alias, $column->getPropertyPath());
        return $fullColumnName;
    }

    /**
     * @return string
     */
    private function getRootAlias()
    {
        return current($this->query->getRootAliases());
    }

    /**
     * @inheritdoc
     */
    public function getData()
    {
        return $this->paginator;
    }

    /**
     * @inheritdoc
     */
    public function count()
    {
        return $this->paginator->count();
    }

}