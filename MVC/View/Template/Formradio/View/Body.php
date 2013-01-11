<?php
/**
 * Formradio Template View
 *
 * @package      Niambie
 * @license      MIT
 * @copyright    2013 Amy Stephen. All rights reserved.
 */
defined('NIAMBIE') or die;
$tooltip_css_class = 'has-tip';
?>
<li>
    <input id="<?php echo $this->row->id; ?>"<?php echo $this->row->checked; ?> name="<?php echo $this->row->name; ?>"
           type="radio">
    <label for="<?php echo $this->row->id; ?>"><?php echo $this->row->id_label; ?></label>
</li>
