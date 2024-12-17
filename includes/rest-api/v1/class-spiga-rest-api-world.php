<?php

/**
 * Rest API get-world
 * It shows 'Hello world' message
 */
class Spiga_Rest_API_World extends Spiga_Rest_API_Base
{
    /**
     * This is our callback function to return a single product.
     *
     * @param WP_REST_Request $request This function accepts a rest request to process data.
     */
    public function get_world_callback(WP_REST_Request $request)
    {
        $data          = new stdClass();
        $data->success = false;
        $data->status  = 400;
        $message       = 'Get quote failed.';
        $code          = 'failed';

        $content_type = $request->get_content_type();
        $body         = $request->get_body();

        if (isset($content_type['value']) && $content_type['value'] == 'application/json') {
            try {
                $body_arguments = (object) json_decode($body);

                if (
                    isset($body_arguments->Print) &&
                    $body_arguments->Print === true
                ) {
                    $message       = 'Hello world.';
                    $code          = 'success';
                    $data->success = true;
                    $data->status  = 200;
                }
            } catch (Exception $exception) {
                error_log('Get quote failed: ' . $exception->getMessage());
            }
        }

        $response = ['code' => $code, 'message' => $message, 'data' => $data];
        $response = new WP_REST_Response($response);
        $response->set_status($data->status);

        return rest_ensure_response($response);
    }

    /**
     * This function is where we register our routes for our example endpoint.
     */
    public function register_api_routes()
    {
        // [GET, POST, PUT, PATCH, DELETE] WP_REST_Server::ALLMETHODS
        // [POST, PUT, PATCH] WP_REST_Server::EDITABLE
        // [DELETE] WP_REST_Server::DELETABLE
        // [POST] WP_REST_Server::CREATABLE
        // [GET] WP_REST_Server::READABLE

        register_rest_route('wc/' . basename(__DIR__), '/get-world', [
            [
                'methods'             => WP_REST_Server::READABLE, // [GET]
                'callback'            => [$this, 'get_world_callback'],
                'permission_callback' => [$this, 'permissions_check'] // can be @override
            ]
        ]);
    }
}
