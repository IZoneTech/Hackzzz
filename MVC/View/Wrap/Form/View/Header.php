<?php
/**
 * Section Wrap View
 *
 * @package      Molajo
 * @license      MIT
 * @copyright    2013 Amy Stephen. All rights reserved.
 */
use Molajo\Service\Services;

defined('MOLAJO') or die;

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
<form<?php echo $headerId; ?><?php echo $headerClass; ?> method="post" action="<?php echo Services::Registry()->get(
    PAGE_LITERAL,
    'page_url'
); ?>">
