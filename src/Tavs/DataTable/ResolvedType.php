<?php

namespace Tavs\DataTable;

use Tavs\DataTable\ColumnType\ColumnTypeInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Class ResolvedType
 * @package Tavs\DataTable
 */
class ResolvedType implements ResolvedTypeInterface
{
    /**
     * @var ColumnTypeInterface
     */
    private $type;

    /**
     * @var ResolvedTypeInterface
     */
    private $parent;

    /**
     * @var OptionsResolverInterface
     */
    private $optionsResolver;

    /**
     * @param ColumnTypeInterface $type
     * @param ResolvedTypeInterface $parent
     */
    public function __construct(ColumnTypeInterface $type, ResolvedTypeInterface $parent = null)
    {
        $this->type = $type;
        $this->parent = $parent;
    }

    /**
     * @return OptionsResolverInterface
     */
    public function getOptionsResolver()
    {
        if (null === $this->optionsResolver) {

            if (null !== $this->parent) {
                $this->optionsResolver = clone $this->parent->getOptionsResolver();
            } else {
                $this->optionsResolver = new OptionsResolver();
            }

            $this->type->setDefaultOptions($this->optionsResolver);

        }

        return $this->optionsResolver;
    }

    /**
     * @inheritdoc
     */
    public function buildView(ColumnView $view, DataTableView $dataTableView, array $options = array())
    {
        if (null !== $this->parent) {
            $this->parent->buildView($view, $dataTableView, $options);
        }

        $this->type->buildView($view, $dataTableView, $options);
    }
}