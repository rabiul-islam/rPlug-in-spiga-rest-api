<?php

/**
 * Spiga webhook product changed
 */
class Spiga_Webhook_Category_Changed
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
    public function category_create_or_update($term_id, $tt_id)
    {
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
                $response = (new Spiga_Endpoint_Notify_Category_Updated($term_id, $kiosk_id))->notify();
                // var_dump($response);
                // die();
            }
            wp_reset_postdata();
        }
    }

    public function category_deleted($term_id, $tt_id)
    {
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
                $response = (new Spiga_Endpoint_Notify_Category_Deleted($term_id, $kiosk_id))->notify();
                // var_dump($response);
                // die();
            }
            wp_reset_postdata();
        }
    }
}
