<?php
/**
 * @package    Molajo
 * @copyright  2012 Individual Molajo Contributors. All rights reserved.
 * @license    GNU GPL v 2, or later and MIT, see License folder
 */
namespace Molajo\Includer;

use Molajo\Helpers;
use Molajo\Service\Services;
use Molajo\Includer;

defined('MOLAJO') or die;

/**
 * Theme
 *
 * @package     Molajo
 * @subpackage  Includer
 * @since       1.0
 */
Class ThemeIncluder extends Includer
{
    /**
     * __construct
     *
     * Class constructor.
     *
     * @param string $name
     * @param string $type
     *
     * @return null
     * @since   1.0
     */
    public function __construct($name = null, $type = null)
    {
        Services::Registry()->set(PARAMETERS_LITERAL, 'extension_catalog_type_id', CATALOG_TYPE_THEME);
        $this->name = $name;
        $this->type = $type;

        return $this;
    }

    /**
     * render
     *
     * Establishes language files and media for theme
     *
     * @param   $attributes <include:type attr1=x attr2=y attr3=z ... />
     *
     * @return mixed
     * @since   1.0
     */
    public function process($attributes = array())
    {
        $this->loadMedia();

        return;
    }

    /**
     * loadMedia
     *
     * Loads Media Files for Site, Application, User, and Theme
     *
     * @return boolean True, if the file has successfully loaded.
     * @since   1.0
     */
    protected function loadMedia()
    {
        /**  Site */
        $this->loadMediaPlus('',
            Services::Registry()->get(PARAMETERS_LITERAL, 'asset_priority_site', 100));

        /** Application */
        $this->loadMediaPlus('/application' . APPLICATION,
            Services::Registry()->get(PARAMETERS_LITERAL, 'asset_priority_application', 200));

        /** User */
        $this->loadMediaPlus('/user' . Services::Registry()->get('User', 'id'),
            Services::Registry()->get(PARAMETERS_LITERAL, 'asset_priority_user', 300));

        /** Theme */
        $priority = Services::Registry()->get(PARAMETERS_LITERAL, 'asset_priority_theme', 600);
        $file_path = Services::Registry()->get(PARAMETERS_LITERAL, 'theme_path');
        $url_path = Services::Registry()->get(PARAMETERS_LITERAL, 'theme_path_url');

        Services::Asset()->addCssFolder($file_path, $url_path, $priority);
        Services::Asset()->addJsFolder($file_path, $url_path, $priority, 0);
        Services::Asset()->addJsFolder($file_path, $url_path, $priority, 1);

        /** Page */
        $priority = Services::Registry()->get(PARAMETERS_LITERAL, 'asset_priority_theme', 600);
        $file_path = Services::Registry()->get(PARAMETERS_LITERAL, 'page_view_path');
        $url_path = Services::Registry()->get(PARAMETERS_LITERAL, 'page_view_path_url');

        Services::Asset()->addCssFolder($file_path, $url_path, $priority);
        Services::Asset()->addJsFolder($file_path, $url_path, $priority, 0);
        Services::Asset()->addJsFolder($file_path, $url_path, $priority, 1);

        /** Site Favicon */
        Services::Asset()->addLink(
            $url = Services::Registry()->get(PARAMETERS_LITERAL, 'theme_favicon'),
            $relation = 'shortcut icon',
            $relation_type = 'image/x-icon',
            $attributes = array()
        );

        /** Catalog ID specific */
        $this->loadMediaPlus('', Services::Registry()->get(PARAMETERS_LITERAL, 'asset_priority_site', 100));

        return;
    }

    /**
     * loadMediaPlus
     *
     * Loads Media Files for Site, Application, User, and Theme
     *
     * @return boolean True, if the file has successfully loaded.
     * @since   1.0
     */
    protected function loadMediaPlus($plus = '', $priority = 500)
    {
        /** Site Specific: Application */
        $file_path = SITE_MEDIA_FOLDER . '/' . $plus;
        $url_path = SITE_MEDIA_URL . '/' . $plus;

        $css = Services::Asset()->addCssFolder($file_path, $url_path, $priority);
        $js = Services::Asset()->addJsFolder($file_path, $url_path, $priority, 0);
        $defer = Services::Asset()->addJsFolder($file_path, $url_path, $priority, 1);

        /** Site Specific: Site-wide */
        $file_path = SITE_MEDIA_FOLDER . $plus;
        $url_path = SITE_MEDIA_URL . $plus;

        $css = Services::Asset()->addCssFolder($file_path, $url_path, $priority);
        $js = Services::Asset()->addJsFolder($file_path, $url_path, $priority, false);
        $defer = Services::Asset()->addJsFolder($file_path, $url_path, $priority, 1);

        /** All Sites: Application */
        $file_path = SITES_MEDIA_FOLDER . '/' . APPLICATION . $plus;
        $url_path = SITES_MEDIA_URL . '/' . APPLICATION . $plus;

        $css = Services::Asset()->addCssFolder($file_path, $url_path, $priority);
        $js = Services::Asset()->addJsFolder($file_path, $url_path, $priority, 0);
        $defer = Services::Asset()->addJsFolder($file_path, $url_path, $priority, 1);

        /** All Sites: Site Wide */
        $file_path = SITES_MEDIA_FOLDER . $plus;
        $url_path = SITES_MEDIA_URL . $plus;

        $css = Services::Asset()->addCssFolder($file_path, $url_path, $priority);
        $js = Services::Asset()->addJsFolder($file_path, $url_path, $priority, 0);
        $defer = Services::Asset()->addJsFolder($file_path, $url_path, $priority, 1);

        return;
    }
}
