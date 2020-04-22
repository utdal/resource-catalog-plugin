<?php

namespace UTDallasResourceCatalog;

use UTDallasResourceCatalog\PostTypes\Resource;
use UTDallasResourceCatalog\Shortcodes\ResourceCatalog;
use UTDallasResourceCatalog\Taxonomies\Audiences;
use UTDallasResourceCatalog\Taxonomies\Lengths;
use UTDallasResourceCatalog\Taxonomies\Programs;

class ResourceCatalogPlugin
{
    /** @var string The current version of the plugin. */
    protected $version = PLUGIN_VERSION;

    /** @var string The url of the assets folder. */
    protected $asset_url = PLUGIN_URL . 'public';

    /** @var string The filesystem path of the plugin */
    protected $plugin_path = PLUGIN_PATH;

    public function load()
    {
        require_once($this->plugin_path . '/includes/cmb2/init.php');

        add_action('init', [$this, 'registerPostTypes']);
        add_action('init', [$this, 'registerShortcodes']);
        add_action('init', [$this, 'registerTaxonomies'], 15);
        add_action('cmb2_admin_init', [$this, 'registerCustomFields']);
        add_action('rest_api_init', [$this, 'registerCustomFieldAPIs']);
        add_action('wp_enqueue_scripts', [$this, 'registerScripts']);
    }

    public function registerScripts()
    {
        wp_register_style('resource_catalog_css', $this->asset_url . '/css/resource-catalog.css', [], $this->version);

        wp_register_script('vue.js', 'https://cdn.jsdelivr.net/npm/vue@2.6.10/dist/vue.min.js', [], '2.6.10');
        wp_register_script('resource_catalog_js', $this->asset_url . '/js/resource-catalog.js', ['vue.js'], $this->version);
    }

    public function registerPostTypes()
    {
        (new Resource())->register();
    }

    public function registerCustomFields()
    {
        (new Resource())->registerFields();
    }

    public function registerCustomFieldAPIs()
    {
        (new Resource())->registerFieldAPIs();
    }

    public function registerTaxonomies()
    {
        $resource_name = (new Resource())->name;

        (new Audiences([$resource_name]))->register();
        (new Lengths([$resource_name]))->register();
        (new Programs([$resource_name]))->register();
    }

    public function registerShortcodes($templates)
    {
        (new ResourceCatalog)->register();
    }

}