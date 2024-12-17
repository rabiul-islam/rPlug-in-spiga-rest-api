<?php

/**
 * Spiga notify endpoint product updated
 */
class Spiga_Endpoint_Notify_Product_Deleted extends Spiga_Endpoint_Notify_Base
{
    /**
     * Product id
     *
     * @since    1.0.0
     * @access   private
     *
     * @var int $product_id
     */
    private $product_id;

    /**
     * Kisok id
     *
     * @since    1.0.0
     * @access   private
     *
     * @var string
     */
    private $kisok_id;

    /**
     * Event notification type
     *
     * @since    1.0.0
     * @access   public
     *
     * @var string
     */
    const NOTIFICATION_TYPE = 'FoodItemChange';

    /**
     * Class construct or initalizer
     *
     * @return void
     */
    public function __construct($product_id, $kisok_id)
    {
        $this->product_id = $product_id;
        $this->kisok_id   = $kisok_id;
    }

    /**
     * Get notification type
     *
     * @return string
     */
    public function get_notification_type()
    {
        return self::NOTIFICATION_TYPE;
    }

    /**
     * Get product id
     *
     * @return integer
     */
    public function get_payload()
    {
        return ['ItemIds' => [$this->product_id],'IsDeleted' => true];
    }

    /**
     * Get kisok id
     *
     * @return string
     */
    public function get_kisok_id()
    {
        return $this->kisok_id;
    }
}
