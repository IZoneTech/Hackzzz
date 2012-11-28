<?php
/**
 * @package    Molajo
 * @copyright  2012 Individual Molajo Contributors. All rights reserved.
 * @license    GNU GPL v 2, or later and MIT, see License folder
 */
namespace Molajo\Plugin\Datalist;

use Molajo\Plugin\Plugin\Plugin;
use Molajo\Service\Services;

defined('MOLAJO') or die;

/**
 * @package     Molajo
 * @subpackage  Plugin
 * @since       1.0
 */
class DatalistPlugin extends Plugin
{
	/**
	 * Prepares list of Datalist Lists
	 *
	 * @return boolean
	 * @since   1.0
	 */
	public function onAfterRoute()
	{
		if (APPLICATION_ID == 2) {
		} else {
			return true;
		}

		$files = Services::Filesystem()->folderFiles(
			PLATFORM_FOLDER . '/Datalist'
		);

		if (count($files) === 0 || $files === false) {
			$dataLists = array();
		} else {
			$datalistLists = $this->processFiles($files);
		}

		$resourceFiles = Services::Filesystem()->folderFiles(
			Services::Registry()->get(PARAMETERS_LITERAL, 'extension_path') . '/Datalist'
		);

		if (count($resourceFiles) == 0 || $resourceFiles === false) {
			$resourceLists = array();
		} else {
			$resourceLists = $this->processFiles($resourceFiles);
		}

		$new = array_merge($datalistLists, $resourceLists);
		$newer = array_unique($new);
		sort($newer);

		$datalist = array();

		foreach ($newer as $file) {
			$row = new \stdClass();
			$row->value = $file;
			$row->id = $file;
			$datalist[] = $row;
		}

		Services::Registry()->set(DATALIST_LITERAL, 'Datalists', $datalist);

		return true;
	}

	/**
	 * Prepares list of Datalist Lists
	 *
	 * @return  boolean
	 * @since   1.0
	 */
	protected function processFiles($files)
	{
		$fileList = array();

		foreach ($files as $file) {

			$length = strlen($file) - strlen('.xml');
			$value = substr($file, 0, $length);

			$fileList[] = $value;
		}

		return $fileList;
	}
}
