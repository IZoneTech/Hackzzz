<?php
/**
 * Application Breadcrumbs
 *
 * @package    Molajo
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 */
namespace Molajo\Plugins\Application;

use Molajo\Plugins\SystemEventPlugin;

/**
 * Application Breadcrumbs
 *
 * @package  Molajo
 * @license  http://www.opensource.org/licenses/mit-license.html MIT License
 * @since    1.0
 */
abstract class ApplicationBreadcrumbs extends SystemEventPlugin
{
    /**
     * Retrieves an array of active menuitems, including the current menuitem and its parents
     *
     * @return  array
     * @since   1.0.0
     */
    public function getMenuBreadcrumbIds()
    {
        if ($this->plugin_data->page->current_menuitem_id === 0) {
            return array();
        }

        $controller = $this->setMenuBreadcrumbsQuery();
        $rows       = $controller->getData();
        $select     = $this->setMenuBreadcrumbsParentId($rows);

        return $this->setBreadcrumbs($select, $rows);
    }

    /**
     * Set Query for Menu Breadcrumbs
     *
     * @return  object
     * @since   1.0.0
     */
    protected function setMenuBreadcrumbsQuery()
    {
        $controller = $this->resource->get('query:///Molajo//Model//Datasource//Menuitem.xml');

        $controller->setModelRegistry('query_object', 'list');
        $controller->setModelRegistry('use_pagination', 0);

        $prefix = $controller->getModelRegistry('primary_prefix', 'a');

        $controller->where('column', $prefix . '.status', '>', 'integer', '0');

        $controller->setDistinct(true);
        $controller->from('#__extension_instances', 'current_menuitem');
        $controller->where('column', $prefix . '.catalog_type_id', '=', 'integer', 11000);
        $controller->where('column', $prefix . '.catalog_type_id', '=', 'column', 'current_menuitem.catalog_type_id');
        $controller->where('column', $prefix . '.status', '=', 'column', 'current_menuitem.status');
        $controller->where('column', $prefix . '.lft', '>=', 'column', 'current_menuitem.lft');
        $controller->where('column', $prefix . '.rgt', '<=', 'column', 'current_menuitem.rgt');
        $controller->where('column', $prefix . '.root', '=', 'column', 'current_menuitem.root');
        $controller->where(
            'column',
            $prefix . '.id',
            '=',
            'integer',
            (int)$this->plugin_data->page->current_menuitem_id
        );

        return $controller;
    }

    /**
     * Set Query for Menu Breadcrumbs
     *
     * @return  array  $rows
     *
     * @return  array
     * @since   1.0.0
     */
    protected function setMenuBreadcrumbsParentId($rows)
    {
        $look_for_parent = 0;

        $select = array();
        $i      = 0;
        foreach ($rows as $item) {

            list($select, $look_for_parent)
                = $this->setMenuBreadcrumbParentIdItem($item, $look_for_parent, $i, $select);

            $i++;
        }

        rsort($select);

        return $select;
    }

    /**
     * Set Query for Menu Breadcrumbs
     *
     * @param   object  $item
     * @param   integer $look_for_parent
     * @param   integer $i
     * @param   array   $select
     *
     * @return  array
     * @since   1.0.0
     */
    protected function setMenuBreadcrumbParentIdItem($item, $look_for_parent, $i, $select)
    {
        $this->plugin_data->page->extension_id = $item->extension_id;

        if ($look_for_parent === 0) {
            $select[]        = $i;
            $look_for_parent = $item->parent_id;

        } else {
            if ($look_for_parent === $item->id) {
                $select[]        = $i;
                $look_for_parent = $item->parent_id;
            }
        }

        return array($select, $look_for_parent);
    }

    /**
     * Set Breadcrumbs
     *
     * @return  array  $select
     * @return  array  $rows
     *
     * @return  array
     * @since   1.0.0
     */
    protected function setBreadcrumbs($select, $rows)
    {
        $breadcrumbs = array();

        foreach ($select as $index) {

            if ($this->plugin_data->page->current_menuitem_id === 0) {
                return array();
            }

            $breadcrumbs[] = $rows[$index];
        }

        return $breadcrumbs;
    }
}
