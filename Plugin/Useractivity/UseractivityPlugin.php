<?php
/**
 * @package    Molajo
 * @copyright  2012 Individual Molajo Contributors. All rights reserved.
 * @license    GNU GPL v 2, or later and MIT, see License folder
 */
namespace Molajo\Plugin\Useractivity;

use Molajo\Plugin\Plugin\Plugin;
use Molajo\Service\Services;

defined('MOLAJO') or die;

/**
 * Useractivity
 *
 * @package     Molajo
 * @subpackage  Plugin
 * @since       1.0
 */
class UseractivityPlugin extends Plugin
{

    /**
     * onAfterRead
     *
     * @return boolean
     * @since   1.0
     */
    public function onAfterRead()
    {
        if ($this->get('criteria_log_user_view_activity', 0) == 1) {
            return $this->setUserActivityLog();
        }

        return;
    }

    /**
     * onAfterCreate
     *
     * @return boolean
     * @since   1.0
     */
    public function onAfterCreate()
    {
        if ($this->get('criteria_log_user_activity_create', 0) == 1) {
            return $this->setUserActivityLog();
        }

        return true;
    }

    /**
     * onAfterUpdate
     *
     * @return boolean
     * @since   1.0
     */
    public function onAfterUpdate()
    {
        if ($this->get('criteria_log_user_update_activity', 0) == 1) {
            return $this->setUserActivityLog();
        }

        return true;
    }

    /**
     * onAfterDelete
     *
     * @return boolean
     * @since   1.0
     */
    public function onAfterDelete()
    {
        if ($this->get('criteria_log_user_activity_delete', 0) == 1) {
            return $this->setUserActivityLog();
        }

        return true;
    }

    /**
     * onAfterRead
     *
     * User Activity
     *
     * @return boolean
     * @since   1.0
     */
    public function setUserActivityLog()
    {
        /** Retrieve Key for Action  */
        $action_id = Services::Registry()->get(
            'Actions',
            $this->get('action', 'display')
        );

        /** Retrieve User Data  */
        $controllerClass = 'Molajo\\MVC\\Controller\\Controller';
        $m = new $controllerClass();
        $results = $m->connect('Table', 'UserActivity');
        if ($results === false) {
            return false;
        }

        $m->set('user_id', Services::Registry()->set('User', 'id'));
        $m->set('action_id', $action_id);
        $m->set('catalog_id', $this->data->catalog_id);
        $m->set('activity_datetime', null);

        $results = $m->getData('create');

        return true;
    }
}
