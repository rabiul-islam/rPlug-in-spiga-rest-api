<?php

/**
 * Spiga rest API base class
 */
abstract class Spiga_Rest_API_Base
{
    /**
     * Check if a given request has access to get items
     *
     * @param WP_REST_Request $request Full data about the request.
     *
     * @return boolean
     */
    public function permissions_check(WP_REST_Request $request)
    {
        $auth_header = $request->get_header('Authorization');

        if (is_null($auth_header) || empty($auth_header)) {
            return false;
        }

        $auth_str           = base64_decode(trim(end(explode(' ', $auth_header))));
        list($key, $secret) = array_pad(explode(':', $auth_str), 2, '');

        if (empty($key) || empty($secret) || !function_exists('wc_api_hash')) {
            return false;
        }

        global $wpdb;
        $row = $wpdb->get_var(
            $wpdb->prepare(
                "select count(key_id) from {$wpdb->prefix}woocommerce_api_keys where consumer_key=%s AND consumer_secret=%s limit 1",
                wc_api_hash($key),
                $secret
            )
        );

        return $row == 1 ? true : false;
    }

    /**
     * Register routes
     *
     * @return void
     */
    abstract public function register_api_routes();
}
