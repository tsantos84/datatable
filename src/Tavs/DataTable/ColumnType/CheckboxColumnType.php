<?php

namespace Tavs\DataTable\ColumnType;
use Tavs\DataTable\ColumnView;
use Tavs\DataTable\DataTableView;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Class CheckboxColumnType
 * @package Tavs\DataTable\ColumnType
 */
class CheckboxColumnType extends AbstractColumnType
{
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'block_name' => 'column_checkbox',
            'searchable' => false,
            'align' => 'center',
            'attr' => array()
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
        $view['attr'] = $options['attr'];
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
        return 'checkbox';
    }
}