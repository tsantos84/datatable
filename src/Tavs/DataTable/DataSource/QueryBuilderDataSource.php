<?php

namespace Tavs\DataTable\DataSource;

use Doctrine\ORM\Query\Expr;
use Tavs\DataTable\ColumnInterface;
use Tavs\DataTable\DataTableInterface;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request;
use Tavs\DataTable\Query\Parser;

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
     * @param ParameterBag       $params
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
     * @param ParameterBag       $params
     */
    private function handleCriterias(DataTableInterface $datatable, ParameterBag $params)
    {

        //(Field1.status = 1 OR Field1.status = 2 AND Field 1 <> 3) AND (....)

        if ($search = $params->get('search')) {

            $parser = new Parser();
            $criterias = $parser->parse($search['value']);

            if (count($criterias) > 0) {

                $query = $this->query;
                $expr = $query->expr();
                $globalAndX = $expr->andX();

                foreach ($criterias as $criteria) {

                    $field = $criteria['field'];
                    $orX = $expr->orX();
                    $andX = $expr->andX();
                    $criteriaSet = $expr->andX();

                    // loop throug all expressions for each field
                    foreach ($criteria['expressions'] as $expression) {

                        $method = $expression['expr'];
                        $value = $expression['val'];
                        $args = [$field];

                        if (method_exists($expr, $method)) {

                            if (is_array($value)) {
                                $args = array_merge($args, $value);
                            } else {
                                $args[] = $value;
                            }

                            // calls the expression
                            $exprResult = call_user_func_array([$expr, $method], $args);

                            if (Parser::OP_OR == $expression['connector']) {
                                $orX->add($exprResult);
                            } else {
                                $andX->add($exprResult);
                            }
                        } else {
                            throw new \InvalidArgumentException(sprintf(
                                'The expression %s is not allowed', $expression['expr']
                            ));
                        }
                    }

                    $query->andWhere($criteriaSet->addMultiple([$andX, $orX]));
                }

            }
        }
    }

    /**
     * @param DataTableInterface $datatable
     * @param ParameterBag       $params
     */
    private function handleOrdering(DataTableInterface $datatable, ParameterBag $params)
    {
        foreach ($params->get('order') as $spec) {

            $name = $params->get('columns[' . $spec['column'] . '][name]', null, true);

            $column = $datatable->get($name);

            if ($column->isOrderable()) {
                $this->query->orderBy($this->getColumnFullName($column), $spec['dir']);
            }
        }
    }

    /**
     * @param ColumnInterface $column
     *
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