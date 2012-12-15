<?php
use Molajo\Service\Services;

/**
 * @package    Niambie
 * @copyright  2012 Amy Stephen. All rights reserved.
 * @license    GNU GPL v 2, or later and MIT, see License folder
 */
defined('NIAMBIE') or die;

$headerId = trim($this->parameters['wrap_view_css_id']);
if ($headerId == '') :
else :
    $headerId = ' id="' . $headerId . '"';
endif;

$headerClass = trim($this->parameters['wrap_view_css_class']);
if ($headerClass == '') :
else :
    $headerClass = ' class="' . $headerClass . '"';
endif;
?>
<form<?php echo $headerId; ?><?php echo $headerClass; ?> method="post" action="<?php echo Services::Registry()->get(PAGE_LITERAL, 'page_url'); ?>">
