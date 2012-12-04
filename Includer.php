<?php
/**
 * @package    Molajo
 * @copyright  2012 Amy Stephen. All rights reserved.
 * @license    GNU GPL v 2, or later and MIT, see License folder
 */
namespace Molajo;

use Molajo\Service\Services;
use Molajo\MVC\Controller\DisplayController;

defined('MOLAJO') or die;

/**
 * Includer
 *
 * @package     Molajo
 * @subpackage  Extension
 * @since       1.0
 */
class Includer
{
    /**
     * $name
     *
     * @var    string
     * @since  1.0
     */
    protected $name = null;

    /**
     * $type: Head, Message, Profiler, Resource, Tag, Template, Theme, Wrap
     *
     * @var    string
     * @since  1.0
     */
    protected $type = null;

    /**
     * $tag
     *
     * @var    string
     * @since  1.0
     */
    protected $tag = null;

    /**
     * Any defined parameter for the extension can be overridden on the include
     *
     * <include:extension statement attr1=x attr2=y attrN="and-so-on" />
     *
     * @var    array
     * @since  1.0
     */
    protected $attributes = array();

    /**
     * From the MVC to be processed by Plugins and passed back to Includer
     *
     * @var    string
     * @since  1.0
     */
    protected $rendered_output = null;

    /**
     * @param   string  $name
     * @param   string  $type
     *
     * @return  null
     * @since   1.0
     */
    public function __construct($name = null, $type = null)
    {
        $this->name = $name;
        $this->type = $type;

        Services::Registry()->createRegistry('Include');

        Services::Registry()->set(PARAMETERS_LITERAL, 'includer_name', $this->name);
        Services::Registry()->set(PARAMETERS_LITERAL, 'includer_type', $this->type);

        Services::Registry()->copy(ROUTE_PARAMETERS_LITERAL, PARAMETERS_LITERAL, 'Criteria*');
        Services::Registry()->copy(ROUTE_PARAMETERS_LITERAL, PARAMETERS_LITERAL, 'Enable*');
        Services::Registry()->copy(ROUTE_PARAMETERS_LITERAL, PARAMETERS_LITERAL, 'Request*');
        Services::Registry()->copy(ROUTE_PARAMETERS_LITERAL, PARAMETERS_LITERAL, 'Theme*');
        Services::Registry()->copy(ROUTE_PARAMETERS_LITERAL, PARAMETERS_LITERAL, 'Page*');

        return;
    }

    /**
     * process
     *
     * - Loads Metadata (only Theme Includer)
     * - Loads Assets for Extension
     * - Activates Controller for Task
     * - Returns Rendered Output to Parse for <include:type /> replacement
     *
     * @param   $attributes <include:type attr1=x attr2=y attr3=z ... />
     *
     * @return  mixed
     * @since   1.0
     */
    public function process($attributes = array())
    {
        Services::Registry()->deleteRegistry('Tempattributes');
        Services::Registry()->createRegistry('Tempattributes');
        $this->attributes = $attributes;
        $this->getAttributes();

        $this->getExtension();

        $results = $this->setRenderCriteria();
        if ($results === false) {
            return false;
        }

        $this->loadPlugins();

        $this->onBeforeIncludeEvent();

        $rendered_output = $this->invokeMVC();

        if ($rendered_output == ''
            && Services::Registry()->get('parameters', 'criteria_display_view_on_no_results') == 0
        ) {
        } else {
            $this->loadMedia();
            $this->loadViewMedia();
        }

        $this->onAfterIncludeEvent();

        return $this->rendered_output;
    }

    /**
     * Use the view and/or wrap criteria ife specified on the <include statement
     *
     * @return  null
     * @since   1.0
     */
    protected function getAttributes()
    {
        if (count($this->attributes) > 0) {
        } else {
            return;
        }

        //todo filter input appropriately
        //todo case statements
        foreach ($this->attributes as $name => $value) {

            $name = strtolower($name);
            if ($name == 'name' || $name == 'title') {

                if ($this->name == strtolower(CATALOG_TYPE_TEMPLATE_VIEW_LITERAL)) {

                    if ((int)$value > 0) {
                        $template_id = (int)$value;
                        $template_title = Helpers::Extension()->getExtensionNode($template_id);

                    } else {
                        $template_title = ucfirst(strtolower(trim($value)));
                        $template_id = Helpers::Extension()
                            ->getInstanceID(CATALOG_TYPE_TEMPLATE_VIEW, $template_title);
                    }

                    Services::Registry()->set(PARAMETERS_LITERAL, 'template_view_id', $template_id);
                    Services::Registry()->set(PARAMETERS_LITERAL, 'template_view_path_node', $template_title);
                    Services::Registry()->set(PARAMETERS_LITERAL, 'extension_title', $template_title);
                    Services::Registry()->set(PARAMETERS_LITERAL, 'template_view_title', $template_title);

                } else {

                    $value = ucfirst(strtolower(trim($value)));
                    Services::Registry()->set(PARAMETERS_LITERAL, 'extension_title', $value);
                }

            } elseif ($name == 'tag') {
                $this->tag = $value;

            } elseif ($name == strtolower(CATALOG_TYPE_TEMPLATE_VIEW_LITERAL)
                || $name == 'template_view_title'
                || $name == 'template_view'
            ) {
                $value = ucfirst(strtolower(trim($value)));

                $template_id = Helpers::Extension()
                    ->getInstanceID(CATALOG_TYPE_TEMPLATE_VIEW, $value);

                if ((int)$template_id == 0) {
                } else {
                    Services::Registry()->set(PARAMETERS_LITERAL, 'template_view_id', $template_id);
                    Services::Registry()->set(PARAMETERS_LITERAL, 'template_view_path_node', $value);
                    Services::Registry()->set(PARAMETERS_LITERAL, 'extension_title', $value);
                    Services::Registry()->set(PARAMETERS_LITERAL, 'template_view_title', $value);
                }

            } elseif ($name == 'template_view_css_id'
                || $name == 'template_css_id'
                || $name == 'template_id'
                || $name == 'id'
            ) {
                Services::Registry()->set(PARAMETERS_LITERAL, 'template_view_css_id', $value);

            } elseif ($name == 'template_view_css_class'
                || $name == 'template_css_class'
                || $name == 'template_class'
                || $name == 'class'
            ) {
                Services::Registry()->set(PARAMETERS_LITERAL, 'template_view_css_class', str_replace(',', ' ', $value));

            } elseif ($name == strtolower(CATALOG_TYPE_WRAP_VIEW_LITERAL)
                || $name == 'wrap_view_title'
                || $name == 'wrap_view'
                || $name == 'wrap_title'
            ) {

                $value = ucfirst(strtolower(trim($value)));
                $wrap_id = Helpers::Extension()
                    ->getInstanceID(CATALOG_TYPE_WRAP_VIEW, $value);

                if ((int)$wrap_id == 0) {
                } else {
                    Services::Registry()->set(PARAMETERS_LITERAL, 'wrap_view_path_node', $value);
                    Services::Registry()->set(PARAMETERS_LITERAL, 'wrap_view_id', $wrap_id);
                }

            } elseif ($name == 'wrap_view_css_id'
                || $name == 'wrap_css_id'
                || $name == 'wrap_id'
            ) {
                Services::Registry()->set(PARAMETERS_LITERAL, 'wrap_view_css_id', $value);

            } elseif ($name == 'wrap_view_css_class'
                || $name == 'wrap_css_class'
                || $name == 'wrap_class'
            ) {
                Services::Registry()->set(PARAMETERS_LITERAL, 'wrap_view_css_class', str_replace(',', ' ', $value));

            } elseif ($name == 'wrap_view_role'
                || $name == 'wrap_role'
                || $name == 'role'
            ) {
                Services::Registry()->set(PARAMETERS_LITERAL, 'wrap_view_role', str_replace(',', ' ', $value));

            } elseif ($name == 'wrap_view_property'
                || $name == 'wrap_property'
                || $name == 'property'
            ) {
                Services::Registry()->set(PARAMETERS_LITERAL, 'wrap_view_property', str_replace(',', ' ', $value));

            } elseif ($name == 'datalist') {
                Services::Registry()->set(PARAMETERS_LITERAL, 'datalist', $value);
                Services::Registry()->set(PARAMETERS_LITERAL, 'model_type', 'datalist');
                Services::Registry()->set(PARAMETERS_LITERAL, 'model_name', $value);
                Services::Registry()->set(PARAMETERS_LITERAL, 'model_query_object', QUERY_OBJECT_LIST);

            } elseif ($name == 'model_name') {
                Services::Registry()->set(PARAMETERS_LITERAL, 'model_name', $value);

            } elseif ($name == 'model_type') {
                Services::Registry()->set(PARAMETERS_LITERAL, 'model_type', $value);

            } elseif ($name == 'model_query_object'
                || $name == 'query_object'
            ) {
                Services::Registry()->set(PARAMETERS_LITERAL, 'model_query_object', $value);

            } else {
                /** Todo: For security reasons: match field to model registry and filter first */
                Services::Registry()->set('Tempattributes', $name, $value);
            }
        }
    }

    /**
     * getExtension
     *
     * Retrieve extension information after looking up the ID in the extension-specific includer
     *
     * @return  bool
     * @since   1.0
     */
    protected function getExtension()
    {
        return;
    }

    /**
     * Uses Include Request and Attributes (overrides) to set Parameters for Rendering
     *
     * @return  bool
     * @since   1.0
     */
    protected function setRenderCriteria()
    {
        $template_id = 0;
        $template_title = '';

        $saveTemplate = array();
        $temp = Services::Registry()->get('parameters', 'template*');

        if (is_array($temp) && count($temp) > 0) {
            foreach ($temp as $key => $value) {

                if ($key == 'template_view_id'
                    || $key == 'template_view_path_node'
                    || $key == 'template_view_title'
                ) {

                } elseif (is_array($value)) {
                    $saveTemplate[$key] = $value;

                } elseif ($value === 0
                    || trim($value) == ''
                    || $value === null
                ) {

                } else {
                    $saveTemplate[$key] = $value;
                }
            }
        }

        $saveWrap = array();
        $temp = Services::Registry()->get('parameters', 'wrap*');
        $temp2 = Services::Registry()->get('parameters', 'model*');
        $temp3 = array_merge($temp, $temp2);
        $temp2 = Services::Registry()->get('parameters', 'data*');
        $temp = array_merge($temp2, $temp3);

        if (is_array($temp) && count($temp) > 0) {
            foreach ($temp as $key => $value) {

                if (is_array($value)) {
                    $saveWrap[$key] = $value;

                } elseif ($value === 0 || trim($value) == '' || $value === null) {

                } else {
                    $saveWrap[$key] = $value;
                }
            }
        }

        if ($this->type == CATALOG_TYPE_WRAP_VIEW_LITERAL) {
        } else {
            $results = $this->setTemplateRenderCriteria($saveTemplate);
            if ($results === false) {
                return false;
            }
        }

        $results = $this->setWrapRenderCriteria($saveWrap);
        if ($results === false) {
            return false;
        }

        Services::Registry()->delete(PARAMETERS_LITERAL, 'item*');
        Services::Registry()->delete(PARAMETERS_LITERAL, 'list*');
        Services::Registry()->delete(PARAMETERS_LITERAL, 'form*');
        Services::Registry()->delete(PARAMETERS_LITERAL, 'menuitem*');

        Services::Registry()->sort(PARAMETERS_LITERAL);

        $fields = Services::Registry()->get(CONFIGURATION_LITERAL, 'application*');
        if (count($fields) === 0 || $fields === false) {
        } else {
            foreach ($fields as $key => $value) {
                Services::Registry()->set(PARAMETERS_LITERAL, $key, $value);
            }
        }

        $fields = Services::Registry()->getArray('Tempattributes');
        if (count($fields) === 0 || $fields === false) {
        } else {
            foreach ($fields as $key => $value) {
                Services::Registry()->set(PARAMETERS_LITERAL, $key, $value);
            }
        }

        return true;
    }

    /**
     * Process Template Options
     *
     * @param   string  $saveTemplate
     *
     * @return  bool
     * @since   1.0
     */
    protected function setTemplateRenderCriteria($saveTemplate)
    {
        $template_id = (int)Services::Registry()->get('parameters', 'template_view_id');

        if ((int)$template_id == 0) {
            $template_title = Services::Registry()->get('parameters', 'template_view_path_node');
            if (trim($template_title) == '') {
            } else {
                $template_id = Helpers::Extension()
                    ->getInstanceID(CATALOG_TYPE_TEMPLATE_VIEW, $template_title);
                Services::Registry()->set(PARAMETERS_LITERAL, 'template_view_id', $template_id);
            }
        }

        if ((int)$template_id == 0) {
            $template_id = Helpers::View()->getDefault(CATALOG_TYPE_TEMPLATE_VIEW_LITERAL);
            Services::Registry()->set(PARAMETERS_LITERAL, 'template_view_id', $template_id);
        }

        if ((int)$template_id == 0) {
            return false;
        }

        Helpers::View()->get($template_id, CATALOG_TYPE_TEMPLATE_VIEW_LITERAL);

        if (is_array($saveTemplate) && count($saveTemplate) > 0) {
            foreach ($saveTemplate as $key => $value) {
                Services::Registry()->set(PARAMETERS_LITERAL, $key, $value);
            }
        }

        return true;
    }

    /**
     * Process Wrap Options
     *
     * @param   string  @saveWrap
     *
     * @return  bool
     * @since   1.0
     */
    protected function setWrapRenderCriteria($saveWrap)
    {
        if (is_array($saveWrap) && count($saveWrap) > 0) {
            foreach ($saveWrap as $key => $value) {
                if (is_array($value)) {
                    $saveWrap[$key] = $value;

                } elseif ($value === 0 || trim($value) == '' || $value === null) {

                } else {
                    Services::Registry()->set(PARAMETERS_LITERAL, $key, $value);
                }
            }
        }

        $wrap_id = 0;
        $wrap_title = '';

        $wrap_id = (int)Services::Registry()->get('parameters', 'wrap_view_id');

        if ((int)$wrap_id == 0) {
            $wrap_title = Services::Registry()->get('parameters', 'wrap_view_path_node', '');
            if (trim($wrap_title) == '') {
                $wrap_title = 'None';
            }
            $wrap_id = Helpers::Extension()
                ->getInstanceID(CATALOG_TYPE_WRAP_VIEW, $wrap_title);
            Services::Registry()->set(PARAMETERS_LITERAL, 'wrap_view_id', $wrap_id);
        }

        if (is_array($saveWrap) && count($saveWrap) > 0) {
            foreach ($saveWrap as $key => $value) {
                if ($key == 'wrap_view_id' || $key == 'wrap_view_path_node' || $key == 'wrap_view_title') {
                } else {
                    Services::Registry()->set(PARAMETERS_LITERAL, $key, $value);
                }
            }
        }

        $saveWrap = array();
        $temp = Services::Registry()->get('parameters', 'wrap*');
        $temp2 = Services::Registry()->get('parameters', 'model*');
        $temp3 = array_merge($temp, $temp2);
        $temp2 = Services::Registry()->get('parameters', 'data*');
        $temp = array_merge($temp2, $temp3);

        if (is_array($temp) && count($temp) > 0) {
            foreach ($temp as $key => $value) {

                if (is_array($value)) {
                    $saveWrap[$key] = $value;

                } elseif ($value === 0 || trim($value) == '' || $value === null) {

                } else {
                    $saveWrap[$key] = $value;
                }
            }
        }

        Helpers::View()->get($wrap_id, CATALOG_TYPE_WRAP_VIEW_LITERAL);

        if (is_array($saveWrap) && count($saveWrap) > 0) {
            foreach ($saveWrap as $key => $value) {
                if ($key == 'wrap_view_id' || $key == 'wrap_view_path_node' || $key == 'wrap_view_title') {
                } else {
                    Services::Registry()->set(PARAMETERS_LITERAL, $key, $value);
                }
            }
        }

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

        return true;
    }

    /**
     * Load Plugins Overrides from the Template and/or Wrap View folders
     *
     * @return  void
     * @since   1.0
     */
    protected function loadPlugins()
    {
        $node = Services::Registry()->get('parameters', 'extension_name_path_node');

        Services::Event()->registerPlugins(
            Helpers::Extension()->getPath(CATALOG_TYPE_RESOURCE, $node),
            Helpers::Extension()->getNamespace(CATALOG_TYPE_RESOURCE, $node)
        );

        $node = Services::Registry()->get('parameters', 'template_view_path_node');

        Services::Event()->registerPlugins(
            Helpers::Extension()->getPath(CATALOG_TYPE_TEMPLATE_VIEW, $node),
            Helpers::Extension()->getNamespace(CATALOG_TYPE_TEMPLATE_VIEW, $node)
        );

        $node = Services::Registry()->get('parameters', 'wrap_view_path_node');

        Services::Event()->registerPlugins(
            Helpers::Extension()->getPath(CATALOG_TYPE_WRAP_VIEW, $node),
            Helpers::Extension()->getNamespace(CATALOG_TYPE_WRAP_VIEW, $node)
        );

        return;
    }

    /**
     * Loads Media CSS and JS files for extension and related content
     *
     * @return  null
     * @since   1.0
     */
    protected function loadMedia()
    {
        return $this;
    }

    /**
     * Loads Media CSS and JS files for Template and Wrap Views
     *
     * @return  null
     * @since   1.0
     */
    protected function loadViewMedia()
    {
        $priority = Services::Registry()->get('parameters', 'criteria_media_priority_other_extension', 400);

        $file_path = Services::Registry()->get('parameters', 'template_view_path');
        $url_path = Services::Registry()->get('parameters', 'template_view_path_url');

        $css = Services::Asset()->addCssFolder($file_path, $url_path, $priority);
        $js = Services::Asset()->addJsFolder($file_path, $url_path, $priority, 0);
        $defer = Services::Asset()->addJsFolder($file_path, $url_path, $priority, 1);

        $file_path = Services::Registry()->get('parameters', 'wrap_view_path');
        $url_path = Services::Registry()->get('parameters', 'wrap_view_path_url');

        $css = Services::Asset()->addCssFolder($file_path, $url_path, $priority);
        $js = Services::Asset()->addJsFolder($file_path, $url_path, $priority, 0);
        $defer = Services::Asset()->addJsFolder($file_path, $url_path, $priority, 1);

        return $this;
    }

    /**
     * Instantiate the Controller and execute action method, receive rendered output from Controller
     *
     * @return  mixed
     * @since   1.0
     */
    protected function invokeMVC()
    {
        Services::Registry()->sort(PARAMETERS_LITERAL);

        $message = 'Includer->invokeMVC '
            . 'Name ' . $this->name
            . ' Type: ' . $this->type
            . ' Template: ' . Services::Registry()->get('parameters', 'template_view_title');

        $message .= ' Parameters:<br />';
        ob_start();
        $message .= Services::Registry()->get('parameters', '*');
        $message .= ob_get_contents();
        ob_end_clean();

        echo $message;

        Services::Profiler()->set($message, PROFILER_RENDERING, VERBOSE);

        $controller = new DisplayController();
        $controller->set(
            'primary_key_value',
            (int)Services::Registry()->get('parameters', 'source_id'),
            'model_registry'
        );
        $parms = Services::Registry()->getArray(PARAMETERS_LITERAL);
        $cached_output = Services::Cache()->get(CATALOG_TYPE_TEMPLATE_VIEW_LITERAL, implode('', $parms));

        if ($cached_output === false) {
            if (count($parms) > 0) {
                foreach ($parms as $key => $value) {
                    $controller->set($key, $value, 'parameters');
                }
            }
            $this->rendered_output = $controller->execute();
            Services::Cache()->set(CATALOG_TYPE_TEMPLATE_VIEW_LITERAL, implode('', $parms), $this->rendered_output);
        } else {
            $this->rendered_output = $cached_output;
        }

        return;
    }

    /**
     * Schedule Event onBeforeIncludeEvent
     *
     * @return  boolean
     * @since   1.0
     */
    protected function onBeforeIncludeEvent()
    {
        return $this->triggerEvent('onBeforeInclude');
    }

    /**
     * Schedule Event onAfterParseEvent Event
     *
     * @return  boolean
     * @since   1.0
     */
    protected function onAfterIncludeEvent()
    {
        return $this->triggerEvent('onAfterInclude');
    }

    /**
     * Common Method for Includer Events
     *
     * @param   string  $event_name
     * @param   string  $renderedOutput
     *
     * @return  string  rendered output
     * @since   1.0
     */
    protected function triggerEvent($eventName)
    {
        $model_registry = ucfirst(strtolower(Services::Registry()->get(PARAMETERS_LITERAL, 'model_name')))
            . ucfirst(strtolower(Services::Registry()->get(PARAMETERS_LITERAL, 'model_type')));

        $arguments = array(
            'model' => null,
            'model_registry' => Services::Registry()->get($model_registry),
            'parameters' => Services::Registry()->get(PARAMETERS_LITERAL),
            'query_results' => array(),
            'data' => array(),
            'rendered_output' => $this->rendered_output,
            'include_parse_sequence' => null,
            'include_parse_exclude_until_final' => null
        );

        $arguments = Services::Event()->scheduleEvent(
            $eventName,
            $arguments,
            array()
        );

        if (isset($arguments[PARAMETERS_LITERAL])) {
            Services::Registry()->delete(PARAMETERS_LITERAL);
            Services::Registry()->createRegistry(PARAMETERS_LITERAL);
            Services::Registry()->loadArray(PARAMETERS_LITERAL, $arguments[PARAMETERS_LITERAL]);
            Services::Registry()->sort(PARAMETERS_LITERAL);
        }

        if (isset($arguments['rendered_output'])) {
            $this->rendered_output = $arguments['rendered_output'];
        }

        return;
    }
}
