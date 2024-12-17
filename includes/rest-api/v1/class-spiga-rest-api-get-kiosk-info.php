<?php

/**
 * Rest API get-nickname
 * It shows get-nicknames
 */
class Spiga_Rest_API_Get_Kiosk_Info extends Spiga_Rest_API_Base
{
    /**
     * This is our callback function to return a single product.
     *
     * @param WP_REST_Request $request This function accepts a rest request to process data.
     */
    public function get_all_active_kiosk(WP_REST_Request $request)
    {
        $data          = new stdClass();
        $data->success = false;
        $data->status  = 404;
        $message       = 'Kiosk not found.';
        $code          = 'failed';

        $content_type = $request->get_content_type();
        $body         = $request->get_body();

        if (isset($content_type['value']) && $content_type['value'] == 'application/json') {
            try {
                $kiosk_list = get_posts(
                    [
                        'numberposts' => -1,
                        'post_status' => array('publish', 'draft'),
                        'post_type'   => 'kiosk',
                    ]
                );

                if (count($kiosk_list) > 0) {
                    $data->status = 200;
                    $code         = 'success';
                    $message      = 'Kiosk successfully found.';

                    $kiosk_array=[];
                    foreach ($kiosk_list as $kiosk) {
                        $status=false;
                        if($kiosk->post_status=='publish'){
                            $status=true;
                        }
                        array_push($kiosk_array, ['id'=>$kiosk->ID, 'name'=>$kiosk->post_title,'description'=>$kiosk->post_content,'is_available'=>$status]);
                    }
                }
            } catch (Exception $exception) {
                error_log('Get kiosk failed: ' . $exception->getMessage());
            }
        } else {
            $data->status = 500;
            $message      = 'Header wrong content type or missing.';
        }

        $response = empty($kiosk_array) ? ['code' => $code, 'message' => $message, 'data' => $data] : $kiosk_array;
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

        register_rest_route('wc/' . basename(__DIR__), '/get-kiosk-info', [
            [
                'methods'             => WP_REST_Server::READABLE, // [GET]
                'callback'            => [$this, 'get_all_active_kiosk'],
                'permission_callback' => [$this, 'permissions_check'] // can be @override
            ]
        ]);
    }
}
