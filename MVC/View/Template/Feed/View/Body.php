<?php
/**
 * Feed Template View
 *
 * @package      Molajo
 * @license      MIT
 * @copyright    2013 Amy Stephen. All rights reserved.
 */
defined('MOLAJO') or die; ?>
<p>
    <a href="<?php echo $this->row->link; ?>">
        <strong>
            <?php echo $this->row->title; ?>
        </strong>
    </a>
    <?php if ($this->parameters['display_snippet'] == 1) {
    echo '<br />' . $this->row->description;
}
    if ($this->parameters['display_published_date'] == 1) {
        echo '<br />' . '<strong>' . Services::Language()->translate(
            'Published'
        ) . ':</strong> ' . $this->row->published_date;
    } ?>
</p>
