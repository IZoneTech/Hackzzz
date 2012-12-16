<?php
use Molajo\Service\Services;

/**
 * @package    Niambie
 * @copyright  2012 Amy Stephen. All rights reserved.
 * @license    MIT, see License folder
 * echo Services::Language()->translate('No textareaion')
 */
defined('NIAMBIE') or die;
$tooltip_css_class = 'has-tip';
?>
<span class="<?php echo $tooltip_css_class; ?>" title="<?php echo $this->row->tooltip; ?>"><label for="<?php echo $this->row->id; ?>"><?php echo $this->row->label; ?></label></span><textarea <?php
if ($this->row->placeholder == '') {
} else {
	echo 'placeholder' . '="' . $this->row->placeholder .'" ';
} ?>
