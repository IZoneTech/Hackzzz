<?php
/**
 * Ajax Plugin
 *
 * @package    Molajo
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014 Amy Stephen. All rights reserved.
 */
namespace Molajo\Plugin\Ajax;

use stdClass;
use Molajo\Plugin\SystemEventPlugin;
use CommonApi\Event\SystemInterface;

/**
 * Ajax Plugin
 *
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @since      1.0
 */
class AjaxPlugin extends SystemEventPlugin implements SystemInterface
{
    /**
     * Identify Ajax Request and adapt the Template, Wrap and Tokens
     *
     * @return  $this
     * @since   1.0
     */
    public function onBeforeExecute()
    {
        if ($this->runtime_data->request->client->ajax == 0) {
            return $this;
        }

        $this->runtime_data->resource->template = $this->resource->get('Template:///Molajo//View//Template//' . 1342);
        $this->runtime_data->resource->wrap     = $this->resource->get('Wrap:///Molajo//View//Wrap//' . 2090);

        $sequence = array();
        $x        = $this->resource->get('xml:///Molajo//Application//Ajax_sequence.xml')->include;
        foreach ($x as $y) {
            $sequence[] = (string)$y;
        }

        if (isset($this->runtime_data->render)) {
        } else {
            $this->runtime_data->render = new stdClass();
        }
        $this->runtime_data->render->token_sequence = $sequence;

        $final_sequence = array();
        $x              = $this->resource->get('xml:///Molajo//Application//Ajax_final.xml')->include;

        if ($x === null || count($x) > 0) {
        } else {
            foreach ($x as $y) {
                $final_sequence[] = (string)$y;
            }
        }

        $this->runtime_data->render->exclude_tokens = $final_sequence;

        return $this;
    }
}
