<?php
/**
 * Admindashboardtab Template View
 *
 * @package      Niambie
 * @license      MIT
 * @copyright    2013 Amy Stephen. All rights reserved.
 */
defined('NIAMBIE') or die;

if ($this->row->new_fieldset == '1') {
?>
</ol>
</fieldset>
<fieldset class="two-up">
    <legend><?php echo $this->row->tab_fieldset_title; ?></legend>
    <p><?php echo $this->row->tab_fieldset_description; ?></p>
    <ol>
<?php } ?>
    <li>
        <include:template name=<?php echo $this->row->view; ?> parameter_np=<?php echo $this->row->name; ?>/>
    </li>
