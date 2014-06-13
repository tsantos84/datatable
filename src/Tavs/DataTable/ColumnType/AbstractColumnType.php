<?php

namespace Tavs\DataTable\ColumnType;

use Tavs\DataTable\ColumnView;
use Tavs\DataTable\DataTableView;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Class AbstractColumnType
 * @package Tavs\DataTable\ColumnType
 */
abstract class AbstractColumnType implements ColumnTypeInterface
{
    /**
     * @inheritdoc
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
    }

    /**
     * @inheritdoc
     */
    public function buildView(ColumnView $view, DataTableView $dataTableView, array $options)
    {
    }

    /**
     * @inheritdoc
     */
    public function getParent()
    {
        return null;
    }
}