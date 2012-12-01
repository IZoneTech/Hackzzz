<?php
/**
 * @package    Molajo
 * @copyright  2012 Amy Stephen. All rights reserved.
 * @license    GNU GPL v 2, or later and MIT, see License folder
 */
namespace Molajo\Includer;

use Molajo\Service\Services;
use Molajo\Includer;

defined('MOLAJO') or die;

/**
 * User Interface Includer
 *
 * @package     Molajo
 * @subpackage  Includer
 * @since       1.0
 */
class UiIncluder extends Includer
{
    /**
     * process - render include statement for configured UI Library
     *
     * @param   $attributes
     *
     * @return mixed
     * @since   1.0
     */
    public function process($attributes = array())
    {
        $this->attributes = $attributes;

        parent::getAttributes();

        $includer = '<include:template';

        foreach ($this->attributes as $key => $value) {

            $useValue = $value;

            if ($key == 'name') {

                $find = 'ui' . trim($value);

                $useValue = Services::Registry()->get(CONFIGURATION_LITERAL, $find);

                if ((int) $useValue == 0) {
                    $useValue = Services::Registry()->get(CONFIGURATION_LITERAL, $find);
                }
            }
            $includer .= ' ' . trim($key) . '=' . trim($useValue);
        }
        $includer .= '/>';

        return $includer;
    }
}
