<?php

namespace Tavs\DataTable\ColumnType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Class EmailColumnType
 * @package Tavs\DataTable\ColumnType
 */
class EmailColumnType extends AbstractColumnType
{
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'block_name' => 'column_email'
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
        return 'email';
    }
}