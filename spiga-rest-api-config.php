<?php
/**
 * Define all config here
 */

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
if (!defined('SPIGA_REST_API_VERSION')) {
    // Staging
    define('SPIGA_REST_API_VERSION', '1.0.0');

    // Production
    // define('SPIGA_REST_API_VERSION', '1.0.0');
}

/**
 * Environment settings
 */
if (!defined('SRA_ENV')) {
    // Staging
    // define('SRA_ENV', 'stg-'); 
   

     //local 
    define('SRA_ENV', '');

    // Production
    // define('SRA_ENV', '');
}

/**
 * Set rest API origin
 */
if (!defined('SRA_ORIGIN')) {
    // Staging
    define('SRA_ORIGIN', 'https://stg.cms.spiga-ristorante.ch/spiga');
    //https://sv-cms.selisead.ch/spiga

    // Production
    // define('SRA_ORIGIN', site_url());
}

/**
 * App endpoint origin
 */
if (!defined('APP_ORIGIN')) {
    // Staging
    //define('APP_ORIGIN', 'https://stg-spiga.selise.biz');  
   
    // Local
    define('APP_ORIGIN', 'http://spiga.seliselocal.com');
 
    // Production
      //define('APP_ORIGIN', 'https://spiga-bs.selise.biz');
}

/**
 * App endpoint origin
 */
if (!defined('APP_URL_NOTIFY')) {
    // Staging
     //define('APP_URL_NOTIFY', 'https://' . SRA_ENV . 'microservices.selise.biz/api/notification/v2/api/Notifier/Notify');

    // Local
    define('APP_URL_NOTIFY', 'http://' . SRA_ENV . 'microservices.seliselocal.com/api/notification/v2/api/Notifier/Notify');

    // Production
    //define('APP_URL_NOTIFY', 'https://' . SRA_ENV . 'microservices.selise.biz/api/notification/v1/api/Notifier/Notify');
}

/**
 * App token endpoint
 */
if (!defined('APP_TOKEN_URL')) {
    // Staging
    //define('APP_TOKEN_URL', 'https://' . SRA_ENV . 'microservices.selise.biz/api/identity/v7/identity/token');

    // Local
      define('APP_TOKEN_URL', 'http://' . SRA_ENV . 'microservices.seliselocal.com/api/identity/v20/identity/token');

    // Production
    // define('APP_TOKEN_URL', 'https://' . SRA_ENV . 'microservices.selise.biz/api/identity/v1/identity/token');
}

/**
 * App endpoint admin user
 */
if (!defined('APP_ADMIN_USER')) {
    // Staging
    // define('APP_ADMIN_USER', 'wk@selise.ch');
    define('APP_ADMIN_USER', 'arc@selise.ch');

    // Production
    // define('APP_ADMIN_USER', 'wk@selise.ch');
}

/**
 * App endpoint admin password
 */
if (!defined('APP_ADMIN_PASSWORD')) {
    // Staging
    define('APP_ADMIN_PASSWORD', '1qazZAQ!');

    // Production
    // define('APP_ADMIN_PASSWORD', '1qazZAQ!');
}

/**
 * Plugin dir path & dir url
 */
if (!defined('SRA_DIR_PATH')) {
    define('SRA_DIR_PATH', plugin_dir_path(__FILE__));
}
if (!defined('SRA_DIR_URL')) {
    define('SRA_DIR_URL', plugin_dir_url(__FILE__));
}

/**
 * Set default language
 */
if (!defined('SRA_DEFAULT_LANG')) {
    define('SRA_DEFAULT_LANG', 'en');
}

/**
 * Plugin text-domain.
 */
if (!defined('SRA_TEXTDOMAIN')) {
    define('SRA_TEXTDOMAIN', 'spiga-rest-api');
}

if (!defined('SRA_SETTINGS')) {
    define('SRA_SETTINGS', 'spiga_rest_api_settings');
}

/**
 * Plugin upload path & url.
 */
// if (!defined('BS_UPLOAD_PATH') && !defined('BS_UPLOAD_URL')) {
//     $upload_dir = wp_get_upload_dir();

//     define('BS_UPLOAD_PATH', $upload_dir['basedir'] . '/' . SRA_TEXTDOMAIN . '/');
//     define('BS_UPLOAD_URL', $upload_dir['baseurl'] . '/' . SRA_TEXTDOMAIN . '/');
// }