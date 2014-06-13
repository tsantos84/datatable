<?php

namespace Tavs\DataTable\ColumnType;
use Tavs\DataTable\ColumnView;
use Tavs\DataTable\DataTableView;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Class ActionsColumnType
 * @package Tavs\DataTable\ColumnType
 */
class ActionsColumnType extends AbstractColumnType
{
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'block_name' => 'column_actions',
            'mapped' => false,
            'title' => 'Ações',
            'edit_label' => 'Editar',
            'remove_label' => 'Remover'
        ));

        $resolver->setRequired(array(
            'edit_url',
            'remove_url'
        ));
    }

    /**
     * @inheritdoc
     */
    public function buildView(ColumnView $view, DataTableView $dataTableView, array $options)
    {
        $view->vars = array_merge($view->vars, array(
            'edit_url' => $options['edit_url'],
            'edit_label' => $options['edit_label'],
            'remove_url' => $options['remove_url'],
            'remove_label' => $options['remove_label']
        ));
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
        return 'actions';
    }
}