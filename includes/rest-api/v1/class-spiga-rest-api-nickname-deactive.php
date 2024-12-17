<?php

/**
 * Rest API nickname-deactivate/<id>
 * It deactivate nickname
 */
class Spiga_Rest_API_Nickname_Deactivate extends Spiga_Rest_API_Base
{
    /**
     * This is our callback function to return a single product.
     *
     * @param WP_REST_Request $request This function accepts a rest request to process data.
     */
    public function set_nickname_deactive(WP_REST_Request $request)
    {
        $data          = new stdClass();
        $data->success = false;
        $data->status  = 404;
        $message       = 'Nickname deactivation failed.';
        $code          = 'failed';

        $content_type = $request->get_content_type();
        $body         = $request->get_params();

        if (isset($content_type['value']) && $content_type['value'] == 'application/json') {
            try {
                if ($body['id']) {
                    // if you update post meta table using api request, it doesn't modify wp_post table modified column
                    // for updateing date modified time forcedly just sending a blank update request   
                    
                    $is_active = get_post_meta( $body['id'], 'is_active', true );
                    if($is_active=='no'){
                        $data->status = 404;
                        $code         = 'failed';
                        $message      = 'This Nickname already deactive.';
                    }  
                    else{              
                        $forcedly_modified_time = wp_update_post([
                            'ID'            => $body['id']
                        ]);
                        
                        if ($forcedly_modified_time) {
                            $meta_id = update_post_meta(
                                $body['id'],
                                'is_active',
                                'no'
                            );
                            if ($meta_id) {
                                $data->status = 200;
                                $code         = 'success';
                                $message      = 'Nickname successfully deactivated.';
                            }
                        }
                    }
                }
            } catch (Exception $exception) {
                error_log('Nickname deactivation failed: ' . $exception->getMessage());
            }
        } else {
            $data->status = 500;
            $message      = 'Header wrong content type or missing.';
        }

        $response = empty($meta_id) ? ['code' => $code, 'message' => $message, 'data' => $data] : (int)$body['id'];
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

        register_rest_route('wc/' . basename(__DIR__), '/nickname-deactivate/(?P<id>\d+)', [
            [
                'methods'             => WP_REST_Server::EDITABLE, // [POST]
                'callback'            => [$this, 'set_nickname_deactive'],
                'permission_callback' => [$this, 'permissions_check'] // can be @override
            ]
        ]);
    }
}
