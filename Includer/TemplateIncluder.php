<?php
/**
 * @package    Molajo
 * @copyright  2012 Individual Molajo Contributors. All rights reserved.
 * @license    GNU GPL v 2, or later and MIT, see License folder
 */
namespace Molajo\Includer;

use Molajo\Helpers;
use Molajo\Service\Services;
use Molajo\Includer;

defined('MOLAJO') or die;

/**
 * Template
 *
 * @package     Molajo
 * @subpackage  Includer
 * @since       1.0
 */
Class TemplateIncluder extends Includer
{
	/**
	 * @param string $name
	 * @param string $type
	 *
	 * @return null
	 * @since   1.0
	 */
	public function __construct($name = null, $type = null)
	{
		Services::Registry()->set('Parameters', 'extension_catalog_type_id', 0);
		parent::__construct($name, $type);
		Services::Registry()->set('Parameters', 'criteria_html_display_filter', false);

		return $this;
	}

	/**
	 * setRenderCriteria - Retrieve default values, if not provided by extension
	 *
	 * @return bool
	 * @since   1.0
	 */
	protected function setRenderCriteria()
	{
		/**  Template */
		$template_id = 0;
		$template_title = Services::Registry()->get('Parameters', 'template_view_path_node');

		if (trim($template_title) == '') {
		} else {
			$template_id = Helpers::Extension()
				->getInstanceID(CATALOG_TYPE_EXTENSION_TEMPLATE_VIEW, $template_title);
		}

		if ((int) $template_id == 0) {
			$template_id = Services::Registry()->get('Parameters', 'template_view_id');
		}

		if (trim($template_title) == '' || (int) $template_id > 0) {
		} else {
			Services::Registry()->set('Parameters', 'template_view_path_node', $template_title);
			$template_id = Helpers::Extension()
				->getInstanceID(CATALOG_TYPE_EXTENSION_TEMPLATE_VIEW, $template_title);
		}

		if ((int)$template_id == 0) {
			 $template_id = Helpers::View()->getDefault('Template');
		}

		Services::Registry()->set('Parameters', 'template_view_id', $template_id);

		return parent::setRenderCriteria();
	}

	/**
	 * Loads Media CSS and JS files for Template and Template Views
	 *
	 * @return object
	 * @since   1.0
	 */
	protected function loadViewMedia()
	{
		if ($this->type == 'asset' || $this->type == 'metadata') {
			return $this;
		}

		$priority = Services::Registry()->get('Parameters', 'criteria_media_priority_other_extension', 400);

		$file_path = Services::Registry()->get('Parameters', 'template_view_path');
		$url_path = Services::Registry()->get('Parameters', 'template_view_path_url');

		Services::Asset()->addCssFolder($file_path, $url_path, $priority);
		Services::Asset()->addJsFolder($file_path, $url_path, $priority, 0);
		Services::Asset()->addJsFolder($file_path, $url_path, $priority, 1);

		return $this;
	}
}
