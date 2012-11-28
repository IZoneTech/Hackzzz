<?php
/**
 * @package    Molajo
 * @copyright  2012 Individual Molajo Contributors. All rights reserved.
 * @license    GNU GPL v 2, or later and MIT, see License folder
 */
namespace Molajo\Plugin\Detailitem;

use Molajo\Service\Services;
use Molajo\Plugin\Plugin\Plugin;

defined('MOLAJO') or die;

/**
 * Detailitem
 *
 * @package     Molajo
 * @subpackage  Plugin
 * @since       1.0
 */
class DetailitemPlugin extends Plugin
{

    /**
     * Prepares Data for non-menuitem single content item requests
     *
     * @return boolean
     * @since   1.0
     */
    public function onBeforeParse()
    {
        return true;
        if (Services::Registry()->exists(PARAMETERS_LITERAL, 'menuitem_id')) {
            if ((int) Services::Registry()->get(PARAMETERS_LITERAL, 'menuitem_id') == 0) {
            } else {
                return true;
            }
        }

        if (Services::Registry()->exists(PARAMETERS_LITERAL, 'criteria_source_id')) {
            if ((int) Services::Registry()->get(PARAMETERS_LITERAL, 'criteria_source_id') == 0) {
                return true; // request for list;
            } else {
                // request for item is handled by this method
            }
        }

        $this->set('request_model_type', $this->get('model_type'));
        $this->set('request_model_name', $this->get('model_name'));

        $this->set('model_type', DATA_OBJECT_LITERAL);
        $this->set('model_name', PRIMARY_LITERAL);
        $this->set('model_query_object', QUERY_OBJECT_LIST);

        $this->parameters['model_type'] = DATA_OBJECT_LITERAL;
        $this->parameters['model_name'] = PRIMARY_LITERAL;

        //$this->getComments();
        return true;
    }

    /**
     * Grid Query: results stored in Plugin registry
     *
     * @return bool
     * @since   1.0
     */
    protected function getComments()
    {
        $controllerClass = CONTROLLER_CLASS;
        $controller = new $controllerClass();
        $results = $controller->getModelRegistry(DATASOURCE_LITERAL, 'Comments');
        if ($results === false) {
            return false;
        }

        $results = $controller->setDataobject();
        if ($results === false) {
            return false;
        }

        $controller->model->query->where('a.root = ' . $this->get('id'));
        $controller->set('model_offset', 0);
        $controller->set('model_count', 10);

        $query_results = $controller->getData(QUERY_OBJECT_LIST);

        echo '<pre><br /><br />';
        var_dump($query_results);
        echo '<br /><br /></pre>';

        echo '<br /><br />';
        echo $controller->model->query->__toString();
        echo '<br /><br />';

        die;

        Services::Registry()->set(DATA_OBJECT_LITERAL, DATALIST_LITERAL, $query_results);

        return true;
    }
}
