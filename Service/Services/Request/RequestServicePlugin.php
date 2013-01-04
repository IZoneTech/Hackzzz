<?php
/**
 * Request Service Plugin
 *
 * @package      Niambie
 * @license      MIT
 * @copyright    2013 Amy Stephen. All rights reserved.
 */
namespace Molajo\Service\Services\Request;

use Molajo\Service\Services;
use Molajo\Service\ServicesPlugin;

defined('NIAMBIE') or die;

/**
 * Request Service Plugin
 *
 * @author       Amy Stephen
 * @license      MIT
 * @copyright    2013 Amy Stephen. All rights reserved.
 * @since        1.0
 */
Class RequestServicePlugin extends ServicesPlugin
{
    /**
     * On After Startup Event
     *
     * Follows the completion of the start method defined in the configuration
     *
     * @return  void
     * @since   1.0
     */
    public function onAfterServiceStartup()
    {
        $this->frontcontroller_class
            ->set('request_method', $this->service_class->get('method', 'GET'));
        $this->frontcontroller_class
            ->set('request_mimetype', $this->service_class->get('mimetype', 'text/html'));
        $this->frontcontroller_class
            ->set('request_post_variables', $this->service_class->get('post_variables', array()));
        $this->frontcontroller_class
            ->set('request_using_ssl', $this->service_class->get('is_secure'));
        $this->frontcontroller_class
            ->set('request_base_url', $this->service_class->get('base_url'));

        return;
    }
}
