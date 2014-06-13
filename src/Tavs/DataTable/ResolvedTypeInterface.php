<?php

namespace Tavs\DataTable;

use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Class ResolvedType
 * @package Tavs\DataTable
 */
interface ResolvedTypeInterface
{
    /**
     * @return OptionsResolverInterface
     */
    public function getOptionsResolver();

    /**
     * @param ColumnView $view
     * @param DataTableView $dataTableView
     * @param array $options
     * @return void
     */
    public function buildView(ColumnView $view, DataTableView $dataTableView, array $options = array());
}