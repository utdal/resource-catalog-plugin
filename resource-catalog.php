<?php

/**
 * The plugin bootstrap file
 *
 * @link              https://github.com/utdallasresearch/resource-catalog
 * @since             1.0.0
 * @package           UTDallas_Resource_Catalog
 *
 * @wordpress-plugin
 * Plugin Name:       UT Dallas Resource Catalog
 * Plugin URI:        https://github.com/utdallasresearch/resource-catalog
 * Description:       Manage and display a searchable, filterable catalog of resources. See `readme.md` for more info.
 * Version:           1.0.3
 * Author:            UT Dallas Research Information Systems
 * Author URI:        https://research.utdallas.edu
 * License:           MIT
 * License URI:       https://opensource.org/licenses/MIT
 * Text Domain:       utdallas-resource-catalog
 * Domain Path:       /languages
 */

define('UTDallasResourceCatalog\PLUGIN_VERSION', '1.0.3');
define('UTDallasResourceCatalog\PLUGIN_PATH', plugin_dir_path(__FILE__));
define('UTDallasResourceCatalog\PLUGIN_URL', plugin_dir_url(__FILE__));

// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

//////////////////////////////
// Autoload plugin classes  //
//////////////////////////////

spl_autoload_register(function ($class_name) {
    $namespace = 'UTDallasResourceCatalog\\';
    $namespace_length = strlen($namespace);

    // Only load UTDallasResourceCatalog classes
    if (strncmp($namespace, $class_name, $namespace_length) !== 0) {
        return;
    }

    $relative_class = substr($class_name, $namespace_length);
    $filename = plugin_dir_path(__FILE__) . 'src/' . str_replace('\\', '/', $relative_class) . '.php';

    if (file_exists($filename)) {
        include_once $filename;
    }
});

(new \UTDallasResourceCatalog\ResourceCatalogPlugin())->load();
