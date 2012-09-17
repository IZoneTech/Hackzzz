<?php
/**
 * @package    Molajo
 * @copyright  2012 Individual Molajo Contributors. All rights reserved.
 * @license    GNU GPL v 2, or later and MIT, see License folder
 */
namespace Molajo\Service\Services\Form;

use Molajo\Helpers;
use Molajo\Service\Services;

defined('MOLAJO') or die;

/**
 * Form
 *
 * @package     Molajo
 * @subpackage  Service
 * @since       1.0
 */
Class FormService
{
	/**
	 * @static
	 * @var    object
	 * @since  1.0
	 */
	protected static $instance;

	/**
	 * @static
	 * @return bool|object
	 * @since   1.0
	 */
	public static function getInstance()
	{
		if (empty(self::$instance)) {
			self::$instance = new FormService();
		}

		return self::$instance;
	}

	/**
	 * Build two sets of data:
	 *
	 *  1. Fieldsets: collection of the names of fields to be created (as include statements) in the fieldset
	 *  2. Fields: Field-specific registries which define attributes input to the form field creation view
	 *
	 * @return array
	 */
	public function buildFieldset($namespace, $tabLink, $input_fields)
	{
		$fieldset = array();

		foreach ($input_fields as $field) {

			$view = 'input';
			$datalist = '';

			if (isset($field['locked']) && $field['locked'] == 1) {
			} else {

				switch ($field['type']) {
					case 'audio':
					case 'boolean':
					case 'image':
					case 'color':
					case 'date':
					case 'datetime':
					case 'email':
					case 'file':
					case 'month':
					case 'password':
					case 'range':
					case 'search':
					case 'tel':
					case 'time':
					case 'url':
					case 'integer':
						$view = 'input';
						break;

					case 'text':
						$view = 'textarea';
						break;

					default:
						$view = 'input';
						break;
				}

				if (isset($field['hidden']) && $field['hidden'] == 1) {
					$view = 'input';
				}

				if (isset($field['datalist'])) {
					$view = 'select';
					$datalist = $field['datalist'];
				}

				switch ($view) {
					case 'select':
						$registryName = $this->buildSelectField($namespace, $tabLink, $field);
						break;

					case 'textarea':
						$registryName = $this->buildTextareaField($namespace, $tabLink, $field);
						break;

					default:
						$registryName = $this->buildInputField($namespace, $tabLink, $field);
						break;
				}

				$row = new \stdClass();

				$row->name = $registryName;
				$row->view = $view;
				$row->datalist = $datalist;

				$fieldset[] = $row;
			}
		}

		return $fieldset;
	}

	/**
	 * buildSelectField field
	 *
	 * @return array
	 */
	protected function buildInputField($namespace, $tabLink, $field)
	{
		$fieldRecordset = array();

		$name = $field['name'];

		$label = Services::Language()->translate(strtoupper($field['name'] . '_LABEL'));
		$tooltip = Services::Language()->translate(strtoupper($field['name'] . '_TOOLTIP'));
		$placeholder = Services::Language()->translate(strtoupper($field['name'] . '_PLACEHOLDER'));

		if (isset($field['application_default'])) {
		} else {
			$field['application_default'] = NULL;
		}

		if (($field['application_default'] === NULL || $field['application_default'] == ' ')
			&& ($field['default'] === NULL || $field['default'] == ' ')
		) {

			$default_message = Services::Language()->translate('No default value defined.');

		} elseif ($field['application_default'] === NULL || $field['application_default'] == ' ') {

			$default_message = Services::Language()->translate('Field-level default: ')
				. $field['default'];

		} else {
			$default_message = Services::Language()->translate('Application configured default setting: ')
				. $field['application_default'];
		}

		$iterate = array();
		$iterate['id'] = $field['name'];
		$iterate['name'] = $field['name'];

		if (isset($field['null']) && $field['null'] == 1) {
			$iterate['required'] = 'required';
		}

		if ($field['type'] == 'boolean') {
			$iterate['type'] = 'radio';
		} else {
			$iterate['type'] = $field['type'];
		}

		if (isset($field['hidden']) && $field['hidden'] == 1) {
			$iterate['type'] = 'hidden';
		}

		if (isset($field['value'])) {
		} else {
			$field['value'] = NULL;
		}

		$iterate['value'] = $field['value'];

		foreach ($iterate as $key => $value) {
			$row = new \stdClass();

			$row->view = 'forminput';
			$row->name = $name;
			$row->label = $label;
			$row->placeholder = $placeholder;
			$row->tooltip = $tooltip;
			$row->default_message = $default_message;

			$row->key = $key;
			$row->value = $value;

			$fieldRecordset[] = $row;
		}

		/** Field Dataset */
		$registryName = $namespace . strtolower($tabLink) . $name;
		$registryName = str_replace('_', '', $registryName);

		Services::Registry()->set('Plugindata', $registryName, $fieldRecordset);

		return $registryName;
	}

	/**
	 * buildSelectField field
	 *
	 * @return array
	 */
	protected function buildSelectField($namespace, $tabLink, $field)
	{
		$fieldRecordset = array();

		$name = $field['name'];

		$label = Services::Language()->translate(strtoupper($field['name'] . '_LABEL'));
		$tooltip = Services::Language()->translate(strtoupper($field['name'] . '_TOOLTIP'));
		$placeholder = Services::Language()->translate(strtoupper($field['name'] . '_PLACEHOLDER'));

		if (isset($field['application_default'])) {
		} else {
			$field['application_default'] = NULL;
		}

		if (($field['application_default'] === NULL || $field['application_default'] == ' ')
			&& ($field['default'] === NULL || $field['default'] == ' ')
		) {

			$default_message = Services::Language()->translate('No default value defined.');

		} elseif ($field['application_default'] === NULL || $field['application_default'] == ' ') {

			$default_message = Services::Language()->translate('Field-level default: ')
				. $field['default'];

		} else {
			$default_message = Services::Language()->translate('Application configured default setting: ')
				. $field['application_default'];
		}

		$iterate = array();
		$iterate['id'] = $field['name'];
		$iterate['name'] = $field['name'];

		if (isset($field['null']) && $field['null'] == 1) {
			$iterate['required'] = 'required';
		}

		if ($field['type'] == 'boolean') {
			$iterate['type'] = 'radio';
		} else {
			$iterate['type'] = $field['type'];
		}

		if (isset($field['hidden']) && $field['hidden'] == 1) {
			$iterate['type'] = 'hidden';
		}

		if (isset($field['value'])) {
		} else {
			$field['value'] = NULL;
		}

		$selected = $field['value'];

		$iterate['datalist'] = $field['datalist'];
		$datalist = $field['datalist'];

		if (isset($field['multiple'])) {
		} else {
			$field['multiple'] = 'multiple';
		}

		if (isset($field['size'])) {
		} else {
			$field['size'] = 'size';
		}

		foreach ($iterate as $key => $value) {
			$row = new \stdClass();

			$row->view = 'formselect';
			$row->name = $name;
			$row->label = $label;
			$row->placeholder = $placeholder;
			$row->tooltip = $tooltip;
			$row->default_message = $default_message;
			$row->selected = $selected;
			$row->datalist = $datalist;

			$row->key = $key;
			$row->value = $value;

			$fieldRecordset[] = $row;
		}

		/** Field Dataset */
		$registryName = $namespace . strtolower($tabLink) . $name;
		$registryName = str_replace('_', '', $registryName);

		Services::Registry()->set('Plugindata', $registryName, $fieldRecordset);

		return $registryName;
	}

	/**
	 * buildTextareaField field
	 *
	 * @return array
	 */
	public function buildTextareaField($namespace, $tabLink, $field)
	{
		$fieldRecordset = array();

		$name = $field['name'];

		$label = Services::Language()->translate(strtoupper($field['name'] . '_LABEL'));
		$tooltip = Services::Language()->translate(strtoupper($field['name'] . '_TOOLTIP'));
		$placeholder = Services::Language()->translate(strtoupper($field['name'] . '_PLACEHOLDER'));

		if (isset($field['application_default'])) {
		} else {
			$field['application_default'] = NULL;
		}

		if (($field['application_default'] === NULL || $field['application_default'] == ' ')
			&& ($field['default'] === NULL || $field['default'] == ' ')
		) {

			$default_message = Services::Language()->translate('No default value defined.');

		} elseif ($field['application_default'] === NULL || $field['application_default'] == ' ') {

			$default_message = Services::Language()->translate('Field-level default: ')
				. $field['default'];

		} else {
			$default_message = Services::Language()->translate('Application configured default setting: ')
				. $field['application_default'];
		}

		$iterate = array();
		$iterate['id'] = $field['name'];
		$iterate['name'] = $field['name'];

		if (isset($field['null']) && $field['null'] == 1) {
			$iterate['required'] = 'required';
		}

		if (isset($field['hidden']) && $field['hidden'] == 1) {
			$iterate['type'] = 'hidden';
		}

		if (isset($field['value'])) {
		} else {
			$field['value'] = NULL;
		}

		$selected = $field['value'];


		foreach ($iterate as $key => $value) {
			$row = new \stdClass();

			$row->view = 'formtextarea';
			$row->name = $name;
			$row->label = $label;
			$row->placeholder = $placeholder;
			$row->tooltip = $tooltip;
			$row->default_message = $default_message;
			$row->selected = $selected;
			$row->key = $key;
			$row->value = $value;

			$fieldRecordset[] = $row;
		}

		/** Field Dataset */
		$registryName = $namespace . strtolower($tabLink) . $name;
		$registryName = str_replace('_', '', $registryName);

		Services::Registry()->set('Plugindata', $registryName, $fieldRecordset);

		return $registryName;
	}
}
