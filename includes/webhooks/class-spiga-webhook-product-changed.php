<?php

/**
 * Spiga webhook product changed
 */
class Spiga_Webhook_Product_Changed
{
    /**
     * Product create or update
     *
     * @param integer $post_id
     * @param WP_Post $post
     * @param boolean $update
     *
     * @return void
     */
    public function product_create_or_update($post_id, $post, $update)
    {
        if ($post->post_status == 'publish' && $post->post_type == 'product') {
           

        $exclude_me = apply_filters('spiga_exclude_current_product', false, $post_id, $post);

        // if ($exclude_me || !$product = wc_get_product($post)) {
        //     return;
        // }

        // Notify kiosk
        $exclude_kiosk_ids = apply_filters('spiga_exclude_kiosk_ids', []);
        $args              = [
            'post_type'      => 'kiosk',
            'post_status'    => 'publish',
            'posts_per_page' => -1,
            'post__not_in'   => $exclude_kiosk_ids
        ];
        $kiosks = new WP_Query($args);

        if ($kiosks->have_posts()) {
            while ($kiosks->have_posts()) {
                $kiosks->the_post();

                $kiosk_id = get_the_ID();
                $kiosk_id = get_post_meta($kiosk_id, 'unique_id', true);
                $response = (new Spiga_Endpoint_Notify_Product_Updated($post_id, $kiosk_id))->notify();
                // var_dump($response);
                // die();
            }
            wp_reset_postdata();
        }
      }
    }

    /**
     * Product deleted
     *
     * @param integer $post_id
     *
     * @return void
     */
    public function product_deleted($post_id)
    {
        $post = get_post( $post_id );
       
        if ($post->post_status == 'publish' && $post->post_type == 'product') {

        // Notify kiosk
        $exclude_kiosk_ids = apply_filters('spiga_exclude_kiosk_ids', []);
        $args              = [
            'post_type'      => 'kiosk',
            'post_status'    => 'publish',
            'posts_per_page' => -1,
            'post__not_in'   => $exclude_kiosk_ids
        ];
        $kiosks = new WP_Query($args);

        if ($kiosks->have_posts()) {
            while ($kiosks->have_posts()) {
                $kiosks->the_post();

                $kiosk_id = get_the_ID();
                $kiosk_id = get_post_meta($kiosk_id, 'unique_id', true);
                $response = (new Spiga_Endpoint_Notify_Product_Deleted($post_id, $kiosk_id))->notify();
                // var_dump($response);
                // die();
            }

            //die();
            wp_reset_postdata();
        }
      }
    }
}
