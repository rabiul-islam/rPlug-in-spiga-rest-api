<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://selise.ch/
 * @since      1.0.0
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 *
 * @author     Selise Web <rafin.biswas@selise.ch>
 */
class Spiga_Rest_Api
{
    /**
     * The loader that's responsible for maintaining and registering all hooks that power
     * the plugin.
     *
     * @since    1.0.0
     * @access   protected
     *
     * @var Spiga_Rest_Api_Loader $loader    Maintains and registers all hooks for the plugin.
     */
    protected $loader;

    /**
     * The unique identifier of this plugin.
     *
     * @since    1.0.0
     * @access   protected
     *
     * @var string $plugin_name    The string used to uniquely identify this plugin.
     */
    protected $plugin_name;

    /**
     * The current version of the plugin.
     *
     * @since    1.0.0
     * @access   protected
     *
     * @var string $version    The current version of the plugin.
     */
    protected $version;

    /**
     * Define the core functionality of the plugin.
     *
     * Set the plugin name and the plugin version that can be used throughout the plugin.
     * Load the dependencies, define the locale, and set the hooks for the admin area and
     * the public-facing side of the site.
     *
     * @since    1.0.0
     */
    public function __construct()
    {
        $this->version     = SPIGA_REST_API_VERSION;
        $this->plugin_name = 'spiga-rest-api';

        $this->load_dependencies();
        $this->set_locale();
        $this->register_api_route_hooks();
        $this->register_application_hooks();
        // $this->define_assets_hooks();
    }

    /**
     * Load the required dependencies for this plugin.
     *
     * Include the following files that make up the plugin:
     *
     * - Spiga_Rest_Api_Loader. Orchestrates the hooks of the plugin.
     * - Spiga_Rest_Api_i18n. Defines internationalization functionality.
     * - Spiga_Rest_Api_Admin. Defines all hooks for the admin area.
     * - Spiga_Rest_Api_Assets. Defines all hooks for the public side of the site.
     * - Identity_Token_Management. Defines endpoint auth class.
     * - Api_Call_Handler. Defines endpoint call handler class.
     * - Spiga_Endpoint_Notifiable. Defines notifiable interface.
     * - Spiga_Endpoint_Notify_Base. Defines notification endpoint base class.
     * - Spiga_Endpoint_Notify_Product_Updated. Defines notify product updated class.
     * - Spiga_Webhook_Product_Changed. Defines webhook product changed class.
     * - Spiga_Rest_API_Base. Defines rest API base class.
     * - Spiga_Rest_API_World. Defines rest API of world.
     * - Spiga_Rest_API_variant_product. Defines rest API of varient product.
     * - Spiga_Rest_API_Nickname. Defines rest API of nickname.
     * - Spiga_Rest_API_Nickname_Deactivate. Defines rest API of nickname.
     * - Spiga_Rest_API_Add_Kiosk_Info. Defines rest API of add kiosk.
     * - Spiga_Rest_API_Get_Kiosk_Info. Defines rest API of get kiosk.
     *
     * Create an instance of the loader which will be used to register the hooks
     * with WordPress.
     *
     * @since    1.0.0
     * @access   private
     */
    private function load_dependencies()
    {
        /**
         * The class responsible for orchestrating the actions and filters of the
         * core plugin.
         */
        require_once SRA_DIR_PATH . 'includes/class-spiga-rest-api-loader.php';

        /**
         * The class responsible for defining internationalization functionality
         * of the plugin.
         */
        require_once SRA_DIR_PATH . 'includes/class-spiga-rest-api-i18n.php';

        /**
         * The class responsible for defining all actions that occur in the assets-facing
         * side of the site.
         */
        // require_once SRA_DIR_PATH . 'assets/class-spiga-rest-api-assets.php';

        /**
         * The class responsible for authenticate ecap endpoints
         * side of the site.
         */
        require_once SRA_DIR_PATH . 'includes/endpoint/auth/class-identity-token-management.php';

        /**
         * The class responsible for defining endpoints call handler
         * side of the site.
         */
        require_once SRA_DIR_PATH . 'includes/endpoint/traits/trait-api-call-handler.php';

        /**
         * The class responsible for defining notification base class
         * side of the site.
         */
        require_once SRA_DIR_PATH . 'includes/endpoint/notification/interface-spiga-endpoint-notifiable.php';

        /**
         * The class responsible for defining notification base class
         * side of the site.
         */
        require_once SRA_DIR_PATH . 'includes/endpoint/notification/class-spiga-endpoint-notify-base.php';

        /**
         * The class responsible for defining notify product updated
         * side of the site.
         */
        require_once SRA_DIR_PATH . 'includes/endpoint/notification/class-spiga-endpoint-notify-product-updated.php';

        /**
         * The class responsible for defining notify product updated
         * side of the site.
         */
        require_once SRA_DIR_PATH . 'includes/endpoint/notification/class-spiga-endpoint-notify-product-deleted.php';

        /**
         * The class responsible for defining webhook product changed
         * side of the site.
         */
        require_once SRA_DIR_PATH . 'includes/webhooks/class-spiga-webhook-product-changed.php';

        /**
         * The class responsible for defining notify category updated
         * side of the site.
         */
        require_once SRA_DIR_PATH . 'includes/endpoint/notification/class-spiga-endpoint-notify-category-updated.php';

        /**
         * The class responsible for defining notify product updated
         * side of the site.
         */
        require_once SRA_DIR_PATH . 'includes/endpoint/notification/class-spiga-endpoint-notify-category-deleted.php';

        /**
         * The class responsible for defining webhook category changed
         * side of the site.
         */
        require_once SRA_DIR_PATH . 'includes/webhooks/class-spiga-webhook-coupon-changed.php';

        /**
         * The class responsible for defining notify category updated
         * side of the site.
         */
        require_once SRA_DIR_PATH . 'includes/endpoint/notification/class-spiga-endpoint-notify-coupon-updated.php';

        /**
         * The class responsible for defining notify product updated
         * side of the site.
         */
        require_once SRA_DIR_PATH . 'includes/endpoint/notification/class-spiga-endpoint-notify-coupon-deleted.php';

        /**
         * The class responsible for defining webhook category changed
         * side of the site.
         */
        require_once SRA_DIR_PATH . 'includes/webhooks/class-spiga-webhook-category-changed.php';

        /**
         * The class responsible for defining rest API base
         * side of the site.
         */
        require_once SRA_DIR_PATH . 'includes/rest-api/class-spiga-rest-api-base.php';

        /**
         * The class responsible for defining rest API of world
         * side of the site.
         */
        require_once SRA_DIR_PATH . 'includes/rest-api/v1/class-spiga-rest-api-world.php';

        /**
         * The class responsible for defining rest API of varient product
         * side of the site.
         */
        require_once SRA_DIR_PATH . 'includes/rest-api/v1/class-spiga-rest-api-variant-product.php';

        /**
         * The class responsible for defining rest API of varient product by id
         * side of the site.
         */
        require_once SRA_DIR_PATH . 'includes/rest-api/v1/class-spiga-rest-api-variant-product-by-id.php';

        /**
         * The class responsible for defining rest API of nickname
         * side of the site.
         */
        require_once SRA_DIR_PATH . 'includes/rest-api/v1/class-spiga-rest-api-nickname.php';

        /**
         * The class responsible for defining rest API for deactivating nickname
         * side of the site.
         */
        require_once SRA_DIR_PATH . 'includes/rest-api/v1/class-spiga-rest-api-nickname-deactive.php';

        /**
         * The class responsible for defining rest API of add kiosk
         * side of the site.
         */
        require_once SRA_DIR_PATH . 'includes/rest-api/v1/class-spiga-rest-api-add-kiosk-info.php';

        /**
         * The class responsible for defining rest API of get kiosk
         * side of the site.
         */
        require_once SRA_DIR_PATH . 'includes/rest-api/v1/class-spiga-rest-api-get-kiosk-info.php';

        /**
         * The class responsible for defining rest API of testing
         * side of the site.
         */
        require_once SRA_DIR_PATH . 'includes/rest-api/v1/testing.php';

        $this->loader = new Spiga_Rest_Api_Loader();
    }

    /**
     * Define the locale for this plugin for internationalization.
     *
     * Uses the Spiga_Rest_Api_i18n class in order to set the domain and to register the hook
     * with WordPress.
     *
     * @since    1.0.0
     * @access   private
     */
    private function set_locale()
    {
        $plugin_i18n = new Spiga_Rest_Api_i18n();

        $this->loader->add_action('plugins_loaded', $plugin_i18n, 'load_plugin_textdomain');
    }

    /**
     * Register all of the hooks related to the public-facing functionality
     * of the plugin.
     *
     * @since    1.0.0
     * @access   private
     */
    private function register_api_route_hooks()
    {
        $rest_api_world = new Spiga_Rest_API_World();
        $this->loader->add_action('rest_api_init', $rest_api_world, 'register_api_routes');

        $rest_api_variant_product = new Spiga_Rest_API_variant_product();
        $this->loader->add_action('rest_api_init', $rest_api_variant_product, 'register_api_routes');

        $rest_api_variant_product_by_id = new Spiga_Rest_API_variant_product_by_id();
        $this->loader->add_action('rest_api_init', $rest_api_variant_product_by_id, 'register_api_routes');

        $rest_api_nickname = new Spiga_Rest_API_Nickname();
        $this->loader->add_action('rest_api_init', $rest_api_nickname, 'register_api_routes');

        $rest_api_nickname_deactivate = new Spiga_Rest_API_Nickname_Deactivate();
        $this->loader->add_action('rest_api_init', $rest_api_nickname_deactivate, 'register_api_routes');

        $rest_api_add_kiosk_info = new Spiga_Rest_API_Add_Kiosk_Info();
        $this->loader->add_action('rest_api_init', $rest_api_add_kiosk_info, 'register_api_routes');

        $rest_api_get_kiosk_info = new Spiga_Rest_API_Get_Kiosk_Info();
        $this->loader->add_action('rest_api_init', $rest_api_get_kiosk_info, 'register_api_routes');

        $testing = new Testing();
        $this->loader->add_action('rest_api_init', $testing, 'register_api_routes');
    }

    /**
     * Register all of the hooks related to the public-facing functionality
     * of the plugin.
     *
     * @since    1.0.0
     * @access   private
     */
    private function register_application_hooks()
    {
        $product_changed = new Spiga_Webhook_Product_Changed();
        $this->loader->add_action('save_post', $product_changed, 'product_create_or_update', 10, 3);
        // $this->loader->add_action('before_delete_post', $product_changed, 'product_deleted');
        $this->loader->add_action('wp_trash_post', $product_changed, 'product_deleted');

        

        
        $category_changed = new Spiga_Webhook_Category_Changed();
        $this->loader->add_action('create_product_cat', $category_changed, 'category_create_or_update', 10, 2);
        $this->loader->add_action('edit_product_cat', $category_changed, 'category_create_or_update', 10, 2);
        $this->loader->add_action('delete_product_cat', $category_changed, 'category_deleted', 10, 2);

        
        $coupon_changed = new Spiga_Webhook_Coupon_Changed();
        $this->loader->add_action('save_post', $coupon_changed, 'coupon_create_or_update', 10, 3);
        $this->loader->add_action('wp_trash_post', $coupon_changed, 'coupon_deleted');
    }

    /**
     * Register all of the hooks related to the public-facing functionality
     * of the plugin.
     *
     * @since    1.0.0
     * @access   private
     */
    private function define_assets_hooks()
    {
        $plugin_public = new Spiga_Rest_Api_Assets($this->get_plugin_name(), $this->get_version());

        $this->loader->add_action('wp_enqueue_scripts', $plugin_public, 'enqueue_styles');
        $this->loader->add_action('wp_enqueue_scripts', $plugin_public, 'enqueue_scripts');
    }

    /**
     * Run the loader to execute all of the hooks with WordPress.
     *
     * @since    1.0.0
     */
    public function run()
    {
        $this->loader->run();
    }

    /**
     * The name of the plugin used to uniquely identify it within the context of
     * WordPress and to define internationalization functionality.
     *
     * @since     1.0.0
     *
     * @return string The name of the plugin.
     */
    public function get_plugin_name()
    {
        return $this->plugin_name;
    }

    /**
     * The reference to the class that orchestrates the hooks with the plugin.
     *
     * @since     1.0.0
     *
     * @return Spiga_Rest_Api_Loader Orchestrates the hooks of the plugin.
     */
    public function get_loader()
    {
        return $this->loader;
    }

    /**
     * Retrieve the version number of the plugin.
     *
     * @since     1.0.0
     *
     * @return string The version number of the plugin.
     */
    public function get_version()
    {
        return $this->version;
    }
}
