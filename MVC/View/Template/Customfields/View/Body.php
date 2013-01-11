<?php
/**
 * Customfields Template View
 *
 * @package      Niambie
 * @license      MIT
 * @copyright    2013 Amy Stephen. All rights reserved.
 */
use Molajo\Service\Services;

defined('NIAMBIE') or die; ?>
<tr>
    <td class="name"><?php echo Services::Language()->translate($this->row->label); ?></td>
    <td class="type"><?php echo $this->row->type; ?></td>
    <td class="required"><?php $this->row->required; ?></td>
    <td class="datalist"><?php echo $this->row->datalist; ?></td>
    <td class="hidden"><?php echo $this->row->hidden; ?></td>
    <td><a href="#" class="small button alert radius"><?php echo Services::Language()->translate(
        'Delete',
        'Delete'
    ); ?></a></td>
</tr>
