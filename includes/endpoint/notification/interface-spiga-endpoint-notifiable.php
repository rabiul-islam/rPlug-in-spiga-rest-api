<?php

/**
 * Spiga event make notifiable
 */
interface Spiga_Endpoint_Notifiable
{
    /**
     * Check if a given request has access to get items
     *
     * @return void
     */
    public function notify();
}
