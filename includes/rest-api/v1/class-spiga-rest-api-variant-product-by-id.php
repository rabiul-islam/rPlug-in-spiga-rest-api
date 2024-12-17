<?php

/**
 * Rest API /variant/product
 * It shows variant products
 */
class Spiga_Rest_API_variant_product_by_id extends Spiga_Rest_API_Base
{
    /**
     * This is our callback function to return a single product.
     *
     * @param WP_REST_Request $request This function accepts a rest request to process data.
     */
    public function get_varient_product(WP_REST_Request $request)
    {
        $data          = new stdClass();
        $data->success = false;
        $data->status  = 404;
        $message       = 'Invalid ID.';
        $code          = 'failed';
        $product  = [];

        $content_type = $request->get_content_type();
        $body         = $request->get_params();

        if (isset($content_type['value']) && $content_type['value'] == 'application/json') {
            try {
                $auth_header = $request->get_header('Authorization');

                if (is_null($auth_header) || empty($auth_header)) {
                    return false;
                }

                $auth_str                             = base64_decode(trim(end(explode(' ', $auth_header))));
                list($consumer_key, $consumer_secret) = array_pad(explode(':', $auth_str), 2, '');

                $get_product_from_api = wp_remote_get(SRA_ORIGIN . '/wp-json/wc/v3/products/'.$body['id'].'?consumer_key=' . $consumer_key . '&consumer_secret=' . $consumer_secret);

                if (empty($get_product_from_api)) {
                    $response = ['code' => $code, 'message' => $message, 'data' => $data];
                    $response = new WP_REST_Response($response);
                    $response->set_status($data->status);

                    return rest_ensure_response($response);
                }

                $product = json_decode($get_product_from_api['body']);

                if ($product->id > 0 && $product->status=='publish') {
                    $data->status = 200;
                    $code         = 'success';
                    $message      = 'Product successfully found.';


                        $translation=pll_get_post_translations($product->id);
                    
                       $addons_array=[];
                        if ($product->variations) {
                            $variation_arrays=[]; 
                            $get_available_variations_array= [];
                            foreach (wc_get_product($product->id)->get_available_variations() as $available_variation) {   

                                foreach (wc_get_product($available_variation['variation_id'])->get_variation_attributes() as $vkey => $attr) { 
                                    //check by name    
                                    $variation_price = wc_get_product($available_variation['variation_id'])->get_price(); 
                                    array_push($variation_arrays, ['id'=>$available_variation['variation_id'], $vkey=>$attr, 'price'=>$variation_price]);
                                }    

                        }  

                           /* $variation_array=[];
                            foreach ($product->variations as $variation) {
                                $variation_price = wc_get_product($variation)->get_price();
                                $attribute_name  = wc_get_product($variation)->get_attributes();
                                array_push($variation_array, ['id'=>$variation, 'size' => $attribute_name['size'], 'color' => $attribute_name['color'],'price'=>$variation_price]);

                            }
                            $product->variations=$variation_array;*/



                            $variation_array =[];
                                foreach(wc_get_product($product->id)->get_available_variations() as $variation){
                                  

                                    $attribute_name  = wc_get_product($product->id)->get_attributes(); 


                                    $p_attributes = [];
                                    
                                    $p_attributes['id'] = $variation['variation_id'];
                                    $p_attributes['price'] = $variation['display_regular_price'];

                                    foreach($attribute_name as $key=>$name){  
                                          
                                          $attr_name = strtolower($name['name']);   
                                          $attr_value = $variation['attributes']['attribute_'.$attr_name];                     
                                          $p_attributes[$attr_name] = $attr_value;                                         
                                    }                                    
                                    
                                     array_push($variation_array, $p_attributes); 
                                  // array_push($variation_array, $p_attributes);  
                                  //$variation_array = array_merge($variation_array,array($p_attributes)); 
                                } 

                                $product->variations=$variation_array;


                        }

                        if ($product->meta_data) {
                            foreach ($product->meta_data as $meta_data) {
                                if($meta_data->key=="is_suggested"){
                                    $product->is_suggested=$meta_data->value;
                                }
                                if($meta_data->key=="display_order"){
                                    $product->display_order=$meta_data->value;
                                }
                            }
                        }

                        $translations=pll_get_post_translations($product->id);
                        if (count($translations)>0) {
                            $translation_array=[];
                            foreach ($translations as $tkey => $trans) {
                                array_push($translation_array, ['id'=>$trans, 'name'=>$tkey]);
                            }
                            $product->translations=$translation_array;
                        }

                        

                        
                    //     $addons_list        = pewc_get_extra_fields($product->id);
                    //     if (count($addons_list) > 0) {
                    //         foreach ($addons_list as $key => $addons) {
                    //             if($addons["items"]){
                    //                 foreach ($addons["items"] as $key => $items){
                    //                     if($items["id"]){
                    //                         array_push($addons_array,[
                    //                             'id'=>$items["id"], 
                    //                             'field_id'=>$items["field_id"], 
                    //                             'group_id'=>$items["group_id"],
                    //                             'field_label'=>$items["field_label"], 
                    //                             'field_type'=>$items["field_type"], 
                    //                             'field_options'=>$items["field_options"],
                    //                             'field_minchecks'=>$items["field_minchecks"], 
                    //                             'field_maxchecks'=>$items["field_maxchecks"]
                    //                             ]);
                    //                     }
                    //                 }
                    //             }
                    //         }
                    //     }
                    //    $product->addons=$addons_array;


                    /*------Robi custom Code for Default Attributes ------*/ 
                    if ($product->variations) { 
                        $default_attributes_arrays = wc_get_product($product->id)->get_default_attributes(); 
                        $default_attributes=[];
                        //get default attr name
                        foreach ($default_attributes_arrays as $vkey => $list) {   
                            //get all available variation
                            array_push($default_attributes, ['attribute_name'=>$vkey, 'variation_name'=>$list ]);   

                        }   
                        $product->default_attributes = $default_attributes;
                    }//if isset variations

                    
                     //get product tags by product id 
                     $product_tag_terms = get_terms( array('hide_empty' => false,'taxonomy' => 'product_tag',));  

                    foreach($product_tag_terms as $tags){  
                        $obj = wc_get_product($product->id)->get_data();  
                        $product_tags_array = [];
                        foreach($obj['tag_ids'] as $tags_id){  
                            
                            if($tags->term_id == $tags_id){                                 
                                $meta       = get_term_meta($tags_id);                       
                                $thumb_id   = $meta['tag_icon'][0];  
                                $icon       = wp_get_attachment_image_url( $thumb_id, 'medium' ); 
                               $tags_name =  $tags->name;
                               $tags_slug =  $tags->slug;
                            }
                        array_push($product_tags_array, ['id'=> $tags_id, 'name'=>$tags_name, 'slug'=>$tags_slug, 'icon'=>$icon]);                             
                        }
                    }  
                     $product->tags = $product_tags_array;
               
                    /*------Robi custom Code for Default Attributes ------*/


                        $final_product=(object)array(
                            'id'=>$product->id, 
                            'name'=>$product->name, 
                            'date_created'=>$product->date_created,
                            'date_modified'=>$product->date_modified, 
                            'type'=>$product->type, 
                            'status'=>$product->status,
                            'description'=>$product->description, 
                            'short_description'=>$product->short_description,
                            'price'=>$product->price, 
                            'purchasable'=>$product->purchasable,
                            'tax_status'=>$product->tax_status, 
                            'tax_class'=>$product->tax_class, 
                            'stock_status'=>$product->stock_status, 
                            'categories'=>$product->categories,
                            'tags'=>$product->tags, 
                            //'tag_icon'=>$product->tags, 
                            'images'=>$product->images,  
                            'variations'=>$product->variations,  
                            'default_attributes'=>$product->default_attributes, 
                            'is_suggested'=>$product->is_suggested,
                            'display_order'=>$product->display_order,
                            'translations'=>$product->translations
                            // 'addons'=>$product->addons
                        );

                    
                }
               
            } catch (Exception $exception) {
                error_log('Get varient product failed: ' . $exception->getMessage());
            }
        } else {
            $data->status = 500;
            $message      = 'Header wrong content type or missing.';
        }

        $response = empty($product->id && $product->status=='publish') ? ['code' => $code, 'message' => $message, 'data' => $data] : $final_product;
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

        register_rest_route('wc/' . basename(__DIR__), '/variant/product/(?P<id>\d+)', [
            [
                'methods'             => WP_REST_Server::READABLE, // [GET]
                'callback'            => [$this, 'get_varient_product'],
                'permission_callback' => [$this, 'permissions_check'] // can be @override
            ]
        ]);
    }
}
