<?php
/**
 * @package    Molajo
 * @copyright  2012 Individual Molajo Contributors. All rights reserved.
 * @license    GNU GPL v 2, or later and MIT, see License folder
 */
namespace Molajo\Plugin\Fields;

use Molajo\Plugin\Plugin\Plugin;
use Molajo\Service\Services;
use Molajo\Helpers;

defined('MOLAJO') or die;

/**
 * @package     Molajo
 * @subpackage  Plugin
 * @since       1.0
 */
class FieldsPlugin extends Plugin
{
    /**
     * Generates list of Datalists for use in defining Custom Fields of Type Selectlist
     *
     * This can be moved to onBeforeParse when Plugin ordering is in place
     *
     * @return boolean
     * @since   1.0
     */
    public function onAfterRoute()
    {
        if (APPLICATION_ID == 2) {
        } else {
            return true;
        }

        $extended_literal = ' (' . Services::Language()->translate('extended') . ')';
        $parameter_literal = ' (' . Services::Language()->translate('parameter') . ')';
        $customfield_literal = ' (' . Services::Language()->translate('customfield') . ')';
        $metadata_literal = ' (' . Services::Language()->translate('metadata') . ')';

        $model_name = Services::Registry()->get('Parameters', 'model_name');
        $model_type = Services::Registry()->get('Parameters', 'model_type');

        $table_registry_name = ucfirst(strtolower($model_name)) . ucfirst(strtolower($model_type));

        if (Services::Registry()->exists($table_registry_name) === true) {
        } else {
            Helpers::Content()->getResourceContentParameters($model_type, $model_name);
        }

        $primary_prefix = Services::Registry()->get($table_registry_name, 'primary_prefix');

        $fieldArray = array();
		$standardFieldArray = array();

        $normalFields = Services::Registry()->get($table_registry_name, 'fields');

        $status = 0;

        if (count($normalFields) > 0) {

            foreach ($normalFields as $field) {

                $row = new \stdClass();
				$row->id = $field['name'];
                $row->value = $field['name'];

                $standardFieldArray[] = $row;
				$fieldArray[] = $row;

                if ($field['name'] == 'status') {
                    $status = 1;
                }

                if ($field['type'] == 'datetime') {
					$row = new \stdClass();
					$row->id = $field['name'] . '_n_days_ago';
					$row->value = $field['name'] . '_n_days_ago ' . $extended_literal;
					$fieldArray[] = $row;

					$row = new \stdClass();
					$row->id = $field['name'] . '_ccyy';
					$row->value = $field['name'] . '_ccyy' . $extended_literal;
					$fieldArray[] = $row;

					$row = new \stdClass();
					$row->id = $field['name'] . '_mm';
					$row->value = $field['name'] . '_mm' . $extended_literal;
					$fieldArray[] = $row;

					$row = new \stdClass();
					$row->id = $field['name'] . '_dd';
					$row->value = $field['name'] . '_dd' . $extended_literal;
					$fieldArray[] = $row;

                    $row = new \stdClass();
                    $row->id = $field['name'] . '_ccyy_mm_dd';
                    $row->value = $field['name'] . '_ccyy_mm_dd' . $extended_literal;
                    $fieldArray[] = $row;

					$row = new \stdClass();
					$row->id = $field['name'] . '_month_name_abbr';
					$row->value = $field['name'] . '_month_name_abbr' . $extended_literal;
					$fieldArray[] = $row;

                    $row = new \stdClass();
					$row->id = $field['name'] . '_month_name';
                    $row->value = $field['name'] . '_month_name' . $extended_literal;
                    $fieldArray[] = $row;

                    $row = new \stdClass();
					$row->id = $field['name'] . '_time';
                    $row->value = $field['name'] . '_time' . $extended_literal;
                    $fieldArray[] = $row;

                    $row = new \stdClass();
					$row->id = $field['name'] . '_day_number';
                    $row->value = $field['name'] . '_day_number' . $extended_literal;
                    $fieldArray[] = $row;

					$row = new \stdClass();
					$row->id = $field['name'] . '_day_name_abbr';
					$row->value = $field['name'] . '_day_name_abbr' . $extended_literal;
					$fieldArray[] = $row;

					$row = new \stdClass();
					$row->id = $field['name'] . '_day_name';
					$row->value = $field['name'] . '_day_name' . $extended_literal;
					$fieldArray[] = $row;
                }

                if ($field['type'] == 'text') {
                    $row = new \stdClass();
					$row->id = $field['name'] . '_introductory';
                    $row->value = $field['name'] . '_introductory' . $extended_literal;
                    $fieldArray[] = $row;

                    $row = new \stdClass();
					$row->id = $field['name'] . '_fulltext';
                    $row->value = $field['name'] . '_fulltext' . $extended_literal;
                    $fieldArray[] = $row;

                    $row = new \stdClass();
					$row->id = $field['name'] . '_snippet';
                    $row->value = $field['name'] . '_snippet' . $extended_literal;
                    $fieldArray[] = $row;
                }

            }
        }

        if ($status == 0) {
        } else {
            $row = new \stdClass();
			$row->id = 'status_name';
            $row->value = 'status_name' . $extended_literal;

            $fieldArray[] = $row;
        }

        $joins = Services::Registry()->get($table_registry_name, 'joins');

        if (count($joins) > 0) {
            foreach ($joins as $field) {
                $temp = explode(',', $field['select']);
                if (count($temp) > 0) {
                    foreach ($temp as $f) {
                        if (trim($f) == '') {
                        } else {
                            $row = new \stdClass();
							$row->id = $field['alias'] . '_' . $f;
                            $row->value =  $field['alias'] . '_' . $f . $extended_literal;

                            $fieldArray[] = $row;
                        }
                    }
                }
            }
        }

        $customfields = Services::Registry()->get($table_registry_name, 'Customfields');
        $customFieldArray = array();
        if (count($customfields) > 0) {
            foreach ($customfields as $field) {
                $row = new \stdClass();
				$row->id = $field['name'];
                $row->value = $field['name'] . $customfield_literal;

                $fieldArray[] = $row;
                $standardFieldArray[] = $row;
                $customFieldArray[] = $row;
            }
        }

        $parameters = Services::Registry()->get($table_registry_name, 'Parameters');
        $parametersFieldArray = array();
        if (count($parameters) > 0) {
            foreach ($parameters as $field) {
                $row = new \stdClass();
				$row->id = 'parameter' . '_' . $field['name'];
                $row->value = $field['name'] . $parameter_literal;

                $fieldArray[] = $row;
                $parametersFieldArray[] = $row;
            }
        }

        $metadata = Services::Registry()->get($table_registry_name, 'Metadata');
        $metadataFieldArray = array();
        if (count($metadata) > 0) {
            foreach ($metadata as $field) {
                $row = new \stdClass();
                $row->id = 'metadata' . '_' . $field['name'];
                $row->value = 'metadata' . '_' . $field['name'] . $metadata_literal;

                $fieldArray[] = $row;
                $standardFieldArray[] = $row;
                $metadataFieldArray[] = $row;
            }
        }

        asort($fieldArray);
		asort($standardFieldArray);
        asort($metadataFieldArray);
        asort($parametersFieldArray);
        asort($customFieldArray);

        Services::Registry()->set('Datalist', $table_registry_name . 'Fields', $fieldArray);
		Services::Registry()->set('Datalist', $table_registry_name . 'Fieldsstandard', $standardFieldArray);
        Services::Registry()->set('Datalist', $table_registry_name . 'Fieldsmetadata', $metadataFieldArray);
        Services::Registry()->set('Datalist', $table_registry_name . 'Fieldsparameters', $parametersFieldArray);
        Services::Registry()->set('Datalist', $table_registry_name . 'Fieldscustom', $customFieldArray);

        return true;
    }
}
