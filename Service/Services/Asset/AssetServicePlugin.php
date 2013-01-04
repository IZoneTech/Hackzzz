<?php
/**
 * Asset Service Plugin
 *
 * @package      Niambie
 * @license      MIT
 * @copyright    2013 Amy Stephen. All rights reserved.
 */
namespace Molajo\Service\Services\Asset;

use Molajo\Service\Services;
use Molajo\Plugin\Plugin\Plugin;

defined('NIAMBIE') or die;

/**
 * Asset Service Plugin
 *
 * @author       Amy Stephen
 * @license      MIT
 * @copyright    2013 Amy Stephen. All rights reserved.
 * @since        1.0
 */
Class AssetServicePlugin extends Plugin
{
    /**
     * on Before Startup Event
     *
     * Follows instantiation of the service class and before the method identified as the "start" method
     *
     * @return  void
     * @since   1.0
     */
    public function onBeforeServiceStartup()
    {

    }

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
        $this->service_class->set('html5', $this->frontcontroller_class->get('application_html5'));
        $this->service_class->set('line_end', $this->frontcontroller_class->get('application_line_end'));
        $this->service_class->set('mimetype', $this->frontcontroller_class->get('request_mimetype'));
        $this->service_class->set('direction', $this->frontcontroller_class->get('language_direction'));

        return;
    }
}
