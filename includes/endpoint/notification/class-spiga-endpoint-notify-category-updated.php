<?php

/**
 * Spiga notify endpoint product updated
 */
class Spiga_Endpoint_Notify_Category_Updated extends Spiga_Endpoint_Notify_Base
{
    /**
     * Product id
     *
     * @since    1.0.0
     * @access   private
     *
     * @var int $category_id
     */
    private $category_id;

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
    const NOTIFICATION_TYPE = 'FoodCategoryChange';

    /**
     * Class construct or initalizer
     *
     * @return void
     */
    public function __construct($category_id, $kisok_id)
    {
        $this->category_id = $category_id;
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
        return ['ItemIds' => [$this->category_id],'IsDeleted' => false];
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
