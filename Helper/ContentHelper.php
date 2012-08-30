<?php
/**
 * @package    Molajo
 * @copyright  2012 Individual Molajo Contributors. All rights reserved.
 * @license    GNU GPL v 2, or later and MIT, see License folder
 */
namespace Molajo\Helper;

use Molajo\Helpers;
use Molajo\Service\Services;

defined('MOLAJO') or die;

/**
 * Content Helper
 *
 * Retrieves Item, List, or TemplateView Parameter information for Route
 *
 * @package      Molajo
 * @subpackage   Helper
 * @since        1.0
 */
Class ContentHelper
{
	/**
	 * Static instance
	 *
	 * @var     object
	 * @since   1.0
	 */
	protected static $instance;

	/**
	 * getInstance
	 *
	 * @static
	 * @return bool|object
	 * @since   1.0
	 */
	public static function getInstance()
	{
		if (empty(self::$instance)) {
			self::$instance = new ContentHelper();
		}

		return self::$instance;
	}

	/**
	 * Retrieves List Route information
	 *
	 * @param   $id
	 * @param   $model_type
	 * @param   $model_name
	 * @param   $model_query_object
	 *
	 * @return  boolean
	 * @since   1.0
	 */
	public function getListRoute($id, $model_type, $model_name)
	{
		Services::Registry()->set('Query', 'Current', 'Content getListRoute');

		$item = $this->get($id, $model_type, $model_name);
		if (count($item) == 0) {
			return Services::Registry()->set('Parameters', 'status_found', false);
		}

		/** Route Registry */
		Services::Registry()->set('Parameters', 'extension_instance_id', (int)$item->id);
		Services::Registry()->set('Parameters', 'extension_title', $item->title);
		Services::Registry()->set('Parameters', 'extension_translation_of_id', (int)$item->translation_of_id);
		Services::Registry()->set('Parameters', 'extension_language', $item->language);
		Services::Registry()->set('Parameters', 'extension_catalog_type_id', (int)$item->catalog_type_id);
		Services::Registry()->set('Parameters', 'extension_catalog_type_title', $item->content_catalog_types_title);
		Services::Registry()->set('Parameters', 'extension_modified_datetime', $item->modified_datetime);

		/** Content Extension and Source */
		Services::Registry()->set('Parameters', 'catalog_type_id', $item->content_catalog_types_id);
		Services::Registry()->set('Parameters', 'content_type', (int)$item->content_catalog_types_type);
		Services::Registry()->set('Parameters', 'primary_category_id', $item->content_catalog_types_primary_category_id);
		Services::Registry()->set('Parameters', 'source_table', (int)$item->content_catalog_types_source_table);
		Services::Registry()->set('Parameters', 'source_id', 0);
		Services::Registry()->set('Parameters', 'source_slug', (int)$item->content_catalog_types_slug);
		Services::Registry()->set('Parameters', 'source_routable', (int)$item->content_catalog_types_routable);

		/** Set Parameters */
		$this->setParameters('list', $item->table_registry_name . 'Parameters');

		return true;
	}

	/**
	 * Retrieve Route information for a specific Content Item or Form
	 *
	 * @return boolean
	 * @since    1.0
	 */
	public function getRouteItem($id, $model_type, $model_name)
	{
		Services::Registry()->set('Query', 'Current', 'Content getRouteItem');

		$item = $this->get($id, $model_type, $model_name);

		if (count($item) == 0) {
			return Services::Registry()->set('Parameters', 'status_found', false);
		}

		Services::Registry()->set('Plugindata', 'PrimaryRequestQueryResults', array($item));

		Services::Registry()->set('Parameters', 'content_id', (int)$item->id);
		Services::Registry()->set('Parameters', 'content_title', $item->title);
		Services::Registry()->set('Parameters', 'content_translation_of_id', (int)$item->translation_of_id);
		Services::Registry()->set('Parameters', 'content_language', $item->language);
		Services::Registry()->set('Parameters', 'content_catalog_type_id', (int)$item->catalog_type_id);
		Services::Registry()->set('Parameters', 'content_catalog_type_title', $item->catalog_types_title);
		Services::Registry()->set('Parameters', 'content_modified_datetime', $item->modified_datetime);

		Services::Registry()->set('Parameters', 'extension_instance_id', (int)$item->extension_instance_id);
		Services::Registry()->set('Parameters', 'extension_title', $item->extension_instances_title);
		Services::Registry()->set('Parameters', 'extension_id', (int)$item->extensions_id);
		Services::Registry()->set('Parameters', 'extension_name_path_node', $item->extensions_name);
		Services::Registry()->set('Parameters', 'extension_catalog_type_id',
			(int)$item->extension_instances_catalog_type_id);

		$parameterNamespace = $item->table_registry_name . 'Parameters';

		/** Content Extension and Source */
		Services::Registry()->set('Parameters', 'extension_instance_id',
			Services::Registry()->get($parameterNamespace, 'criteria_extension_instance_id'));

		/** Theme, Page, Template and Wrap Views */
		$editCheck = Services::Registry()->get('Parameters', 'catalog_url_sef_request');
		if (substr($editCheck, strlen($editCheck) - 4, 4) == 'edit') {
			Services::Registry()->set('Parameters', 'request_action', 'edit');
		}
		if (strtolower(Services::Registry()->get('Parameters', 'request_action')) == 'display') {
			$requestTypeNamespace = 'item';
		} else {
			$requestTypeNamespace = 'form';
		}

		Services::Registry()->set('Parameters', 'extension_catalog_type_id',
			(int)$item->extension_instances_catalog_type_id);

		Services::Registry()->set('Parameters', 'parent_menu_id',
			Services::Registry()->get($parameterNamespace, 'item_parent_menu_id'));

		$this->getResourceParameters((int)$item->extension_instance_id);

		$this->setParameters(
			$requestTypeNamespace,
			$item->table_registry_name . 'Parameters',
			'ResourcesSystemParameters'
		);

		return true;
	}

	/**
	 * Retrieves the Menu Item Route information
	 *
	 * @return boolean
	 * @since   1.0
	 */
	public function getRouteTemplateView()
	{
		Services::Registry()->set('Query', 'Current', 'Content getRouteTemplateView');

		Services::Registry()->sort('Parameters');

		$item = $this->get(
			Services::Registry()->get('Parameters', 'catalog_source_id'),
			'Menuitem',
			Services::Registry()->get('Parameters', 'catalog_menuitem_type')
		);

		if (count($item) == 0) {
			return Services::Registry()->set('Parameters', 'status_found', false);
		}

		/** Route Registry */
		Services::Registry()->set('Parameters', 'menuitem_id', (int)$item->id);
		Services::Registry()->set('Parameters', 'menuitem_lvl', (int)$item->lvl);
		Services::Registry()->set('Parameters', 'menuitem_title', $item->title);
		Services::Registry()->set('Parameters', 'menuitem_parent_id', $item->parent_id);
		Services::Registry()->set('Parameters', 'menuitem_translation_of_id', (int)$item->translation_of_id);
		Services::Registry()->set('Parameters', 'menuitem_language', $item->language);
		Services::Registry()->set('Parameters', 'menuitem_catalog_type_id', (int)$item->catalog_type_id);
		Services::Registry()->set('Parameters', 'menuitem_catalog_type_title', $item->catalog_types_title);
		Services::Registry()->set('Parameters', 'menuitem_modified_datetime', $item->modified_datetime);

		/** Menu Extension */
		Services::Registry()->set('Parameters', 'menu_id', (int)$item->extension_id);
		Services::Registry()->set('Parameters', 'menu_title', $item->extensions_name);
		Services::Registry()->set('Parameters', 'menu_extension_id', (int)$item->extensions_id);
		Services::Registry()->set('Parameters', 'menu_path_node', $item->extensions_name);

		$this->setParameters('menuitem', $item->table_registry_name . 'Parameters');

		return true;
	}

	/**
	 * Get data for Menu Item or Item or List
	 *
	 * @param $id
	 * @param $model_type
	 * @param $model_name
	 * @param $model_query_object
	 *
	 * @return array An object containing an array of data
	 * @since   1.0
	 */
	public function get($id = 0, $model_type = 'Table', $model_name = 'Content')
	{
		Services::Profiler()->set('ContentHelper->get '
				. ' ID: ' . $id
				. ' Model Type: ' . $model_type
				. ' Model Name: ' . $model_name,
			LOG_OUTPUT_ROUTING, VERBOSE);

		$controllerClass = 'Molajo\\MVC\\Controller\\Controller';
		$m = new $controllerClass();

		$results = $m->connect($model_type, $model_name);
		if ($results == false) {
			return false;
		}

		$m->set('id', (int)$id);
		$m->set('process_plugins', 0);
		$m->set('get_customfields', 1);

		$item = $m->getData('item');
		if (count($item) == 0) {
			return array();
		}

		$item->table_registry_name = $m->table_registry_name;

		return $item;
	}

	/**
	 * Get Parameters for Resource
	 *
	 * @param  $id
	 *
	 * @return  array  An object containing an array of data
	 * @since   1.0
	 */
	protected function getResourceParameters($id = 0)
	{
		$controllerClass = 'Molajo\\MVC\\Controller\\Controller';
		$m = new $controllerClass();

		$m->set('id', (int)$id);
		$m->set('process_plugins', 0);
		$m->set('get_customfields', 1);

		$results = $m->connect('System', 'Resources');
		if ($results == false) {
			return false;
		}

		$item = $m->getData('item');
		if (count($item) == 0) {
			return array();
		}

		return $item;
	}

	/**
	 * Retrieves parameter set (form, item, list, or menuitem) and populates Parameters registry
	 *
	 * @param   $requestTypeNamespace
	 * @param   $parameterNamespace
	 *
	 * @return  bool
	 * @since   1.0
	 */
	public function setParameters($requestTypeNamespace, $parameterNamespace, $resourceNamespace = '')
	{
		Services::Registry()->set('Parameters', 'parameter_type', $requestTypeNamespace);

		/** 1. Parameters from Request Query */
		$newParameters = Services::Registry()->get($parameterNamespace, $requestTypeNamespace . '*');
		if (is_array($newParameters) && count($newParameters) > 0) {
			$this->processParameterSet($newParameters, $requestTypeNamespace);
		}

		/** 2. Resource defaults */
		if ($resourceNamespace == '') {
		} else {
			$resourceParameters = Services::Registry()->get($resourceNamespace, $requestTypeNamespace . '*');
			if (is_array($resourceParameters) && count($resourceParameters) > 0) {
				$this->processParameterSet($newParameters, $requestTypeNamespace);
			}
		}

		/** 3. Application defaults */
		$applicationDefaults = Services::Registry()->get('Configuration', $requestTypeNamespace . '*');
		if (count($applicationDefaults) > 0) {
			$this->processParameterSet($applicationDefaults, $requestTypeNamespace);
		}

		/** Copy remaining */
		Services::Registry()->copy($parameterNamespace, 'Parameters');

		/**  Merge in matching Configuration data  */
		Services::Registry()->merge('Configuration', 'Parameters', true);

		/** Set Theme, Page, Template nad Wrap */
		Helpers::Extension()->setThemePageView();
		Helpers::Extension()->setTemplateWrapModel();

		Services::Registry()->sort('Parameters');
		Services::Registry()->sort('Metadata');

		/** Remove standard patterns no longer needed */
		Services::Registry()->delete('Parameters', 'list*');
		Services::Registry()->delete('Parameters', 'item*');
		Services::Registry()->delete('Parameters', 'form*');
		Services::Registry()->delete('Parameters', 'menuitem*');

		return true;
	}

	/**
	 * processParameterSet iterates a new parameter set to determine whether or not it should be applied
	 *
	 * @param $parameterSet
	 * @param $requestTypeNamespace
	 */
	protected function processParameterSet($parameterSet, $requestTypeNamespace)
	{
		foreach ($parameterSet as $key => $value) {
			$existing = Services::Registry()->get('Parameters', substr($key, strlen($requestTypeNamespace) + 1, 9999));
			if ($existing === 0 || trim($existing) == '' || $existing == null) {
				if ($value === 0 || trim($value) == '' || $value == null) {
				} else {
					Services::Registry()->set('Parameters', substr($key, strlen($requestTypeNamespace) + 1, 9999), $value);
				}
			}
		}
	}
}
