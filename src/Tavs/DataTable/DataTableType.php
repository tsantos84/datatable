<?php

namespace Tavs\DataTable;

use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Class DataTableAbstract
 * @package Tavs\DataTable
 */
class DataTableType implements DataTableTypeInterface
{
    /**
     * @inheritdoc
     */
    public function buildDataTable(DataTableBuilderInterface $builder, array $options = array())
    {
    }

    /**
     * @inheritdoc
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'identifier' => 'id',
            'server_side' => true,
            'ajax' => null,
            'ajax_url' => null,
            'ajax_type' => 'POST',
            'dom' => null,
//            'paging' => true,
//            'auto_width' => true,
//            'ordering' => true,
//            'processing' => true,
//            'searching' => true,
//            'state_save' => false,
//            'dom' => null,
//            'length_menu' => null,
        ));

        $resolver->setAllowedTypes(array(
            'server_side' => 'bool',
            'ajax' => array('null', 'string'),
            'ajax_url' => array('null', 'string'),
            'ajax_type' => array('null', 'string'),
            'dom' => array('null', 'string'),
//            'paging' => 'bool',
//            'auto_width' => 'bool',
//            'ordering' => 'bool',
//            'processing' => 'bool',
//            'searching' => 'bool',
//            'state_save' => 'bool',
//            'length_menu' => array('null', 'array')
        ));

        $resolver->setNormalizers(array(
            'identifier' => function (Options $options, $value) {
                if (is_string($value)) {
                    $value = array($value);
                }
                return $value;
            }
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'datatable';
    }

    /**
     * @inheritdoc
     */
    public function buildView(DataTableView $view, array $options = array())
    {
        $view['identifier'] = $options['identifier'];
        $config = $view['config'] = new View();

        $this->buildOptionsView($config, $options);
        $this->buildAjaxView($config, $options);
    }

    /**
     * @param View $view
     * @param array $options
     */
    protected function buildOptionsView(View $view, array $options = array())
    {
        if ($options['dom']) {
            $view['dom'] = $options['dom'];
        }
    }

    /**
     * @param View $view
     * @param array $options
     */
    protected function buildAjaxView(View $view, array $options = array())
    {
        if ($options['server_side']) {

            $view['serverSide'] = $options['server_side'];

            if (is_string($options['ajax'])) {

                $view['ajax'] = $options['ajax'];

            } else {

                $view->vars['ajax'] = array();

                if ($options['ajax_url']) {
                    $view->vars['ajax']['url'] = $options['ajax_url'];
                }

                if ($options['ajax_type']) {
                    $view->vars['ajax']['type'] = $options['ajax_type'];
                }
            }

        }
    }
}