<?php
/**
 * @package    Niambie
 * @copyright  2013 Amy Stephen. All rights reserved.
 * @license    MIT
 */
defined('NIAMBIE') or die;  ?>
<fieldset>
<?php if ($this->parameters['criteria_title'] == '') {
} else {
    ?>
    <legend><?php echo $this->parameters['criteria_title']; ?></legend>
<?php }
