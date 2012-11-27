<?php
/**
 * @package    Molajo
 * @copyright  2012 Individual Molajo Contributors. All rights reserved.
 * @license    GNU GPL v 2, or later and MIT, see License folder
 */
namespace Molajo\Plugin\Pagetypeedit;

use Molajo\Plugin\Plugin\Plugin;
use Molajo\Service\Services;
use Molajo\Helpers;

defined('MOLAJO') or die;

/**
 * @package     Molajo
 * @subpackage  Plugin
 * @since       1.0
 */
class PagetypeeditPlugin extends Plugin
{
    /**
     * Prepares Configuration Data
     *
     * @return   boolean
     * @since    1.0
     */
    public function onBeforeParse()
    {
        if (strtolower($this->get('page_type')) == 'edit') {
        } else {
            return true;
        }

        $resource_model_type = $this->get('model_type');
        $resource_model_name = $this->get('model_name');

        //todo - submenu
        Services::Registry()->set('Navigation', 'SectionSubmenu', array());

        /** Form Service */
        $form = Services::Form();

        /** Set Input */
        $form->set('namespace', strtolower($this->get('page_type')));

        $form->set('model_type', $this->get('model_type'));
        $form->set('model_name', $this->get('model_name'));
        $form->set('model_registry_name',
            ucfirst(strtolower($this->get('model_name'))) . ucfirst(strtolower($this->get('model_type')))
        );

        $form->set('extension_instance_id', $this->get('criteria_extension_instance_id'));

        $form->set('data', Services::Registry()->get(DATAOBJECT_MODEL_TYPE, PRIMARY_MODEL_NAME));

        /** Parameters */
        $form->set('parameters', Services::Registry()->getArray('ResourcesSystemParameters'));
        $form->set('parameter_fields', Services::Registry()->get('ResourcesSystem', 'parameters'));

        /** Metadata */
        $form->set('metadata', Services::Registry()->getArray('ResourcesSystemMetadata'));
        $form->set('metadata_fields', Services::Registry()->get('ResourcesSystem', 'metadata'));

        /** Customfields */
        $form->set('customfields', Services::Registry()->getArray('ResourcesSystemCustomfields'));
        $form->set('customfields_fields', Services::Registry()->get('ResourcesSystem', 'customfields'));
echo Services::Registry()->get('ResourcesSystemParameters', 'edit_array');

        /** Build Fieldsets and Fields */
        $pageFieldsets = $form->execute(Services::Registry()->get('ResourcesSystemParameters', 'edit_array'));

        /** Set the View Model Parameters and Populate the Registry used as the Model */
        $current_page = $form->getPages(
            $pageFieldsets[0]->page_array,
            $pageFieldsets[0]->page_count
        );

        $this->set('request_model_type', $this->get('model_type'));
        $this->set('request_model_name', $this->get('model_name'));

        $this->set('model_type', DATAOBJECT_MODEL_TYPE);
        $this->set('model_name', PRIMARY_MODEL_NAME);
        $this->set('model_query_object', QUERY_OBJECT_LIST);

        $this->parameters['model_type'] = DATAOBJECT_MODEL_TYPE;
        $this->parameters['model_name'] = PRIMARY_MODEL_NAME;

        Services::Registry()->set(
            PRIMARY_MODEL_NAME,
            PRIMARY_MODEL_NAME_RESULTS,
            $current_page
        );

        return true;
    }
}
