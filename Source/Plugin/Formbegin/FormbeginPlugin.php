<?php
/**
 * Form Begin Plugin
 *
 * @package    Molajo
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 */
namespace Molajo\Plugin\Formbegin;

use stdClass;
use CommonApi\Event\DisplayInterface;
use Molajo\Plugin\DisplayEventPlugin;

/**
 * Form Begin Plugin
 *
 * @package     Molajo
 * @license     http://www.opensource.org/licenses/mit-license.html MIT License
 * @since       1.0
 */
class FormbeginPlugin extends DisplayEventPlugin implements DisplayInterface
{
    /**
     * Prepares form begin attributes
     *
     * @return  $this
     * @since   1.0
     */
    public function onBeforeRenderView()
    {
        return $this;
        if (isset($this->runtime_data->render->token)) {
        } else {
            return $this;
        }

        if ($this->runtime_data->render->token->name == 'formbegin') {
        } else {
            return $this;
        }

        $pageUrl = $this->runtime_data->page->urls['page'];

        $form_action = $this->runtime_data->render->extension->parameters->form_action;
        if (trim($form_action) == '' || $form_action === null) {
            $form_action = ' action="' . $pageUrl . '"';
        } else {
            $form_action = ' action="' . $form_action . '"';
        }

        $form_method = $this->runtime_data->render->extension->parameters->form_method;
        if (trim($form_method) == '' || $form_method === null) {
            $form_method = ' method="post"';
        } else {
            $form_method = ' method="' . $form_method . '"';
        }

        $form_name = $this->runtime_data->render->extension->parameters->form_name;
        if (trim($form_name) == '' || $form_name === null) {
            $form_name = ' name="' . $this->runtime_data->resource->data->alias . '"';
        } else {
            $form_name = ' name="' . $form_name . '"';
        }

        $form_id = $this->runtime_data->render->extension->parameters->form_id;
        if (trim($form_id) == '' || $form_id === null) {
            $form_id = ' id="' . $this->runtime_data->resource->data->alias . '"';
        } else {
            $form_id = ' id="' . $form_name . '"';
        }

        $form_class = $this->runtime_data->render->extension->parameters->form_class;
        if (trim($form_class) == '' || $form_class === null) {
            $form_class = '';
        } else {
            $form_class = 'class="' . $form_class . '"';
        }

        $temp = $this->runtime_data->render->extension->parameters->form_attributes;
        if (trim($temp) == '' || $temp === null) {
            $form_attributes = '';
        } else {
            $form_attributes = implode(' ', $temp);
        }

        /** Build Query Results for View */
        $temp_row = array();

        $temp_row = new stdClass();

        $temp_row->form_action     = $form_action;
        $temp_row->form_method     = $form_method;
        $temp_row->form_name       = $form_name;
        $temp_row->form_id         = $form_id;
        $temp_row->form_class      = $form_class;
        $temp_row->form_attributes = $form_attributes;
        $temp_row[]                = $temp_row;

        $this->runtime_data->plugin_data->form_begin = $temp_row;

        return $this;
    }
}
