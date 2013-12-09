<?php
/**
 * Catalog Plugin
 *
 * @package    Molajo
 * @copyright  2013 Amy Stephen. All rights reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 */
namespace Molajo\Plugin\Catalog;

use CommonApi\Event\DisplayInterface;

use Molajo\Plugin\DisplayEventPlugin;
use Molajo\Controller\CreateController;
use CommonApi\Exception\RuntimeException;

/**
 * Catalog Plugin
 *
 * @package  Molajo
 * @license  http://www.opensource.org/licenses/mit-license.html MIT License
 * @since    1.0
 */
class CatalogPlugin extends DisplayEventPlugin implements DisplayInterface
{
    /**
     * Generates Catalog Datalist
     *
     * @return  $this
     * @since   1.0
     */
    public function onBeforeRenderView()
    {
        if ($this->runtime_data->application->id == 2) {
        } else {
            return $this;
        }

        $controller = $this->resources->get('query:///Molajo//Datasource//Catalog.xml');

        $controller->setModelRegistry('check_view_level_access', 0);
        $controller->setModelRegistry('process_events', 0);
        $controller->setModelRegistry('get_customfields', 0);
        $controller->setModelRegistry('query_object', 'list');

        $controller->model->query->select(
            $controller->model->database->qn($controller->getModelRegistry('primary_prefix', 'a'))
            . ' . '
            . $controller->model->database->qn('id')
        );

        $controller->model->query->select(
            $controller->model->database->qn($controller->getModelRegistry('primary_prefix', 'a'))
            . ' . '
            . $controller->model->database->qn('sef_request')
            . ' as value'
        );

        $controller->model->query->where(
            $controller->model->database->qn($controller->getModelRegistry('primary_prefix', 'a'))
            . ' . '
            . $controller->model->database->qn('application_id')
            . ' = '
            . (int)$this->runtime_data->application->id
        );

        $controller->model->query->where(
            $controller->model->database->qn($controller->getModelRegistry('primary_prefix', 'a'))
            . ' . '
            . $controller->model->database->qn('enabled')
            . ' = '
            . ' 1 '
        );

        $controller->model->query->where(
            $controller->model->database->qn($controller->getModelRegistry('primary_prefix', 'a'))
            . ' . '
            . $controller->model->database->qn('redirect_to_id')
            . ' = '
            . ' 0 '
        );
        $controller->model->query->where(
            $controller->model->database->qn($controller->getModelRegistry('primary_prefix', 'a'))
            . ' . '
            . $controller->model->database->qn('page_type')
            . ' <> '
            . $controller->model->database->q('link')
        );

        $controller->model->query->where(
            $controller->model->database->qn($controller->model->getModelRegistry('primary_prefix', 'a'))
            . '.' . $controller->model->database->qn('catalog_type_id')
            . ' = ' . $this->runtime_data->reference_data->catalog_type_menuitem_id
            . ' OR ' .
            $controller->model->database->qn($controller->model->getModelRegistry('primary_prefix', 'a'))
            . '.' . $controller->model->database->qn('catalog_type_id')
            . ' > ' . (int)$this->runtime_data->reference_data->catalog_type_tag_id
        );

        $controller->model->query->order(
            $controller->model->database->qn($controller->model->getModelRegistry('primary_prefix', 'a'))
            . '.' . $controller->model->database->qn('sef_request')
        );

        $controller->model->setModelRegistry('model_offset', 0);
        $controller->model->setModelRegistry('model_count', 99999);

        try {
            $temp_query_results = $controller->getData();
        } catch (Exception $e) {
            throw new RuntimeException ($e->getMessage());
        }

        $catalogArray = array();

        $application_home_catalog_id =
            (int)$this->runtime_data->application->parameters->application_home_catalog_id;

        if ($application_home_catalog_id === 0) {
        } else {
            if (count($temp_query_results) == 0 || $temp_query_results === false) {
            } else {

                foreach ($temp_query_results as $item) {

                    if ($item->id == $application_home_catalog_id) {
                        $item->value    = trim($this->language_controller->translate('Home'));
                        $catalogArray[] = $item;

                    } elseif (trim($item->value) == '' || $item->value === null) {
                        unset ($item);

                    } else {
                        $catalogArray[] = $item;
                    }
                }
            }
        }

        $this->runtime_data->plugin_data->datalists->catalog = $catalogArray;

        return $this;
    }

    /**
     * Post-create processing
     *
     * @return  $this
     * @since   1.0
     */
    public function onAfterCreate()
    {
        $id = $this->query_results->id;
        if ((int)$id == 0) {
            return $this;
        }

        /** Catalog Activity: fields populated by Catalog Activity plugins */
        if ($this->application->get('log_user_update_activity', 1) == 1) {
            $results = $this->logUserActivity($id, $this->registry->get('Actions', 'create'));
            if ($results === false) {
                return $this;
            }
        }

        if ($this->application->get('log_catalog_update_activity', 1) == 1) {
            $results = $this->logCatalogActivity($id, $this->registry->get('Actions', 'create'));
            if ($results === false) {
                return $this;
            }
        }

        return $this;
    }

    /**
     * Post-update processing
     *
     * @return  $this
     * @since   1.0
     */
    public function onAfterUpdate()
    {
        if ($this->application->get('log_user_update_activity', 1) == 1) {
            $results = $this->logUserActivity(
                $this->query_results->id,
                $this->registry->get('Actions', 'delete')
            );
            if ($results === false) {
                return $this;
            }
        }

        if ($this->application->get('log_catalog_update_activity', 1) == 1) {
            $results = $this->logCatalogActivity(
                $this->query_results->id,
                $this->registry->get('Actions', 'delete')
            );
            if ($results === false) {
                return $this;
            }
        }

        return $this;
    }

    /**
     * Pre-update processing
     *
     * @return  $this
     * @since   1.0
     */
    public function onBeforeUpdate()
    {
        return $this; // only redirect id
    }

    /**
     * Pre-delete processing
     *
     * @return  $this
     * @since   1.0
     */
    public function onBeforeDelete()
    {
        /** @todo - fix empty setModelRegistry */
        $controller_class_namespace = $this->controller_namespace;
        $controller                 = new $controller_class_namespace();
        $controller->model->getModelRegistryModelRegistry('x', 'y', 1);

        $sql = 'DELETE FROM ' . $controller->model->database->qn('#__catalog_categories');
        $sql .= ' WHERE ' . $controller->model->database->qn('catalog_id') . ' = ' . (int)$this->query_results->id;
        $controller->model->database->setQueryPermissions($sql);
        $controller->model->database->execute();

        $sql = 'DELETE FROM ' . $controller->model->database->qn('#__catalog_activity');
        $sql .= ' WHERE ' . $controller->model->database->qn('catalog_id') . ' = ' . (int)$this->query_results->id;
        $controller->model->database->setQueryPermissions($sql);
        $controller->model->database->execute();

        return $this;
    }

    /**
     * Post-delete processing
     *
     * @return  $this
     * @since   1.0
     */
    public function onAfterDelete()
    {
        //how to get id - referential integrity?
        /**
         * if ($this->application->get('log_user_update_activity', 1) == 1) {
         * $this->logUserActivity($id, $this->registry->get('Actions', 'delete'));
         * }
         * if ($this->application->get('log_catalog_update_activity', 1) == 1) {
         * $this->logCatalogActivity($id, $this->registry->get('Actions', 'delete'));
         * }
         */

        return $this;
    }

    /**
     * Log user updates
     *
     * @param   int $id
     * @param   int $action_id
     *
     * @return  $this
     * @since   1.0
     */
    public function logUserActivity($id, $action_id)
    {
        $data              = new \stdClass();
        $data->model_name  = 'UserActivity';
        $data->model_table = 'datasource';
        $data->catalog_id  = $id;
        $data->action_id   = $action_id;

        $controller       = new CreateController();
        $controller->data = $data;
        $user_activity_id = $controller->execute();
        if ($user_activity_id === false) {
            //install failed
            return $this;
        }

        return $this; // only redirect id
    }

    /**
     * Pre-update processing
     *
     * @param   int $id
     * @param   int $action_id
     *
     * @return  $this
     * @since   1.0
     */
    public function logCatalogActivity($id, $action_id)
    {
        $data              = new \stdClass();
        $data->model_name  = 'CatalogActivity';
        $data->model_table = 'datasource';
        $data->catalog_id  = $id;
        $data->action_id   = $action_id;

        $controller          = new CreateController();
        $controller->data    = $data;
        $catalog_activity_id = $controller->execute();
        if ($catalog_activity_id === false) {
            //install failed
            return $this;
        }

        return $this; // only redirect id
    }
}
