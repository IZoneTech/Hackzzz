<?php

/**
 * @package    Niambie
 * @copyright  2012 Amy Stephen. All rights reserved.
 * @license    MIT, see License folder
 */
defined('NIAMBIE') or die;
$csv = "";
foreach ($this->query_results as $row) {
    $csv .= join(",", $row) . "\n";
}
echo $csv;
