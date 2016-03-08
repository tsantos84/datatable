<?php

namespace Tavs\DataTable\Twig\Extension;

use Tavs\DataTable\ColumnView;
use Tavs\DataTable\DataSource\DataSourceInterface;
use Tavs\DataTable\DataTableView;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\PropertyAccess\PropertyAccessor;
use Twig_Environment;

/**
 * Class DataTableExtension
 * @package Tavs\DataTable\Twig\Extension
 */
class DataTableExtension extends \Twig_Extension implements \Twig_Extension_InitRuntimeInterface
{
    /**
     * @var string
     */
    private $resource;

    /**
     * @var \Twig_Environment
     */
    private $environment;

    /**
     * @var \Twig_Template
     */
    private $template;

    /**
     * @var PropertyAccessor
     */
    private $accessor;

    /**
     * @param $resource
     */
    public function __construct($resource)
    {
        $this->resource = $resource;
        $this->accessor = PropertyAccess::createPropertyAccessor();
    }

    /**
     * @param Twig_Environment $environment
     */
    public function initRuntime(Twig_Environment $environment)
    {
        $this->environment = $environment;
    }

    /**
     * @return array
     */
    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('datatable_head', array($this, 'renderHead'), array(
                'is_safe' => array('html')
            )),
            new \Twig_SimpleFunction('datatable_body', array($this, 'renderBody'), array(
                'is_safe' => array('html')
            )),
            new \Twig_SimpleFunction('datatable_column_widget', array($this, 'renderColumnWidget'), array(
                'is_safe' => array('html')
            )),
            new \Twig_SimpleFunction('datatable_options', array($this, 'renderOptions'), array(
                'is_safe' => array('html')
            )),
            new \Twig_SimpleFunction('datatable_record_identifier', array($this, 'getRecordIdentifier')),
        );
    }

    /**
     * @param DataTableView $view
     * @return string
     */
    public function renderHead(DataTableView $view)
    {
        return $this->renderBlock('datatable_head', array('datatable' => $view));
    }

    /**
     * @param DataTableView $view
     * @return string
     */
    public function renderBody(DataTableView $view)
    {
        return $this->renderBlock('datatable_body', array('datatable' => $view));
    }

    /**
     * @param ColumnView $view
     * @param mixed $record
     * @return string
     */
    public function renderColumnWidget(ColumnView $view, $record)
    {
        if ($view['mapped']) {

            $path = $view['property_path'];

            /**
             * Doctrine mixed result detected
             *
             * @see http://docs.doctrine-project.org/en/2.1/reference/dql-doctrine-query-language.html#pure-and-mixed-results
             */
            if (is_array($record) && array_key_exists(0, $record)) {
                if (true === $view['extra_column']) {
                    unset($record[0]);
                } else {
                    $record = $record[0];
                }
            }

            // add "[]" to the path for array access
            if (is_array($record) && strpos($path, '[') === false) {
                $path = sprintf('[%s]', $path);
            }

            $value = $this->accessor->getValue($record, $path);
        } else {
            $value = null;
        }

        return $this->renderBlock($view->vars['block_name'], array_merge($view->vars, array(
            'value' => $value,
            'record' => $record
        )));
    }

    /**
     * @param DataTableView $view
     * @param Request $request
     * @return JsonResponse
     */
    public function getDataTableResponse(DataTableView $view, Request $request)
    {
        $params = $request->isMethod('post')
            ? $request->request
            : $request->query;

        /** @var DataSourceInterface $datasource */
        $datasource = $view['datasource'];

        $json = array(
            'draw' => $params->getInt('draw'),
            'recordsTotal' => $datasource->getTotal(),
            'recordsFiltered' => $datasource->getTotal(),
            'data' => array()
        );

        foreach ($datasource as $record) {
            $row = array();
            foreach ($view->columns as $column) {
                $row[] = $this->renderColumnWidget($column, $record);
            }
            $json['data'][] = $row;
        }

        return $json;
    }

    /**
     * @param DataTableView $view
     * @param array $extra
     * @return string
     */
    public function renderOptions(DataTableView $view, array $extra = [])
    {
        $options = array_merge(['columns' => []], $view['config']->vars, $extra);

        foreach ($view->columns as $column) {

            $c = [
                'title' => $column['title'],
                'searchable' => $column['searchable'],
                'orderable' => $column['orderable'],
                'visible' => $column['visible'],
                'name' => $column['name'],
                'className' => $column['class_name']
            ];

            $options['columns'][] = $c;
        }

        return json_encode($options);
    }

    /**
     * @param DataTableView $view
     * @param $record
     * @return array
     */
    public function getRecordIdentifier(DataTableView $view, $record)
    {
        $identifier = array();

        foreach ($view['identifier'] as $name) {

            $path = $name;

            if (is_array($record)) {
                $path = sprintf('[%s]', $name);
            }

            $identifier[$name] = $this->accessor->getValue($record, $path);
        }

        return $identifier;
    }

    /**
     * Returns the name of the extension.
     *
     * @return string The extension name
     */
    public function getName()
    {
        return 'datatable';
    }

    /**
     * Carrega o primeiro template da lista de recursos
     */
    private function getTemplate()
    {
        if (null === $this->template) {
            $this->template = $this->environment->loadTemplate($this->resource);
        }

        return $this->template;
    }

    /**
     * @param $block
     * @param array $view
     * @return string
     * @throws \InvalidArgumentException
     */
    private function renderBlock($block, array $view)
    {
        $template = $this->getTemplate();
        return $template->renderBlock($block, $view);
    }
}