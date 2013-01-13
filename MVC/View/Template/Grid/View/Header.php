<?php
/**
 * Gallery Template View
 *
 * @package      Molajo
 * @license      MIT
 * @copyright    2013 Amy Stephen. All rights reserved.
 */
use Molajo\Service\Services;

defined('MOLAJO') or die;
$nowrap   = '';
$checked  = '';
$rowCount = Services::Registry()->get('Grid', 'Tablerows'); ?>
<table class="grid responsive">
    <thead>
    <tr>
        <?php
        $count       = 1;
        $columnArray = Services::Registry()->get('Grid', 'Tablecolumns');
        foreach ($columnArray as $column) {
            $extraClass = '';
            $nowrap     = '';
            if ($count == 1) {
                $extraClass .= 'first';
                $nowrap = ' nowrap';
            }
            if ($count == count($columnArray)) {
                $extraClass .= 'last';
            }
            if ($extraClass == '') {
            } else {
                $extraClass = ' class="' . trim($extraClass) . '"';
            }
            ?>
            <th<?php echo $extraClass . $nowrap; ?>><span><?php echo Services::Language()->translate(
                'GRID_' . strtoupper($column) . '_COLUMN_HEADING'
            ); ?></span></th>
            <?php
            $count++;
        } ?>
        <th width="1%">
            <input type="checkbox" class="checkall">
        </th>
    </tr>
    </thead>
    <tbody>
