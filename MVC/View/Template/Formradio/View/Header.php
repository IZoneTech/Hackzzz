<?php
/**
 * Formradio Template View
 *
 * @package      Molajo
 * @license      MIT
 * @copyright    2013 Amy Stephen. All rights reserved.
 */
defined('MOLAJO') or die;
$tooltip_css_class = 'has-tip'; ?>
<fieldset class="radio">
    <legend class="radio">
		<span class="<?php echo $tooltip_css_class; ?>" title="<?php echo $this->row->tooltip; ?>">
			<?php echo $this->row->label; ?>
		</span>
    </legend>
    <ol>
