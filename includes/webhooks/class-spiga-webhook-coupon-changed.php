<?php

/**
 * Spiga webhook product changed
 */
class Spiga_Webhook_Coupon_Changed
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
    public function coupon_create_or_update($post_id, $post, $update)
    {
        if ($post->post_status == 'publish' && $post->post_type == 'shop_coupon') {
           
        

        $exclude_me = apply_filters('spiga_exclude_current_coupon', false, $post_id, $post);

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
                $response = (new Spiga_Endpoint_Notify_Coupon_Updated($post_id, $kiosk_id))->notify();
                // var_dump($response);
                // die();
            }
            wp_reset_postdata();
        }
      }
    }

    public function coupon_deleted($post_id)
    {
        $post = get_post( $post_id );
       
        if ($post->post_status == 'publish' && $post->post_type == 'shop_coupon') {

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
                $response = (new Spiga_Endpoint_Notify_Coupon_Deleted($post_id, $kiosk_id))->notify();
                // var_dump($response);
                // die();
            }
            wp_reset_postdata();
        }
      }
    }
}
