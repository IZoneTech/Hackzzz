<?php
/**
 * Gridcategories Template View
 *
 * @package      Molajo
 * @license      MIT
 * @copyright    2013 Amy Stephen. All rights reserved.
 */
use Molajo\Service\Services;

defined('MOLAJO') or die;
//if ($this->row->enable == 1) {
?>
<div class="grid-categories grid-batch">
    <ol class="grid-batch">
        <li><strong><?php echo Services::Language()->translate('Category Assignment'); ?></strong></li>
        <li>
            <include:template name=formselectlist datalist=categories/>
        </li>
        <li><input type="submit" class="submit button small radius" name="submit" id="AssignCategories" value="Assign"/>
        </li>
        <li><input type="submit" class="submit button small radius" name="submit" id="RemoveCategories" value="Remove"/>
        </li>
    </ol>
</div>
<?php //}
