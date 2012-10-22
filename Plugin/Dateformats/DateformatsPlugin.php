<?php
/**
 * @package    Molajo
 * @copyright  2012 Individual Molajo Contributors. All rights reserved.
 * @license    GNU GPL v 2, or later and MIT, see License folder
 */
namespace Molajo\Plugin\Dateformats;

use Molajo\Plugin\Plugin\Plugin;
use Molajo\Service\Services;

defined('MOLAJO') or die;

/**
 * Date Formats
 *
 * @package     Molajo
 * @subpackage  Plugin
 * @since       1.0
 */
class DateformatsPlugin extends Plugin
{
    /**
     * Pre-create processing
     *
     * @return boolean
     * @since   1.0
     */
    public function onBeforeCreate()
    {
        $fields = $this->retrieveFieldsByType('datetime');

        if (is_array($fields) && count($fields) > 0) {

            foreach ($fields as $field) {

                $name = $field->name;
                $fieldValue = $this->getFieldValue($field);

                if ($name == 'modified_datetime') {

                    $this->saveField($field, $name, $this->now);

                    $modifiedByField = $this->getField('modified_by');
                    $modifiedByValue = $this->getFieldValue($modifiedByField);
                    if ($modifiedByValue === false) {
                        $this->saveField($modifiedByField, 'modified_by', Services::Registry()->get('User', 'id'));
                    }

                } elseif ($fieldValue === false
                    || $fieldValue == '0000-00-00 00:00:00'
) {

                    $this->saveField($field, $name, $this->now);

                    if ($name == 'created_datetime') {
                        $createdByField = $this->getField('created_by');
                        $createdByValue = $this->getFieldValue($createdByField);
                        if ($createdByValue === false) {
                            $this->saveField($createdByField, 'created_by', Services::Registry()->get('User', 'id'));
                        }

                    } elseif ($name == 'activity_datetime') {
                        $createdByField = $this->getField('user_id');
                        $createdByValue = $this->getFieldValue($createdByField);
                        if ($createdByValue === false) {
                            $this->saveField($createdByField, 'user_id', Services::Registry()->get('User', 'id'));
                        }

                    }
                }
            }
        }

        return true;
    }

    /**
     * After-read processing
     *
     * Adds formatted dates to 'normal' or special fields recordset
     *
     * @return boolean
     * @since   1.0
     */
    public function onAfterRead()
    {
        $fields = $this->retrieveFieldsByType('datetime');

        try {
            Services::Date()->convertCCYYMMDD('2011-11-11');
            /** Date Service is not available (likely startup) */
        } catch (\Exception $e) {
            return true;
        }

        if (is_array($fields) && count($fields) > 0) {

            foreach ($fields as $field) {

                $name = $field->name;

                /** Retrieves the actual field value from the 'normal' or special field */
                $fieldValue = $this->getFieldValue($field);

                if ($fieldValue === false
                    || $fieldValue == '0000-00-00 00:00:00'
                    || $fieldValue == ''
                    || $fieldValue == null
                    || substr($name, 0, 12) == 'list_select_'

                ) {

                } else {

                    /** formats the date for CCYYMMDD */
                    $newFieldValue = Services::Date()->convertCCYYMMDD($fieldValue);

                    if ($newFieldValue === false) {
                        $ccyymmdd = false;
                    } else {

                        /** Creates the new 'normal' or special field and populates the value */
                        $ccyymmdd = $newFieldValue;
                        $new_name = $name . '_ccyymmdd';
                        $this->saveField(null, $new_name, $newFieldValue);
                        $fieldValue = $newFieldValue;
                    }

                    /** Using newly formatted date, calculate NN days ago */
                    $newFieldValue = Services::Date()->getNumberofDaysAgo($fieldValue);

                    if ($newFieldValue === false) {
                        $this->saveField(null, $name . '_n_days_ago', null);
                        $this->saveField(null, $name . '_ccyy', null);
                        $this->saveField(null, $name . '_mm', null);
                        $this->saveField(null, $name . '_dd', null);
                        $this->saveField(null, $name . '_month_name_abbr', null);
                        $this->saveField(null, $name . '_month_name', null);
                        $this->saveField(null, $name . '_time', null);
                        $this->saveField(null, $name . '_day_number', null);
                        $this->saveField(null, $name . '_day_name_abbr', null);
                        $this->saveField(null, $name . '_day_name', null);

                    } else {

                        /** Creates the new 'normal' or special field and populates the value */
                        $new_name = $name . '_n_days_ago';
                        $this->saveField(null, $new_name, $newFieldValue);
                    }

                    /** Pretty Date */
                    $newFieldValue = Services::Date()->getPrettyDate($fieldValue);

                    if ($newFieldValue === false) {
                    } else {

                        /** Creates the new 'normal' or special field and populates the value */
                        $new_name = $name . '_pretty_date';
                        $this->saveField(null, $new_name, $newFieldValue);
                    }

                    /** Date Parts */
                    if ($ccyymmdd === false) {
                    } else {
                        $this->saveField(null, $name . '_ccyy', substr($ccyymmdd, 0, 4));
                        $this->saveField(null, $name . '_mm', (int) substr($ccyymmdd, 5, 2));
                        $this->saveField(null, $name . '_dd', (int) substr($ccyymmdd, 8, 2));

                        $newFieldValue = Services::Date()->getMonthName((int) substr($ccyymmdd, 5, 2), true);
                        $this->saveField(null, $name . '_month_name_abbr', $newFieldValue);

                        $newFieldValue = Services::Date()->getMonthName((int) substr($ccyymmdd, 5, 2), false);
                        $this->saveField(null, $name . '_month_name', $newFieldValue);

                        $dateObject = Services::Date()->getDate($fieldValue);
                        $this->saveField(null, $name . '_time', date_format($dateObject, 'G:ia'));

                        $newFieldValue = (int) date_format($dateObject, 'N');
                        $this->saveField(null, $name . '_day_number', $newFieldValue);

                        $newFieldValue = Services::Date()->getDayName((int) date_format($dateObject, 'N'), true);
                        $this->saveField(null, $name . '_day_name_abbr', $newFieldValue);

                        $newFieldValue = Services::Date()->getDayName((int) date_format($dateObject, 'N'), false);
                        $this->saveField(null, $name . '_day_name', $newFieldValue);
                    }
                }
            }
        }

        return true;
    }
}
