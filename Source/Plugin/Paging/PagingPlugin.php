<?php
/**
 * Paging Plugin
 *
 * @package    Molajo
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 */
namespace Molajo\Plugin\Paging;

use CommonApi\Event\DisplayInterface;
use Molajo\Plugin\DisplayEventPlugin;

/**
 * Paging Plugin
 *
 * @package     Molajo
 * @license     http://www.opensource.org/licenses/mit-license.html MIT License
 * @since       1.0
 */
class PagingPlugin extends DisplayEventPlugin implements DisplayInterface
{
    /**
     * After reading, calculate paging data
     *
     * @return  $this
     * @since   1.0
     */
    public function onBeforeRenderView()
    {
        return $this;

        if (strtolower($this->get('template_view_path_node', '', 'runtime_data')) == 'paging') {
        } else {
            return $this;
        }

        /** initialise */
        $url = $this->runtime_data->page->urls['page'];

        /** current_page */
        $current_page = ($this->get('model_offset') / $this->get('model_count', 0, 'runtime_data')) + 1;
        if ($this->get('model_offset') % $this->get('model_count', 0, 'runtime_data')) {
            $current_page ++;
        }

        /** previous page */
        if ((int)$current_page > 1) {
            $previous_page = (int)$current_page - 1;
            $prev_link     = $url . '/page/' . (int)$previous_page;
        } else {
            $previous_page = 0;
            $prev_link     = '';
        }

        /** next page */
        if ((int)$total_pages > (int)$current_page) {
            $next_page = $current_page + 1;
            $next_link = $url . '/page/' . $next_page;
        } else {
            $next_page = 0;
            $next_link = '';
        }

        /** Paging */
        $temp_row = new \stdClass();

        $temp_row->total_items          = (int)$this->get('pagination_total');
        $temp_row->total_items_per_page = (int)$this->get('model_count', 0, 'runtime_data');

        $temp_row->first_page = $first_page;
        $temp_row->first_link = $first_link;

        $temp_row->previous_page = $previous_page;
        $temp_row->prev_link     = $prev_link;

        $temp_row->next_page = $next_page;
        $temp_row->next_link = $next_link;

        $temp_row->last_page = $last_page;
        $temp_row->last_link = $last_link;

        $temp_row[] = $temp_row;

        $this->registry->set('Primary', 'Paging', $temp_row);
    }

    /**
     * Prev and Next Paging for Item Pages
     *
     * @return bool
     */
    protected function itemPaging()
    {
        $controller_class_namespace = $this->controller_namespace;
        $controller                 = new $controller_class_namespace();

        $results = $controller->getModelRegistry(
            $this->get('model_type', 'datasource'),
            $this->get('model_name', '', 'runtime_data'),
            1
        );

        $controller->setDataobject();
        $controller->connectDatabase();

        $controller->set('get_customfields', 0);
        $controller->set('use_special_joins', 0);
        $controller->set('process_events', 0);
        $controller->set('get_item_children', 0);

        $controller->model->query->select(
            $controller->model->database->qn('a')
            . '.' . $controller->model->database->qn($controller->get('primary_key', 'id'))
        );

        $controller->model->query->select(
            $controller->model->database->qn('a')
            . '.' . $controller->model->database->qn($controller->get('name_key', 'title'))
        );

        $controller->model->query->where(
            $controller->model->database->qn('a')
            . '.' . $controller->model->database->qn(
                $controller->get('primary_key', 'id')
                . ' = ' . (int)$this->runtime_data->catalog->source_id
            )
        );

//@todo ordering
        $item = $controller->getData('item');

        $this->model_registry_name = ucfirst(strtolower($this->get('model_name', '', 'runtime_data')))
            . ucfirst(strtolower($this->get('model_type', 'datasource')));

        if ($item === false || count($item) == 0) {
            return $this;
        }
    }
}
