<?php
/**
 * Default Template View
 *
 * @package      Niambie
 * @license      MIT
 * @copyright    2013 Amy Stephen. All rights reserved.
 */
defined('NIAMBIE') or die; ?>
<h2>
    <?php if (isset($this->row->title)) {
    echo $this->row->title;
}?>
</h2>
<?php
if (isset($this->row->text)) {
    echo $this->row->text;
}
