<?php

echo '<pre>';
var_dump($this->row);
echo '</pre>';
/**
 * @package    Molajo
 * @copyright  2012 Individual Molajo Contributors. All rights reserved.
 * @license    GNU GPL v 2, or later and MIT, see License folder
 */
defined('MOLAJO') or die; ?>
<h2>
<?php if (isset($this->row->title)) {
    echo $this->row->title;
}?>
</h2>
<?php
if (isset($this->row->text)) {
    echo $this->row->text;
}
