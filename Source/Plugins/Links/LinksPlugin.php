<?php
/**
 * Links Plugin
 *
 * @package    Molajo
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 */
namespace Molajo\Plugins\Links;

use CommonApi\Event\ReadInterface;
use Molajo\Plugins\ReadEventPlugin;

/**
 * Links Plugin
 *
 * @package  Molajo
 * @license  http://www.opensource.org/licenses/mit-license.html MIT License
 * @since    1.0
 */
class LinksPlugin extends ReadEventPlugin implements ReadInterface
{
    /**
     * After-read processing
     *
     * Retrieves Author Information for Item
     *
     * @return  $this
     * @since   1.0
     */
    public function onAfterRead()
    {
        if (isset($this->runtime_data->route)) {
        } else {
            return $this;
        }

        /**
         * if ($align == 'right') {
         * $css = '.gravatar { float:right; margin: 0 0 15px 15px; }';
         * } else {
         * $css = '.gravatar { float:left; margin: 0 15px 15px 0; }';
         * }
         * $this->document_css->setDeclaration($css, 'text/css');
         */
        $fields = $this->getFieldsByType('text');

        if (is_array($fields) && count($fields) > 0) {

            foreach ($fields as $field) {

                $text_field = $this->getFieldValue($field);

                $pattern = "/(((http[s]?:\/\/)|(www\/.))?(([a-z][-a-z0-9]+\/.)?[a-z][-a-z0-9]+\/.[a-z]+(\/.[a-z]{2,2})?)\/?[a-z0-9._\/~#&=;%+?-]+[a-z0-9\/#=?]{1,1})/is";

                $text_field = preg_replace($pattern, " <a href='$1'>$1</a>", $text_field);

                $text_field = preg_replace("/href=\"www/", "href=\"http://www", $text_field);

                $this->setField($field, $field['name'], $text_field);
            }
        }

        return $this;
    }
}
