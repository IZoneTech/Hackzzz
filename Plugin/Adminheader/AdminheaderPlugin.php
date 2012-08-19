<?php
/**
 * @package    Molajo
 * @copyright  2012 Individual Molajo Contributors. All rights reserved.
 * @license    GNU GPL v 2, or later and MIT, see License folder
 */
namespace Molajo\Plugin\Adminheader;

use Molajo\Plugin\Content\ContentPlugin;
use Molajo\Service\Services;

defined('MOLAJO') or die;

/**
 * @package     Molajo
 * @subpackage  Plugin
 * @since       1.0
 */
class AdminheaderPlugin extends ContentPlugin
{
	/**
	 * Prepares data for the Administrator Header
	 *
	 * @return boolean
	 * @since   1.0
	 */
	public function onAfterRead()
	{
		if (strtolower($this->get('template_view_path_node')) == 'adminheader') {
		} else {
			return true;
		}

		$title = Services::Registry()->get('Plugindata', 'PageTitle');

		$parameter_title = $this->parameters['criteria_title'];

		if ($parameter_title == '') {
			$title = $parameter_title;
		} else {
			$title .= '-' . $parameter_title;
		}
		if (trim($title) == '-') {
			$title = '<strong>Molajo</strong> '. Services::Language()->translate('Administrator');
		}
		$this->saveField(null, 'header_title', $title);

		$homeURL = Services::Registry()->get('Configuration', 'application_base_url');
		$this->saveField(null, 'home_url', $homeURL);

		return true;
	}
}
