<?php

/**
 * Spiga notify endpoint base class
 */
abstract class Spiga_Endpoint_Notify_Base implements Spiga_Endpoint_Notifiable
{
    /**
     * API call handeler - Api_Call_Handler
     */
    use Api_Call_Handler;

    /**
     * Get notification type
     *
     * @return string
     */
    abstract public function get_notification_type();

    /**
     * Get kisok id
     *
     * @return string
     */
    abstract public function get_kisok_id();

    /**
     * Get product id
     *
     * @return array
     */
    protected function get_payload()
    {
        return [];
    }

    /**
     * Event notification send
     *
     * @return array|object
     */
    public function notify()
    {
        
        $subscription_filters                   = new \stdClass();
        $subscription_filters->ActionName       = '';
        $subscription_filters->Context          = 'SvKiosk';
        $subscription_filters->Value            = '';
        $denormalized_payload                   = new \stdClass();
        $denormalized_payload->NotificationType = $this->get_notification_type();

        foreach ($this->get_payload() as $payload_key => $payload_value) {
            $denormalized_payload->$payload_key = $payload_value;
        }

        $response_value          = new \stdClass();
        $response_value->Success = true;

        $response = $this->set_api(APP_URL_NOTIFY)
        ->set_body_args([
            'NotificationType'    => 'FilterSpecificReceiverType',
            'SubscriptionFilters' => [
                $subscription_filters
            ],
            'ResponseKey'         => 'KioskNotificationPayload',
            'DenormalizedPayload' => json_encode($denormalized_payload),
            'ResponseValue'       => json_encode($response_value)
        ])
        ->execute();
        

        error_log('Notificcation service payload: ' . json_encode([
            'NotificationType'    => 'FilterSpecificReceiverType',
            'SubscriptionFilters' => [
                $subscription_filters
            ],
            'ResponseKey'         => 'KioskNotificationPayload',
            'DenormalizedPayload' => json_encode($denormalized_payload),
            'ResponseValue'       => json_encode($response_value)
        ]));
        error_log('Notificcation service response: ' . json_encode($response));
        return $response;

    }
}
