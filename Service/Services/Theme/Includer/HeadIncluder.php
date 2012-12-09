<?php
/**
 * @package    Molajo
 * @copyright  2012 Amy Stephen. All rights reserved.
 * @license    GNU GPL v 2, or later and MIT, see License folder
 */
namespace Molajo\Service\Services\Theme\Includer;

use Molajo\Helpers;
use Molajo\Service\Services;
use Molajo\Service\Services\Theme\Includer;

defined('MOLAJO') or die;

/**
 * Head
 *
 * @package     Molajo
 * @subpackage  Includer
 * @since       1.0
 */
Class HeadIncluder extends Includer
{
    /**
     * @return  null
     * @since   1.0
     */
    public function __construct($name = null, $type = null)
    {
        Services::Registry()->set(PARAMETERS_LITERAL, 'extension_catalog_type_id', 0);
        parent::__construct($name, $type);
        Services::Registry()->set(PARAMETERS_LITERAL, 'criteria_html_display_filter', false);

        return;
    }

    /**
     *  Retrieve default values for Rendering, if not provided by extension
     *
     * @return  bool
     * @since   1.0
     */
    protected function setRenderCriteria()
    {
        Services::Registry()->set(PARAMETERS_LITERAL, 'criteria_display_view_on_no_results', 1);

        Services::Registry()->set(PARAMETERS_LITERAL, 'model_type', ASSETS_LITERAL);

        if ($this->type == 'defer') {

            if ((int) Services::Registry()->get('parameters', 'template_view_id', 0) == 0) {
                Services::Registry()->set(PARAMETERS_LITERAL, 'template_view_id',
                    Services::Registry()->get(CONFIGURATION_LITERAL, 'defer_template_view_id'));
            }

            if ((int) Services::Registry()->get('parameters', 'wrap_view_id', 0) == 0) {
                Services::Registry()->set(PARAMETERS_LITERAL, 'wrap_view_id',
                    Services::Registry()->get(CONFIGURATION_LITERAL, 'defer_wrap_view_id'));
            }

        } else {
            if ((int) Services::Registry()->get('parameters', 'template_view_id', 0) == 0) {
                Services::Registry()->set(PARAMETERS_LITERAL, 'template_view_id',
                    Services::Registry()->get(CONFIGURATION_LITERAL, 'head_template_view_id'));
            }
            if ((int) Services::Registry()->get('parameters', 'wrap_view_id', 0) == 0) {
                Services::Registry()->set(PARAMETERS_LITERAL, 'wrap_view_id',
                    Services::Registry()->get(CONFIGURATION_LITERAL, 'head_wrap_view_id'));
            }
        }

        /** Save existing parameters */
        $savedParameters = array();
        $temp = Services::Registry()->getArray(PARAMETERS_LITERAL);

        if (is_array($temp) && count($temp) > 0) {
            foreach ($temp as $key => $value) {
                if (is_array($value)) {
                    $savedParameters[$key] = $value;

                } elseif ($value === 0 || trim($value) == '' || $value === null) {

                } else {
                    $savedParameters[$key] = $value;
                }
            }
        }

        /** Template  */
        Helpers::View()->get(Services::Registry()->get('parameters', 'template_view_id'),
            CATALOG_TYPE_TEMPLATE_VIEW_LITERAL);

        /** Merge Parameters in (Pre-wrap) */
        if (is_array($savedParameters) && count($savedParameters) > 0) {
            foreach ($savedParameters as $key => $value) {
                Services::Registry()->set(PARAMETERS_LITERAL, $key, $value);
            }
        }
        /** Default Wrap if needed */
        $wrap_view_id = Services::Registry()->get('parameters', 'wrap_view_id');
        Services::Registry()->set(PARAMETERS_LITERAL, 'wrap_view_path_node',
                    Helpers::Extension()->getExtensionNode((int) $wrap_view_id));
        $wrap_view_title = Services::Registry()->get('parameters', 'wrap_view_path_node');

        Services::Registry()->set(PARAMETERS_LITERAL, 'wrap_view_title', $wrap_view_title);
        Services::Registry()->set(PARAMETERS_LITERAL, 'wrap_view_path',
            Helpers::View()->getPath($wrap_view_title, CATALOG_TYPE_WRAP_VIEW_LITERAL));
        Services::Registry()->set(PARAMETERS_LITERAL, 'wrap_view_path_url',
            Helpers::View()->getPathURL($wrap_view_title, CATALOG_TYPE_WRAP_VIEW_LITERAL));
        Services::Registry()->set(PARAMETERS_LITERAL, 'wrap_view_namespace',
            Helpers::View()->getNamespace($wrap_view_title, CATALOG_TYPE_WRAP_VIEW_LITERAL));

        if (Services::Registry()->exists(PARAMETERS_LITERAL, 'wrap_view_role')) {
        } else {
            Services::Registry()->set(PARAMETERS_LITERAL, 'wrap_view_role', '');
        }
        if (Services::Registry()->exists(PARAMETERS_LITERAL, 'wrap_view_property')) {
        } else {
            Services::Registry()->set(PARAMETERS_LITERAL, 'wrap_view_property', '');
        }
        if (Services::Registry()->exists(PARAMETERS_LITERAL, 'wrap_view_header_level')) {
        } else {
            Services::Registry()->set(PARAMETERS_LITERAL, 'wrap_view_header_level', '');
        }
        if (Services::Registry()->exists(PARAMETERS_LITERAL, 'wrap_view_show_title')) {
        } else {
            Services::Registry()->set(PARAMETERS_LITERAL, 'wrap_view_show_title', '');
        }
        if (Services::Registry()->exists(PARAMETERS_LITERAL, 'wrap_view_show_subtitle')) {
        } else {
            Services::Registry()->set(PARAMETERS_LITERAL, 'wrap_view_show_subtitle', '');
        }
        Services::Registry()->delete(PARAMETERS_LITERAL, 'item*');
        Services::Registry()->delete(PARAMETERS_LITERAL, 'list*');
        Services::Registry()->delete(PARAMETERS_LITERAL, 'form*');
        Services::Registry()->delete(PARAMETERS_LITERAL, 'menuitem');

        Services::Registry()->sort(PARAMETERS_LITERAL);

        return true;
    }
}
