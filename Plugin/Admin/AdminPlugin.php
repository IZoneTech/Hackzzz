<?php
/**
 * @package    Molajo
 * @copyright  2012 Individual Molajo Contributors. All rights reserved.
 * @license    GNU GPL v 2, or later and MIT, see License folder
 */
namespace Molajo\Plugin\Admin;

use Molajo\Application;
use Molajo\Plugin\Content\ContentPlugin;
use Molajo\Service\Services;

defined('MOLAJO') or die;

/**
 * @package     Molajo
 * @subpackage  Plugin
 * @since       1.0
 */
class AdminPlugin extends ContentPlugin
{
	/**
	 * Prepares Admin Menus
	 *
	 * Run this LAST
	 *
	 * @return boolean
	 * @since   1.0
	 */
	public function onBeforeParse()
	{
		/** Only used for the Admin */
		if (APPLICATION_ID == 2) {
		} else {
			return true;
		}

		/** Not authorised and not found */
		if ($this->get('model_type') == '' || $this->get('model_name') == '') {
			return true;
		}

		$current_menuitem_id = (int) Services::Registry()->get('Parameters', 'menuitem_id');

		if ((int) $current_menuitem_id == 0) {
			$current_menuitem_id = (int) Services::Registry()->get('Parameters', 'parent_menuid');
		}
		if ((int) $current_menuitem_id == 0) {
			return true;
		}

		$this->pageURL();

		/** Data Source Connection */
		$controllerClass = 'Molajo\\MVC\\Controller\\Controller';
		$connect = new $controllerClass();

		$results = $connect->connect($this->get('model_type'), $this->get('model_name'));
		if ($results == false) {
			return false;
		}

		$this->setBreadcrumbs($current_menuitem_id);

		$this->setMenu($current_menuitem_id);

		$this->setPageTitle();

		return true;
	}

	/**
	 * Build the page url to be used in links
	 *
	 * page_url was set in Route and it contains any non-routable parameters that
	 * were used. Non-routable parameters include such values as /edit, /new, /tag/value, etc
	 *
	 * These values are used in conjunction with the permanent URL for basic operations on that data
	 */
	protected function pageURL()
	{
		$url = Application::Request()->get('base_url_path_for_application') .
			Application::Request()->get('requested_resource_for_route');

		Services::Registry()->set('Plugindata', 'full_page_url', $url);

		return true;
	}

	/**
	 * Set breadcrumbs
	 *
	 * @return void
	 * @since  1.0
	 */
	protected function setBreadcrumbs($current_menuitem_id)
	{
		$bread_crumbs = Services::Menu()->getMenuBreadcrumbIds($current_menuitem_id);
		Services::Registry()->set('Plugindata', 'Adminbreadcrumbs', $bread_crumbs);
	}

	/**
	 * Retrieve an array of values that represent the active menuitem ids for a specific menu
	 *
	 * @return void
	 * @since  1.0
	 */
	protected function setMenu($current_menu_item = 0)
	{
		$bread_crumbs = Services::Registry()->get('Plugindata', 'Adminbreadcrumbs');

		$menuArray = array();
		$menuArray[] = 'Adminhome';
		$menuArray[] = 'Adminnavigationbar';
		$menuArray[] = 'Adminsectionmenu';
		if (count($bread_crumbs) > 2) {
			$menuArray[] = 'Adminstatusmenu';
		}

		$i = 0;
		foreach ($bread_crumbs as $level) {

			$menu_id = $level->extension_id;
			$parent_id = $level->parent_id;

			if ($i == 0) {
				$query_results = Services::Menu()->get($menu_id, $current_menu_item);
				Services::Registry()->set('Plugindata', 'Adminmenu', $query_results);
				$level = 0;
			}

			$list = array();
			foreach ($query_results as $menu_items) {
				if ((int) $parent_id == (int) $menu_items->parent_id) {
					$list[] = $menu_items;
				}
			}
			Services::Registry()->set('Plugindata', $menuArray[$i], $list);

			$i++;
			if ($i > count($menuArray) - 1) {
				break;
			}
		}

/**
		echo '<br />Adminhome <br />';
		echo '<pre>';
		var_dump(Services::Registry()->get('Plugindata','Adminhome'));
		echo '</pre>';

		echo '<br />Adminnavigationbar <br />';
		echo '<pre>';
		var_dump(Services::Registry()->get('Plugindata','Adminnavigationbar'));
		echo '</pre>';

		echo '<br />Adminsectionmenu <br />';
		echo '<pre>';
		var_dump(Services::Registry()->get('Plugindata','Adminsectionmenu'));
		echo '</pre>';

		echo '<br />Adminstatusmenu <br />';
		echo '<pre>';
		var_dump(Services::Registry()->get('Plugindata','Adminstatusmenu'));
		echo '</pre>';

		echo '<br />Adminbreadcrumbs <br />';
		echo '<pre>';
		var_dump(Services::Registry()->get('Plugindata','Adminbreadcrumbs'));
		echo '</pre>';

		echo '<br />Adminmenu <br />';
		echo '<pre>';
		var_dump(Services::Registry()->get('Plugindata','Adminmenu'));
		echo '</pre>';
*/
		return;
	}

	/**
	 * Set the Page Title, given Breadcrumb values
	 *
	 * @param int $extension_instance_id - menu
	 *
	 * @return object
	 * @since   1.0
	 */
	public function setPageTitle()
	{
		$bread_crumbs = Services::Registry()->get('Plugindata', 'Adminbreadcrumbs');

		$title = $bread_crumbs[count($bread_crumbs) - 1]->title;

		Services::Registry()->set('Plugindata', 'PageTitle', $title);

		return $this;
	}
}
