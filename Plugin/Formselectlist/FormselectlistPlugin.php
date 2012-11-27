<?php
/**
 * @package    Molajo
 * @copyright  2012 Individual Molajo Contributors. All rights reserved.
 * @license    GNU GPL v 2, or later and MIT, see License folder
 */
namespace Molajo\Plugin\Formselectlist;

use Molajo\Plugin\Plugin\Plugin;
use Molajo\Service\Services;

defined('MOLAJO') or die;

/**
 * @package     Molajo
 * @subpackage  Plugin
 * @since       1.0
 */
class FormselectlistPlugin extends Plugin
{
    /**
     * Prepares listbox contents
     *
     * @return boolean
     * @since   1.0
     */
    public function onBeforeInclude()
    {
        $results = Services::Registry()->get(DATA_OBJECT_TEMPLATE, $this->get('template_view_path_node'));
        if (count($results) > 0) {
            return true;
        }

        $datalist = Services::Registry()->get(DATA_OBJECT_PARAMETERS, 'datalist', '');
        if ($datalist == '') {
            return true;
        }

        $query_results = Services::Registry()->get(DATA_OBJECT_DATALIST, $datalist);

        if (count($query_results) > 0) {

        } else {
            if ($datalist === false || trim($datalist) == '') {
                return true;
            }

            $results = Services::Text()->getDatalist($datalist, DATA_OBJECT_DATALIST, $this->parameters);
            if ($results === false) {
                return true;
            }

            $selected = $this->get('selected', null);

            $query_results = Services::Text()->buildSelectlist(
                $datalist,
                $results[0]->listitems,
                $results[0]->multiple,
                $results[0]->size,
                $this->get('selected', null)
            );
        }

        $this->set('model_type', DATA_OBJECT_LITERAL);
        $this->set('model_name', DATA_OBJECT_TEMPLATE);
        $this->set('model_query_object', QUERY_OBJECT_LIST);

        $this->parameters['model_type'] = DATA_OBJECT_LITERAL;
        $this->parameters['model_name'] = DATA_OBJECT_TEMPLATE;

        Services::Registry()->set(
            DATA_OBJECT_TEMPLATE,
            $this->get('template_view_path_node'),
            $query_results
        );

        return true;
    }

    /**
     * Remove Registry just rendered
     *
     * @return  object
     * @since   1.0
     */
    public function onAfterInclude()
    {
        Services::Registry()->delete(DATA_OBJECT_TEMPLATE, $this->get('template_view_path_node'));
        return $this;
    }
}
