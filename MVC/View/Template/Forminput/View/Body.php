<?php
/**
 * @package    Niambie
 * @copyright  2012 Amy Stephen. All rights reserved.
 * @license    MIT
 */
defined('NIAMBIE') or die;
if ($this->row->value == $this->row->key) {
    echo $this->row->key .' ';
} else {
    echo $this->row->key . '="' . $this->row->value .'" ';
}
