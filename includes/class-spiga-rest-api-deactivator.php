<?php

/**
 * Fired during plugin deactivation
 *
 * @link       https://selise.ch/
 * @since      1.0.0
 */

/**
 * Fired during plugin deactivation.
 *
 * This class defines all code necessary to run during the plugin's deactivation.
 *
 * @since      1.0.0
 *
 * @author     Selise Web <rafin.biswas@selise.ch>
 */
class Spiga_Rest_Api_Deactivator
{
    /**
     * Short Description. (use period)
     *
     * Long Description.
     *
     * @since    1.0.0
     */
    public static function deactivate()
    {
        delete_option(SRA_SETTINGS);
    }
}
