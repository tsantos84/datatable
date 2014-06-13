<?php
/**
 * Created by PhpStorm.
 * User: c0339564
 * Date: 28/05/14
 * Time: 09:59
 */
namespace Tavs\DataTable;

use Symfony\Component\OptionsResolver\OptionsResolverInterface;


/**
 * Class DataTableAbstract
 * @package Tavs\DataTable
 */
interface DataTableTypeInterface
{
    /**
     * @param DataTableBuilderInterface $builder
     * @param array $options
     * @return void
     */
    public function buildDataTable(DataTableBuilderInterface $builder, array $options = array());

    /**
     * @param OptionsResolverInterface $resolver
     * @return void
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver);

    /**
     * @param DataTableView $view
     * @param array $options
     * @return void
     */
    public function buildView(DataTableView $view, array $options = array());

    /**
     * @return string
     */
    public function getName();
}