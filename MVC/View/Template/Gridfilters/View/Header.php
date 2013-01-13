<?php
/**
 * Gridfilters Template View
 *
 * @package      Molajo
 * @license      MIT
 * @copyright    2013 Amy Stephen. All rights reserved.
 */
use Molajo\Service\Services;

defined('MOLAJO') or die; ?>
<ol class="filters">
    <li>
        <strong><?php echo Services::Language()->translate('Search'); ?></strong>
    </li>
    <li>
        <input name="search" id="search" type="text"
               placeholder="<?php echo Services::Language()->translate('Search For'); ?>"/>
    </li>
    <li>
        <input type="submit" class="submit button small right" name="search-button" id="search-button" value="Search">
        <br/>
    </li>
    <li>
        <strong><?php echo Services::Language()->translate('Filters'); ?></strong>
    </li>
