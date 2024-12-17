<?php

/**
 * Rest API get-nickname
 * It shows get-nicknames
 */
class Spiga_Rest_API_Nickname extends Spiga_Rest_API_Base
{
    /**
     * This is our callback function to return a single product.
     *
     * @param WP_REST_Request $request This function accepts a rest request to process data.
     */
    public function get_all_active_nickname(WP_REST_Request $request)
    {
        $data          = new stdClass();
        $data->success = false;
        $data->status  = 404;
        $message       = 'Nickname not found.';
        $code          = 'failed';

        $content_type = $request->get_content_type();
        $body         = $request->get_body();

        if (isset($content_type['value']) && $content_type['value'] == 'application/json') {
            try {
                $nickname_list = get_posts(
                    [
                        'numberposts' => -1,
                        'post_status' => 'publish',
                        'post_type'   => 'nickname',
                    ]
                );

                if (count($nickname_list) > 0) {

                    global $wpdb;

                    $nickname_array=[];
                    foreach ($nickname_list as $nickname) {
                    
                        $mysql_server_current_time =$wpdb->get_results("SELECT CURRENT_TIMESTAMP;");
                        $d1=$mysql_server_current_time[0]->CURRENT_TIMESTAMP;
                        $d2 =date('Y-m-d H:i:s', strtotime($d1) - (30 * 60)) ;
                        // $d2 =date('Y-m-d H:i:s', strtotime($d1) - (30)) ;
                        
                        $is_active = get_post_meta( $nickname->ID, 'is_active', true );

                        if($is_active=='no' && ($nickname->post_modified<$d2)){
                            // if you update post meta table using api request, it doesn't modify wp_post table modified column
                            // for updateing date modified time forcedly just sending a blank update request 
                            $forcedly_modified_time = wp_update_post([
                                'ID'            => $nickname->ID
                            ]);
                            if ($forcedly_modified_time) {
                                    update_post_meta(
                                    $nickname->ID,
                                    'is_active',
                                    'yes'
                                );
                            }
                        }

                        if($is_active=='yes'){
                            array_push($nickname_array, ['id'=>$nickname->ID, 'nickname'=>$nickname->post_title]);
                        }
                        
                        if (count($nickname_array) > 0) {
                            $data->status = 200;
                            $code         = 'success';
                            $message      = 'Nickname successfully found.';
                        }
                    }
                }
            } catch (Exception $exception) {
                error_log('Get nickname failed: ' . $exception->getMessage());
            }
        } else {
            $data->status = 500;
            $message      = 'Header wrong content type or missing.';
        }

        $response = empty($nickname_array) ? ['code' => $code, 'message' => $message, 'data' => $data] : $nickname_array;
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

        register_rest_route('wc/' . basename(__DIR__), '/get-nickname', [
            [
                'methods'             => WP_REST_Server::READABLE, // [GET]
                'callback'            => [$this, 'get_all_active_nickname'],
                'permission_callback' => [$this, 'permissions_check'] // can be @override
            ]
        ]);
    }
}
