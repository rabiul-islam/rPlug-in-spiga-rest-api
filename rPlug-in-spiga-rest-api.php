<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://www.rabiulislam.info/
 * @since             1.0.0
 *
 * @wordpress-plugin
 * Plugin Name:       Spiga Rest API's
 * Plugin URI:        https://github.com/rabiul-islam
 * Description:       This is a short description of what the plugin does. It's displayed in the WordPress admin area.
 * Version:           1.0.0
 * Author:            Md Rabiul islam
 * Author URI:        https://www.rabiulislam.info/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       spiga-rest-api
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

/**
 * Plugin all configurations.
 * All config constant defined here.
 */
require_once 'spiga-rest-api-config.php';

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-spiga-rest-api-activator.php
 */
function activate_spiga_rest_api()
{
    require_once SRA_DIR_PATH . 'includes/class-spiga-rest-api-activator.php';
    Spiga_Rest_Api_Activator::activate(plugin_basename(__FILE__));
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-spiga-rest-api-deactivator.php
 */
function deactivate_spiga_rest_api()
{
    require_once SRA_DIR_PATH . 'includes/class-spiga-rest-api-deactivator.php';
    Spiga_Rest_Api_Deactivator::deactivate();
}

register_activation_hook(__FILE__, 'activate_spiga_rest_api');
register_deactivation_hook(__FILE__, 'deactivate_spiga_rest_api');

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require SRA_DIR_PATH . 'includes/class-spiga-rest-api.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_spiga_rest_api()
{
    $spiga_rest_api_plugin = new Spiga_Rest_Api();
    $spiga_rest_api_plugin->run();
}
run_spiga_rest_api();
