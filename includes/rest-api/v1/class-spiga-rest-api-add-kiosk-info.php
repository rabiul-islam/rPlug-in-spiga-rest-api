<?php

/**
 * Rest API /add-kiosk-info
 * It add kiosk information
 */
class Spiga_Rest_API_Add_Kiosk_Info extends Spiga_Rest_API_Base
{
    /**
     * This is our callback function to return a single product.
     *
     * @param WP_REST_Request $request This function accepts a rest request to process data.
     */
    public function add_kiosk_info(WP_REST_Request $request)
    {
        $data          = new stdClass();
        $data->success = false;
        $data->status  = 404;
        $message       = 'failed.';
        $code          = 'failed';

        $content_type = $request->get_content_type();
        $body         = $request->get_body();

        if (isset($content_type['value']) && $content_type['value'] == 'application/json') {
            try {
                
                $body_arguments = (object) json_decode($body);
                if ( isset($body_arguments->name) && isset($body_arguments->ip_address) && isset($body_arguments->mac_address) && isset($body_arguments->unique_id) ) {
                    $my_post = [
                        'post_title'  => $body_arguments->name,
                        'post_type'   => 'kiosk',
                        'post_status' => 'publish',
                        'meta_input'  => [
                            'ip_address'  => $body_arguments->ip_address,
                            'mac_address' => $body_arguments->mac_address,
                            'unique_id' => $body_arguments->unique_id
                        ]
                    ];
                    global $wpdb;
                    $metas = $wpdb->get_results( 
                        $wpdb->prepare("SELECT * FROM $wpdb->postmeta where meta_key = %s", 'unique_id')
                       );

                       $mac_address_found=false;
                       $meta_post_id=0;
                       if( count($metas) > 0 ){ 
                        foreach ($metas as $meta){
                            if($meta->meta_value ==$body_arguments->unique_id){
                                $mac_address_found=true;
                                $meta_post_id=$meta->post_id;
                            }
                          }
                      }

                      if($mac_address_found){
                        $post_id      = $meta_post_id;
                        $code         = 'failed';
                        $message      = 'Kiosk already exists.';
                      }
                      else{
                        $post_id = wp_insert_post($my_post);
                        if (count($post_id) > 0) {
                            $data->status = 200;
                            $code         = 'success';
                            $message      = 'Kiosk info successfully saved.';
                        }
                      }
                    
                }
            } catch (Exception $exception) {
                error_log('failed: ' . $exception->getMessage());
            }
        } else {
            $data->status = 500;
            $message      = 'Header wrong content type or missing.';
        }

        $response = empty($post_id) ? ['code' => $code, 'message' => $message, 'data' => $data] : (object)array("KioskId"=>(int)$post_id);
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

        register_rest_route('wc/' . basename(__DIR__), '/add-kiosk-info', [
            [
                'methods'             => WP_REST_Server::EDITABLE, // [POST]
                'callback'            => [$this, 'add_kiosk_info'],
                'permission_callback' => [$this, 'permissions_check'] // can be @override
            ]
        ]);
    }
}
