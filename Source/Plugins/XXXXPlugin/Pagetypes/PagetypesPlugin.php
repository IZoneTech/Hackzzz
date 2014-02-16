<?php
/**
 * Page Types Plugin
 *
 * @package    Molajo
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 */
namespace Molajo\Plugins\Pagetypes;

use CommonApi\Event\SystemInterface;
use Molajo\Plugins\SystemEventPlugin;

/**
 * Page Types Plugin
 *
 * @package  Molajo
 * @license  http://www.opensource.org/licenses/mit-license.html MIT License
 * @since    1.0
 */
class PagetypesPlugin extends SystemEventPlugin implements SystemInterface
{
    /**
     * Generates list of Pagetypes
     *
     * @return  $this
     * @since   1.0
     */
    public function onAfterRoute()
    {
        return $this;

        $folders = glob(BASE_FOLDER . '/Source/Menuitem/*');
        if (count($folders) === 0 || $folders === false) {
            $page_type_list = array();
        } else {
            $page_type_list = $folders;
        }

        $folders = glob(BASE_FOLDER . '/Vendor' . '/Molajo' . '/Menuitem/*');
        if (count($folders) === 0 || $folders === false) {
        } else {
            $new            = array_merge($page_type_list, $folders);
            $page_type_list = $new;
        }

        $newer = array_unique($page_type_list);
        sort($newer);

        $page_types = array();
        foreach ($newer as $item) {
            $temp_row        = new \stdClass();
            $temp_row->value = $item;
            $temp_row->id    = $item;
            $page_types[]    = $temp_row;
        }

        $this->plugin_data->datalists->pagetypes = $page_types;

        return $this;
    }
}
