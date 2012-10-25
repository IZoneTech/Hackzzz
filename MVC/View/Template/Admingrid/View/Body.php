<?php
use Molajo\Service\Services;

/**
 * @package    Molajo
 * @copyright  2012 Individual Molajo Contributors. All rights reserved.
 * @license    GNU GPL v 2, or later and MIT, see License folder
 */
defined('MOLAJO') or die; ?>
<tr<?php echo $this->row->grid_row_class; ?>><?php
    $columnCount = 1;
    $nowrap = ' nowrap';
    $first = 1;
    $columnArray = Services::Registry()->get('Plugindata', 'AdminGridTableColumns');
    foreach ($columnArray as $column) {
        $class = '';
        $nowrap = '';
        if ($column == 'ordering') {
            $nowrap = ' nowrap';
        } elseif ($column == 'featured' || $column == 'stickied' || $column == 'status') {
            $class = ' class="center"';
        }
        ?>

        <td<?php echo $class ?><?php echo $nowrap; ?>><?php
            if ($column == 'title') {
                echo '<a href="' . $this->row->catalog_id_url . '">';
                echo $this->row->$column;
                echo '</a>';

            } elseif ($column == 'username') {
                    echo '<a href="' . $this->row->catalog_id_url . '">';
                    echo $this->row->$column;
                    echo '</a>';

            } elseif ($column == 'status') {
                echo '<span class="status">';
                if ((int) $this->row->status == 2) {
                    echo '<i class="icon-lock" alt="' . Services::Language()->translate($this->row->status) .'"></i>';
                } elseif ((int) $this->row->status == 1) {
                    echo '<i class="icon-ok" alt="' . Services::Language()->translate($this->row->status) .'"></i>';
                } elseif ((int) $this->row->status == 0) {
                    echo '<i class="icon-off" alt="' . Services::Language()->translate($this->row->status) .'"></i>';
                } elseif ((int) $this->row->status == -1) {
                    echo '<i class="icon-trash" alt="' . Services::Language()->translate($this->row->status) .'"></i>';
                } elseif ((int) $this->row->status == -2) {
                    echo '<i class="icon-ban-circle" alt="' . Services::Language()->translate($this->row->status) .'"></i>';
                } elseif ((int) $this->row->status == -5) {
                    echo '<i class="icon-pencil" alt="' . Services::Language()->translate($this->row->status) .'"></i>';
                } elseif ((int) $this->row->status == -10) {
                    echo '<i class="icon-camera" alt="' . Services::Language()->translate($this->row->status) .'"></i>';
                }
                echo '</span>';

            } elseif ($column == 'stickied') {
                echo '<span class="stickied">';
                if ((int) $this->row->$column == 1) {
                    echo '<i class="icon-star" alt="' . Services::Language()->translate('Stickied') .'"></i>';
                } else {
                    echo '<i class="icon-star-empty" alt="' . Services::Language()->translate('Not Stickied') .'"></i>';
                }
                echo '</span>';

            } elseif ($column == 'featured') {
                echo '<span class="featured">';
                if ((int) $this->row->$column == 1) {
                    echo '<i class="icon-star" alt="' . Services::Language()->translate('Featured') .'"></i>';
                } else {
                    echo '<i class="icon-star-empty" alt="' . Services::Language()->translate('Not Featured') .'"></i>';
                }
                echo '</span>';

            } elseif ($column == 'ordering') {
                echo '<span class="orderingicons">';
                if ((int) $this->row->last_row == 1) {
                    echo ' ';
                } else {
                    echo '<i class="icon-arrow-down"></i>';
                }
                if ((int) $this->row->$column == 1) {
                    echo ' ';
                } else {
                    echo '<i class="icon-arrow-up"></i>';
                }
                echo '</span>';
                echo '<span class="ordering">';
                echo $this->row->$column;
                echo '</span>';
            } else {
                echo $this->row->$column;
            }
        ?>
        </td><?php

        if ($first == 1) {
            $first = 0;
            $nowrap = '';
        }

        $columnCount++;
    }
    ?>
    <td class="center last">
        <input type=checkbox value="<?php echo $checked; ?>">
    </td>
</tr>
