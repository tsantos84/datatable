<?php

namespace Tavs\DataTable\ColumnType;

use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Class SelectAllColumnType
 * @package Tavs\DataTable\ColumnType
 */
class SelectAllColumnType extends AbstractColumnType
{
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'mapped' => false,
            'title' => '<input type="checkbox" class="select-all" />',
            'attr' => array(
                'class' => 'select-row'
            )
        ));
    }

    /**
     * @return null
     */
    public function getParent()
    {
        return 'checkbox';
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'select-all';
    }
}