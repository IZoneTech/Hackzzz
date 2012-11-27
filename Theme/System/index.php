<?php
use Molajo\Service\Services;

/**
 * @package    Molajo
 * @copyright  2012 Individual Molajo Contributors. All rights reserved.
 * @license    GNU GPL v 2, or later and MIT, see License folder
 * <include:profiler/>
 */
defined('MOLAJO') or die; ?>
<include:head/>
<?php if (file_exists(Services::Registry()->get(DATA_OBJECT_PARAMETERS, 'page_view_path_include'))) {
        include Services::Registry()->get(DATA_OBJECT_PARAMETERS, 'page_view_path_include');
} ?>
<include:defer/>
