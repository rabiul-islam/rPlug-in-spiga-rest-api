<?php

/**
 * API call handel Blueprint
 *
 * @author  Selise digital <rb@selise.ch>
 */
trait Api_Call_Handler
{
    /**
     * Response.
     *
     * @since    1.0.0
     * @access   private
     *
     * @var array $response_data    Response.
     */
    private $response_data = [];

    /**
     * API url.
     *
     * @since    1.0.0
     * @access   private
     *
     * @var string $api    API url.
     */
    private $api = '';

    /**
     * Content type.
     *
     * @since    1.0.0
     * @access   private
     *
     * @var string $content_type    Content type.
     */
    private static $content_type = 'application/json';

    /**
     * Request type.
     *
     * @since    1.0.0
     * @access   private
     *
     * @var string $request_method    Request type.
     */
    private $request_method = 'post';

    /**
     * Token type.
     *
     * @since    1.0.0
     * @access   private
     *
     * @var string $token_type    Token type.
     */
    private $token_type = 'ecap-auth';

    /**
     * Request body arguments.
     *
     * @since    1.0.0
     * @access   private
     *
     * @var array $body_args    Request body arguments.
     */
    private $body_args = [];

    /**
     * Request execute.
     *
     * @since    1.0.0
     *
     * @return array|object
     */
    public function execute()
    {
        try {
            $headers = [
                'Content-Type' => self::$content_type,
                'Origin'       => APP_ORIGIN
            ];

            if ($auth_token_type = $this->get_token_type() != false) {
                $headers['Authorization'] = $this->get_token($auth_token_type);
            }

            $response = wp_remote_request(
                $this->get_api(),
                [
                    'method'  => strtoupper($this->get_request_method()),
                    'headers' => $headers,
                    'body'    => $this->get_body_arguments()
                ]
            );

            if (!is_wp_error($response)) {
                $response_body = json_decode(wp_remote_retrieve_body($response));
                $response_code = wp_remote_retrieve_response_code($response);

                if ($response_code >= 200 && $response_code < 300) {
                    $this->set_response($response_body);
                } else {
                    $error_messages['ok']     = false;
                    $error_messages['errors'] = ['Request Failed detected by status code: ' . $response_code];
                }
            } else {
                $error_messages['ok']     = false;
                $error_messages['errors'] = $response->get_error_messages();

                $this->set_response($error_messages);
            }
        } catch (\Exception $exception) {
            $error_messages['ok']     = false;
            $error_messages['errors'] = [$exception->getMessage()];

            $this->set_response($error_messages);
        }

        return $this->get_response();
    }

    /**
     * Get request body arguments.
     *
     * @since    1.0.0
     *
     * @return array request body arguments
     */
    private function get_body_arguments()
    {
        return $this->body_args;
    }

    /**
     * Get token type.
     *
     * @since    1.0.0
     *
     * @return string|boolean
     */
    private function get_token_type()
    {
        return $this->token_type;
    }

    /**
     * Get token.
     *
     * @since    1.0.0
     *
     * @param string $type
     *
     * @return string|boolean token
     */
    private function get_token($type)
    {
        switch ($type) {
            case 'ecap-auth':
                return Identity_Token_Management::instance()->get_token();

            default:
                return false;
        }
    }

    /**
     * Get api url.
     *
     * @since    1.0.0
     *
     * @return string Get api url
     */
    private function get_api()
    {
        return $this->api;
    }

    /**
     * Get request type.
     *
     * @since    1.0.0
     *
     * @return string Get request type
     */
    private function get_request_method()
    {
        return $this->request_method;
    }

    /**
     * Get request response.
     *
     * @since    1.0.0
     *
     * @return array|object Get request response
     */
    public function get_response()
    {
        return $this->response_data;
    }

    /**
     * Set request api.
     *
     * @since    1.0.0
     *
     * @param string $api
     *
     * @return self
     */
    public function set_api($api)
    {
        $this->api = $api;

        return $this;
    }

    /**
     * Set token.
     *
     * @since    1.0.0
     *
     * @param string|boolean $type
     *
     * @return self
     */
    public function set_token_type($type)
    {
        $this->token_type = $type;

        return $this;
    }

    /**
     * Set request type.
     *
     * @since    1.0.0
     *
     * @param string $request_method
     *
     * @return self
     */
    public function set_request_method($request_method)
    {
        $this->request_method = $request_method;

        return $this;
    }

    /**
     * Set request body args.
     *
     * @since    1.0.0
     *
     * @param array|string $body_args
     *
     * @return self
     */
    public function set_body_args($body_args)
    {
        if (is_array($body_args)) {
            $this->body_args = json_encode($body_args);
        } elseif (is_string($body_args)) {
            $this->body_args = $body_args;
        }

        return $this;
    }

    /**
     * Set request response.
     *
     * @since    1.0.0
     *
     * @param array|object $body_args
     *
     * @return void
     */
    private function set_response($response_data)
    {
        $this->response_data = $response_data;
    }
}
