<?php
use Molajo\Service\Services;

/**
 * @package    Niambie
 * @copyright  2012 Amy Stephen. All rights reserved.
 * @license    GNU GPL v 2, or later and MIT, see License folder
 */
defined('NIAMBIE') or die;
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
