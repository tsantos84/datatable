<?php

namespace Tavs\DataTable\ColumnType;

use Tavs\DataTable\ColumnView;
use Tavs\DataTable\DataTableView;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Class TextColumnType
 *
 * @package Tavs\DataTable\ColumnType
 */
class TextColumnType extends AbstractColumnType
{
    /**
     * @inheritdoc
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults([
            'block_name'    => 'column_text',
            'align'         => 'left',
            'class_name'    => '',
            'searchable'    => false,
            'orderable'     => false,
            'width'         => null,
            'visible'       => true,
            'title'         => null,
            'property_path' => null,
            'mapped'        => true,
            'entity_alias'  => null,
            'extra_column'  => false
        ]);

        $resolver->setAllowedTypes([
            'block_name'    => 'string',
            'align'         => 'string',
            'class_name'    => 'string',
            'searchable'    => 'bool',
            'orderable'     => 'bool',
            'width'         => ['null', 'int'],
            'visible'       => 'bool',
            'title'         => ['null', 'string'],
            'property_path' => ['null', 'string'],
            'mapped'        => 'bool',
            'entity_alias'  => ['null', 'string'],
            'extra_column'  => 'bool'
        ]);

        $resolver->setRequired([
            'name'
        ]);

        $resolver->setNormalizers([
            'title'         => function (Options $options, $value) {
                return null === $value ? trim(ucwords(preg_replace('/[\._]/', ' ', $options['name']))) : $value;
            },
            'property_path' => function (Options $options, $value) {
                return null === $value ? strtolower($options['name']) : $value;
            }
        ]);
    }

    /**
     * @inheritdoc
     */
    public function buildView(ColumnView $view, DataTableView $dataTableView, array $options)
    {
        $view->vars = array_replace($view->vars, [
            'datatable'     => $dataTableView,
            'name'          => $options['name'],
            'title'         => $options['title'],
            'visible'       => $options['visible'],
            'orderable'     => $options['orderable'],
            'property_path' => $options['property_path'],
            'mapped'        => $options['mapped'],
            'block_name'    => $options['block_name'],
            'searchable'    => $options['searchable'],
            'class_name'    => $options['class_name'],
            'extra_column'  => $options['extra_column']
        ]);

        if (null !== $options['align']) {
            $view['class_name'] .= ' text-' . $options['align'];
        }

        $view['class_name'] = trim($view['class_name']);
    }

    /**
     * @inheritdoc
     */
    public function getName()
    {
        return 'text';
    }
}