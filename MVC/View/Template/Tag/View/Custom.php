<?php
/**
 * Tag Template View
 *
 * @package      Molajo
 * @license      MIT
 * @copyright    2013 Amy Stephen. All rights reserved.
 */
use Molajo\Service\Services;

defined('MOLAJO') or die;
$action = Services::Registry()->get(PAGE_LITERAL, 'page_url'); ?>
<label>Tag</label>
<div class="row">
    <div class="five columns">
        <div class="row collapse">
            <div class="eight mobile-three columns">
                <input type="text"/>
            </div>
            <div class="four mobile-one columns">
                <a href="<?php echo $action; ?>" class="postfix button">Tag</a>
            </div>
        </div>
    </div>
</div>
