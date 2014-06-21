<?php

namespace Tavs\DataTable\ColumnType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Tavs\DataTable\ColumnView;
use Tavs\DataTable\DataTableView;

/**
 * Class DateColumnType
 * @package Tavs\DataTable\ColumnType
 */
class DateTimeColumnType extends AbstractColumnType
{
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'block_name' => 'column_date_time',
            'format' => 'd/m/Y'
        ));
    }

    /**
     * @param ColumnView $view
     * @param DataTableView $dataTableView
     * @param array $options
     * @return mixed|void
     */
    public function buildView(ColumnView $view, DataTableView $dataTableView, array $options)
    {
        $view['format'] = $options['format'];
    }

    /**
     * @return null
     */
    public function getParent()
    {
        return 'text';
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'date_time';
    }
}