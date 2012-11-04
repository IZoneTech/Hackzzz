<?php
/**
 * @package    Molajo
 * @copyright  2012 Individual Molajo Contributors. All rights reserved.
 * @license    GNU GPL v 2, or later and MIT, see License folder
 */
namespace Molajo\Plugin\Pagetypeapplication;

use Molajo\Plugin\Plugin\Plugin;
use Molajo\Service\Services;

defined('MOLAJO') or die;

/**
 * @package     Molajo
 * @subpackage  Plugin
 * @since       1.0
 */
class PagetypeapplicationPlugin extends Plugin
{
    /**
     * Prepares Configuration Tabs and Tab Content
     *
     * @return boolean
     * @since   1.0
     */
    public function onBeforeParse()
    {

        if (strtolower($this->get('page_type')) == 'application') {
        } else {
            return true;
        }

        /** Create Tabs */
        $namespace = $this->parameters['application_namespace'];
        $namespace = ucfirst(strtolower($namespace));

        $page_array = $this->parameters['application_array'];

        $tabs = Services::Form()->setPageArray(
            'Table',
            'Application',
            $namespace,
            $page_array,
            'application_',
            'Pagetypeapplication',
            'Admineapplicationtab',
            null,
            array()
        );

        Services::Registry()->set('Plugindata', 'Pagetypeapplication', $tabs);

        return true;
    }
}
