<?php
/**
 * Menuitems Plugin
 *
 * @package    Molajo
 * @copyright  2013 Amy Stephen. All rights reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 */
namespace Molajo\Plugin\Menuitems;

use stdClass;
use CommonApi\Event\SystemInterface;
use Molajo\Plugin\SystemEventPlugin;
use CommonApi\Exception\RuntimeException;

/**
 * Menuitems Plugin
 *
 * @package  Molajo
 * @license  http://www.opensource.org/licenses/mit-license.html MIT License
 * @since    1.0
 */
class MenuitemsPlugin extends SystemEventPlugin implements SystemInterface
{
    /**
     * Generates list of Menus and Menuitems for use in Datalists
     *
     * @return  $this
     * @since   1.0
     * @throws  \CommonApi\Exception\RuntimeException
     */
    public function onAfterRoute()
    {
        if ($this->runtime_data->application->id == 2) {
        } else {
            return $this;
        }

        return $this;

        $menuitem = $this->resources->get(
            'query:///Molajo/Menuitem/Applications/Configuration.xml',
            array('Parameters', $this->runtime_data)
        );

        $menuitem->setModelRegistry('check_view_level_access', 1);
        $menuitem->setModelRegistry('process_events', 1);
        $menuitem->setModelRegistry('query_object', 'list');
        $menuitem->setModelRegistry('get_customfields', 1);
        $menuitem->setModelRegistry('use_special_joins', 1);

        try {
            echo '<br/><br/><br/><br/><br/><br/><br/>';
            echo 'BBEFORE';
            $this->runtime_data->menuitems = $menuitem->getData();
            echo 'AFTER';
            echo '<pre>';
            var_dump($this->runtime_data->menuitems);
            die;
        } catch (Exception $e) {
            echo 'didied';
            die;
            throw new RuntimeException ($e->getMessage());
        }

        $menuitem->model->query->select(
            $menuitem->model->database->qn(
                $menuitem->getModelRegistry('primary_prefix', 'a')
                . '.' . $menuitem->model->database->qn('title')
            )
        );
        $menuitem->model->query->select(
            $menuitem->model->database->qn(
                $menuitem->getModelRegistry('primary_prefix', 'a')
                . '.' . $menuitem->model->database->qn('id')
            )
        );
        $menuitem->model->query->select(
            $menuitem->model->database->qn(
                $menuitem->getModelRegistry('primary_prefix', 'a')
                . '.' . $menuitem->model->database->qn('lvl')
            )
        );

        $menuitem->model->query->where(
            $menuitem->model->database->qn(
                $menuitem->getModelRegistry('primary_prefix', 'a')
                . '.' . $menuitem->model->database->qn('status')
                . ' IN (0,1,2)'
            )
        );
        $menuitem->model->query->where(
            $menuitem->model->database->qn(
                $menuitem->getModelRegistry('primary_prefix', 'a')
                . '.' . $menuitem->model->database->qn('catalog_type_id')
                . ' = ' . $this->runtime_data->reference_data->catalog_type_menuitem_id
            )
        );

        $menuitem->model->query->order(
            $menuitem->model->database->qn(
                $menuitem->getModelRegistry('primary_prefix', 'a')
                . '.' . $menuitem->model->database->qn('root') . ', '
                . $menuitem->model->database->qn($menuitem->getModelRegistry('primary_prefix', 'a'))
                . '.' . $menuitem->model->database->qn('lft')
            )
        );

        $menuitem->setModelRegistry('model_offset', 0);
        $menuitem->setModelRegistry('model_count', 99999);

        $temp_query_results = $menuitem->getData('list');

        $menuitem = array();
        foreach ($temp_query_results as $item) {
            $temp_row = new \stdClass();

            $name = $item->title;
            $lvl  = (int)$item->lvl - 1;

            if ($lvl > 0) {
                for ($i = 0; $i < $lvl; $i ++) {
                    $name = ' ..' . $name;
                }
            }

            $temp_row->id    = $item->id;
            $temp_row->value = trim($name);

            $menuitem[] = $temp_row;
        }

        $this->runtime_data->plugin_data->datalists->menuitems = $menuitem;

        return $this;
    }
}
