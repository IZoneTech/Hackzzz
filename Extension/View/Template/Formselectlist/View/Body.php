<?php
use Molajo\Service\Services;
/**
 * @package    Molajo
 * @copyright  2012 Amy Stephen. All rights reserved.
 * @license    GNU GPL v 2, or later and MIT, see license folder
 */
defined('MOLAJO') or die;
?>
<option value="<?php echo $this->row->id; ?>"<?php echo $this->row->selected; ?>><?php echo $this->row->value; ?></option>