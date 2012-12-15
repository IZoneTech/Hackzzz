<?php
/**
 * @package    Niambie
 * @copyright  2012 Amy Stephen. All rights reserved.
 * @license    GNU GPL v 2, or later and MIT, see License folder
 */
namespace Molajo\Service\Services\Theme\Includer;

use Molajo\Service\Services;
use Molajo\Service\Services\Theme\Includer;
use Molajo\MVC\Controller\DisplayController;

defined('NIAMBIE') or die;

/**
 * Theme
 *
 * @package     Niambie
 * @subpackage  Includer
 * @since       1.0
 */
class ThemeIncluder extends Includer
{

    /**
     * Render and return output
     *
     * @param   $attributes
     *
     * @return  mixed
     * @since   1.0
     */
    public function process($attributes = array())
    {
        $this->loadPlugins();

        $this->renderOutput();

        return $this->rendered_output;
    }

    /**
     * Load Plugins Overrides from the Theme and/or Page View folders
     *
     * @return  void
     * @since   1.0
     */
    protected function loadPlugins()
    {
        Services::Event()->registerPlugins(
            Services::Registry()->get('include', 'theme_path'),
            Services::Registry()->get('include', 'theme_namespace')
        );
return;
        Services::Event()->registerPlugins(
            Services::Registry()->get('include', 'page_view_path'),
            Services::Registry()->get('include', 'page_view_namespace')
        );

        Services::Event()->registerPlugins(
            Services::Registry()->get('include', 'extension_path'),
            Services::Registry()->get('include', 'extension_namespace')
        );

        return;
    }

    /**
     * The Theme Includer renders the Theme include file and feeds in the Page Name Value
     *  The rendered output from that process provides the initial data to be parsed for Include statements
     */
    protected function renderOutput()
    {
        if (file_exists($this->get('theme_path_include'))) {
        } else {
            Services::Error()->set(500, 'Theme Not found');
            throw new \Exception('Theme not found ' . $this->get('theme_path_include'));
        }

        $controller = new DisplayController();
        $controller->set('include', Services::Registry()->getArray('include'));
        $this->set($this->get('extension_catalog_type_id', '', 'parameters'),
            CATALOG_TYPE_RESOURCE, 'parameters');

        $this->rendered_output = $controller->execute();
        echo $this->rendered_output;
        $this->loadMedia();

        $this->loadViewMedia();

        return;
    }

    /**
     * loadMedia
     *
     * Loads Media Files for Site, Application, User, and Theme
     *
     * @return  bool
     * @since   1.0
     */
    protected function loadMedia()
    {
        $this->loadMediaPlus('',
            Services::Registry()->get('include', 'asset_priority_site', 100));

        $this->loadMediaPlus('/application' . APPLICATION,
            Services::Registry()->get('include', 'asset_priority_application', 200));

        $this->loadMediaPlus('/user' . Services::Registry()->get(USER_LITERAL, 'id'),
            Services::Registry()->get('include', 'asset_priority_user', 300));

        $this->loadMediaPlus('/category' . Services::Registry()->get('include', 'catalog_category_id'),
            Services::Registry()->get('include', 'asset_priority_primary_category', 700));

        $this->loadMediaPlus('/menuitem' . Services::Registry()->get('include', 'menu_item_id'),
            Services::Registry()->get('include', 'asset_priority_menuitem', 800));

        $this->loadMediaPlus('/source/' . Services::Registry()->get('include', 'extension_title')
                . Services::Registry()->get('include', 'criteria_source_id'),
            Services::Registry()->get('include', 'asset_priority_item', 900));

        $this->loadMediaPlus('/resource/' . Services::Registry()->get('include', 'extension_title'),
            Services::Registry()->get('include', 'asset_priority_extension', 900));

        $priority = Services::Registry()->get('include', 'asset_priority_theme', 600);
        $file_path = Services::Registry()->get('include', 'theme_path');
        $url_path = Services::Registry()->get('include', 'theme_path_url');

        Services::Asset()->addCssFolder($file_path, $url_path, $priority);
        Services::Asset()->addJsFolder($file_path, $url_path, $priority, 0);
        Services::Asset()->addJsFolder($file_path, $url_path, $priority, 1);

        $priority = Services::Registry()->get('include', 'asset_priority_theme', 600);
        $file_path = Services::Registry()->get('include', 'page_view_path');
        $url_path = Services::Registry()->get('include', 'page_view_path_url');

        Services::Asset()->addCssFolder($file_path, $url_path, $priority);
        Services::Asset()->addJsFolder($file_path, $url_path, $priority, 0);
        Services::Asset()->addJsFolder($file_path, $url_path, $priority, 1);

        Services::Asset()->addLink(
            $url = Services::Registry()->get('include', 'theme_favicon'),
            $relation = 'shortcut icon',
            $relation_type = 'image/x-icon',
            $attributes = array()
        );

        $this->loadMediaPlus('', Services::Registry()->get('include', 'asset_priority_site', 100));

        return true;
    }

    /**
     * loadMediaPlus
     *
     * Loads Media Files for Site, Application, User, and Theme
     *
     * @return bool
     * @since   1.0
     */
    protected function loadMediaPlus($plus = '', $priority = 500)
    {
        /** Theme */
        $file_path = Services::Registry()->get('include', 'theme_path');
        $url_path = Services::Registry()->get('include', 'theme_path_url');
        $css = Services::Asset()->addCssFolder($file_path, $url_path, $priority);
        $js = Services::Asset()->addJsFolder($file_path, $url_path, $priority, 0);
        $defer = Services::Asset()->addJsFolder($file_path, $url_path, $priority, 1);
        if ($css === true || $js === true || $defer === true) {
            return true;
        }

        /** Site Specific: Application */
        $file_path = SITE_MEDIA_FOLDER . '/' . APPLICATION . $plus;
        $url_path = SITE_MEDIA_URL . '/' . APPLICATION . $plus;
        $css = Services::Asset()->addCssFolder($file_path, $url_path, $priority);
        $js = Services::Asset()->addJsFolder($file_path, $url_path, $priority, 0);
        $defer = Services::Asset()->addJsFolder($file_path, $url_path, $priority, 1);
        if ($css === true || $js === true || $defer === true) {
            return true;
        }

        /** Site Specific: Site-wide */
        $file_path = SITE_MEDIA_FOLDER . $plus;
        $url_path = SITE_MEDIA_URL . $plus;
        $css = Services::Asset()->addCssFolder($file_path, $url_path, $priority);
        $js = Services::Asset()->addJsFolder($file_path, $url_path, $priority, false);
        $defer = Services::Asset()->addJsFolder($file_path, $url_path, $priority, 1);
        if ($css === true || $js === true || $defer === true) {
            return true;
        }

        /** All Sites: Application */
        $file_path = SITES_MEDIA_FOLDER . '/' . APPLICATION . $plus;
        $url_path = SITES_MEDIA_URL . '/' . APPLICATION . $plus;
        $css = Services::Asset()->addCssFolder($file_path, $url_path, $priority);
        $js = Services::Asset()->addJsFolder($file_path, $url_path, $priority, 0);
        $defer = Services::Asset()->addJsFolder($file_path, $url_path, $priority, 1);
        if ($css === true || $js === true || $defer === true) {
            return true;
        }

        /** All Sites: Site Wide */
        $file_path = SITES_MEDIA_FOLDER . $plus;
        $url_path = SITES_MEDIA_URL . $plus;
        $css = Services::Asset()->addCssFolder($file_path, $url_path, $priority);
        $js = Services::Asset()->addJsFolder($file_path, $url_path, $priority, 0);
        $defer = Services::Asset()->addJsFolder($file_path, $url_path, $priority, 1);
        if ($css === true || $js === true || $defer === true) {
            return true;
        }

        /** nothing was loaded */

        return true;
    }
}