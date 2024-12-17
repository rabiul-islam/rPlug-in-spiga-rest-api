<?php

/**
 * Fired during plugin activation
 *
 * @link       https://selise.ch/
 * @since      1.0.0
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 *
 * @author     Selise Web <rafin.biswas@selise.ch>
 */
class Spiga_Rest_Api_Activator
{
    /**
     * Activation hook actions.
     * check is woocommerce installed!
     *
     * @param string $base_file Name of plugin base file
     *
     * @since    1.0.0
     */
    public static function activate($base_file)
    {
        if (!function_exists('WC')) {
            deactivate_plugins($base_file);
            wp_die(
                '<div class="error"><p>' . sprintf(
                    __('<b>This plugin</b> requires %sWooCommerce%s to be installed & activated!', SRA_TEXTDOMAIN),
                    '<a target="_blank" href="https://wordpress.org/plugins/woocommerce/">',
                    '</a>'
                ) . '</p></div>'
            );
        }

        $settings = [];
        if (false === get_option(SRA_SETTINGS)) {
            add_option(SRA_SETTINGS, maybe_serialize($settings));
        } else {
            update_option(SRA_SETTINGS, maybe_serialize($settings));
        }
    }
}
