<?php
/**
 * @package    Niambie
 * @copyright  2013 Amy Stephen. All rights reserved.
 * @license    MIT
 */
namespace Molajo\Plugin\Head;

use Molajo\Plugin\Plugin\Plugin;
use Molajo\Service\Services;

defined('NIAMBIE') or die;

/**
 * @package     Niambie
 * @license     MIT
 * @since       1.0
 */
class HeadPlugin extends Plugin
{
    /**
     * Prepares data for the JS links and Declarations for the Head
     *
     * @return boolean
     * @since   1.0
     */
    public function onBeforeRead()
    {
        if (strtolower($this->get('template_view_path_node', '', 'parameters')) == 'head') {
        } else {
            return true;
        }

        /** JS */
        $controllerClass = CONTROLLER_CLASS;
        $controller      = new $controllerClass();
        $controller->getModelRegistry('dbo', ASSETS_LITERAL);
        $controller->setDataobject();
        $controller->connectDatabase();
        $controller->set('model_parameter', 'Js', 'model_registry');

        $temp_query_results = $controller->getData('getAssets');

        Services::Registry()->set(ASSETS_LITERAL, 'js', $temp_query_results);

        /** JS Declarations */
        $controllerClass = CONTROLLER_CLASS;
        $controller      = new $controllerClass();
        $controller->getModelRegistry('dbo', ASSETS_LITERAL);
        $controller->setDataobject();
        $controller->connectDatabase();

        $controller->set('model_parameter', 'JsDeclarations', 'model_registry');
        $temp_query_results = $controller->getData('getAssets');

        Services::Registry()->set(ASSETS_LITERAL, 'jsdeclarations', $temp_query_results);

        return true;
    }
}
