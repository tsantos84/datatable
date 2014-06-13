<?php

namespace Tavs\DataTable\ColumnType;

use Tavs\DataTable\ColumnView;
use Tavs\DataTable\DataTableView;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Interface ColumnTypeInterface
 * @package Tavs\DataTable\ColumnType
 */
interface ColumnTypeInterface
{
    /**
     * @return null
     */
    public function getParent();

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver);

    /**
     * @param ColumnView $view
     * @param DataTableView $dataTableView
     * @param array $options
     * @return mixed
     */
    public function buildView(ColumnView $view, DataTableView $dataTableView, array $options);

    /**
     * @return string
     */
    public function getName();
}