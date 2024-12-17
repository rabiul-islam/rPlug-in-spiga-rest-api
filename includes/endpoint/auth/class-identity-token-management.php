<?php

/**
 * Authenticate all ecap endpoints
 *
 * @author  Selise digital <rb@selise.ch>
 */
final class Identity_Token_Management
{
    /**
     * Class instance.
     *
     * @since    1.0.0
     * @access   private
     *
     * @var string $instance    class instance.
     */
    private static $instance = null;

    /**
     * Access token.
     *
     * @since    1.0.0
     * @access   private
     *
     * @var string $response_data    Access token.
     */
    private $response_data = null;

    /**
     * Refresh token.
     *
     * @since    1.0.0
     * @access   private
     *
     * @var string $refresh_token    Refresh token.
     */
    private $refresh_token = null;

    /**
     * Constant of token key
     *
     * @since    1.0.0
     * @access   public
     *
     * @var string TOKEN_KEY    Token key.
     */
    const TOKEN_KEY = 'identityAuthorizedToken';

    /**
     * Constant of token key
     *
     * @since    1.0.0
     * @access   public
     *
     * @var string REFRESH_TOKEN_KEY    Refresh token key.
     */
    const REFRESH_TOKEN_KEY = 'identityAuthorizedRefreshToken';

    /**
     * Create singleton class
     *
     * @return self
     */
    public static function instance()
    {
        if (is_null(self::$instance)) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * get access token from ecap
     *
     * @param string  $grant_type    optional
     * @param string  $client_id     optional
     * @param string  $client_secret options
     * @param boolean $remember_me   options
     * @param string  $next_url      options
     *
     * @return string
     */
    private function get_access_token_form_api($grant_type = 'client_credentials', $client_id = '', $client_secret = '', $remember_me = false, $next_url = '')
    {
        $headers = [
            'Content-Type' => 'application/x-www-form-urlencoded',
            'Origin'       => APP_ORIGIN
        ];
        $body    = [];

        switch ($grant_type) {
            case 'client_credentials':
                $body['client_id']     = APP_ADMIN_USER;
                $body['client_secret'] = APP_ADMIN_PASSWORD;
                break;
            case 'password':
                $body['username']    = $client_id;
                $body['password']    = $client_secret;
                $body['remember_me'] = $remember_me;
                $body['next_url']    = '';
                break;
            default:
                $body['client_id']     = APP_ADMIN_USER;
                $body['client_secret'] = APP_ADMIN_PASSWORD;
                break;
        }

        $body['grant_type']    = $grant_type;

        $args = [
            'method' => 'POST',
            'headers'=> $headers,
            'body'   => $body
        ];

        $response_token = '';
        $refresh_token  = '';
        $response       = wp_remote_post(APP_TOKEN_URL, $args);

        if (is_array($response) && !is_wp_error($response)) {
            $response_body = (array) json_decode(wp_remote_retrieve_body($response));
            $response_code = wp_remote_retrieve_response_code($response);

            if ($response_code === 200 && !isset($response_body['error'])) {
                $response_token = strtolower($response_body['token_type']) . ' ' . $response_body['access_token'];
                $refresh_token  = $response_body['refresh_token'];
                $expiration     = current_time('timestamp', true) + $response_body['expires_in'];

                if (!wp_doing_ajax() && $grant_type != 'password') {
                    $js = ' var currentDate = new Date();
                            currentDate.setTime(currentDate.getTime() + (1000 * ' . $response_body['expires_in'] . '));

                            var expires = "expires=" + currentDate.toUTCString(),
                                cookieName = "' . SRA_ENV . '" + "' . self::TOKEN_KEY . '",
                                cookieValue= "' . $response_token . '";

                            document.cookie = cookieName + "=" + cookieValue + ";" + expires + ";path=/";';

                    wp_add_inline_script('jquery', $js);
                } elseif (wp_doing_ajax() && $grant_type != 'password') {
                    setcookie(SRA_ENV . self::TOKEN_KEY, $response_token, $expiration, '/');
                }

                delete_transient(SRA_ENV . self::TOKEN_KEY);
                set_transient(SRA_ENV . self::TOKEN_KEY, $response_token, $response_body['expires_in']);

                delete_transient(SRA_ENV . self::REFRESH_TOKEN_KEY);
                set_transient(SRA_ENV . self::REFRESH_TOKEN_KEY, $refresh_token, (($remember_me ? 14 : 2) * DAY_IN_SECONDS));
            } elseif (isset($response_body['error'])) {
                $response_token = $response_body;
            }
        }

        $this->set_token($response_token);
        $this->set_refresh_token($refresh_token);
    }

    /**
     * Get auth token
     *
     * @return string
     */
    public function get_token()
    {
        if (isset($_COOKIE[SRA_ENV . self::TOKEN_KEY]) && !empty($_COOKIE[SRA_ENV . self::TOKEN_KEY])) {
            return $_COOKIE[SRA_ENV . self::TOKEN_KEY];
        } elseif (false !== ($token = get_transient(SRA_ENV . self::TOKEN_KEY))) {
            return $token;
        }

        $this->get_access_token_form_api('password', APP_ADMIN_USER, APP_ADMIN_PASSWORD);

        return $this->response_data;
    }

    /**
     * Get auth refresh token
     *
     * @return string
     */
    public function get_refresh_token()
    {
        if (false !== ($refresh_token = get_transient(SRA_ENV . self::REFRESH_TOKEN_KEY))) {
            return $refresh_token;
        }

        return $this->refresh_token;
    }

    /**
     * get user access token
     *
     * @param string  $user_name     requred
     * @param string  $user_password requred
     * @param boolean $remember_me   options
     *
     * @return array
     */
    public function get_user_token($user_name, $user_password, $remember_me)
    {
        $this->get_access_token_form_api('password', $user_name, $user_password, $remember_me);

        return $this->get_pure_json_tokens($this->response_data, $this->get_refresh_token());
    }

    /**
     * Set auth token without token
     *
     * @param string $token requred
     *
     * @return string
     */
    private function get_pure_json_tokens($token, $refresh_token)
    {
        if (!is_string($token)) {
            return $token;
        }

        $token = explode(' ', $token);

        return json_encode(['token' => end($token), 'refresh_token' => $refresh_token]);
    }

    /**
     * Set auth token
     *
     * @param string $response_data requred
     *
     * @return void
     */
    private function set_token($response_data)
    {
        $this->response_data = $response_data;
    }

    /**
     * Set auth token
     *
     * @param string $refresh_token requred
     *
     * @return void
     */
    private function set_refresh_token($refresh_token)
    {
        $this->refresh_token = $refresh_token;
    }
}
