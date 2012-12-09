<?php
/**
 * @package    Molajo
 * @copyright  2012 Amy Stephen. All rights reserved.
 * @license    GNU GPL v 2, or later and MIT, see License folder
 */
namespace Molajo\Plugin\Events;

use Molajo\Plugin\Plugin\Plugin;
use Molajo\Service\Services;

defined('MOLAJO') or die;

/**
 * @package     Molajo
 * @subpackage  Plugin
 * @since       1.0
 */
class EventsPlugin extends Plugin
{
    /**
     * Generates list of Events for use in Datalists
     *
     * @return  boolean
     * @since   1.0
     */
    public function onAfterRoute()
    {
        if (APPLICATION_ID == 2) {
        } else {
            return true;
        }

        $events = array(
            'onConnectDatabase',
            'onAfterInitialise',
            'onAfterRoute',
            'onAfterAuthorise',
            'onBeforeParse',
            'onBeforeParseHead',
            'onBeforeInclude',
            'onBeforeRead',
            'onAfterRead',
            'onAfterReadall',
            'onBeforeRenderview',
            'onAfterRenderview',
            'onAfterInclude',
            'onAfterParse',
            'onAfterExecute',
            'onAfterResponse',
            'onBeforecreate',
            'onAftercreate',
            'onBeforeupdate',
            'onAfterupdate',
            'onBeforedelete',
            'onAfterdelete',
            'onAftersignin',
            'onBeforesignin',
            'onAftersignout',
            'onBeforesignout'
        );

        foreach (Services::Registry()->get(EVENTS_LITERAL, 'Events') as $e) {
            if (in_array(strtolower($e), array_map('strtolower', $events))) {
            } else {
                $events[] = $e;
            }
        }

        $eventArray = array();
        foreach ($events as $key) {

            $temp_row = new \stdClass();

            $temp_row->id = $key;
            $temp_row->value = trim($key);

            $eventArray[] = $temp_row;
        }

        Services::Registry()->set(DATALIST_LITERAL, 'EventsList', $eventArray);

        return true;
    }
}
