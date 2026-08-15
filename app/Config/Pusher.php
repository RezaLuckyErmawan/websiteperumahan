<?php

namespace Config;

/**
 * Pusher Configuration
 *
 * Real-time messaging service for chat functionality
 */
class Pusher
{
    /**
     * Pusher configuration array
     */
    public array $config = [
        'app_id' => env('PUSHER_APP_ID', ''),
        'key' => env('PUSHER_KEY', ''),
        'secret' => env('PUSHER_SECRET', ''),
        'cluster' => env('PUSHER_CLUSTER', 'mt1'),
        'use_tls' => true,

        // Optional encryption
        'encryption_master_key' => env('PUSHER_ENCRYPTION_KEY', ''),

        // Timeout settings
        'timeout' => 30,

        // Notification host (optional)
        'notification_host' => env('PUSHER_NOTIFICATION_HOST', ''),

        // Debug mode (set to true in development)
        'debug' => env('PUSHER_DEBUG', false),
    ];

    /**
     * Get Pusher configuration for a specific chat type
     *
     * @param string $type Type of chat: 'customer', 'booking', 'internal'
     * @return array Configuration array
     */
    public function getChatConfig(string $type = 'customer'): array
    {
        $config = $this->config;

        // You can customize per chat type if needed
        switch ($type) {
            case 'customer':
                // Customer support chat settings
                break;
            case 'booking':
                // Booking-specific chat settings
                break;
            case 'internal':
                // Internal team chat settings
                break;
        }

        return $config;
    }

    /**
     * Get channel name for specific chat room
     *
     * @param string $roomType Type: 'customer-support', 'booking', 'internal'
     * @param int|string $identifier Customer ID, booking ID, or team name
     * @return string Channel name
     */
    public function getChannelName(string $roomType, $identifier): string
    {
        return "chat-{$roomType}-{$identifier}";
    }

    /**
     * Get event name for different message types
     *
     * @param string $eventType Type: 'new-message', 'typing', 'read-receipt'
     * @return string Event name
     */
    public function getEventName(string $eventType): string
    {
        $events = [
            'new-message' => 'App\\Events\\NewMessage',
            'typing' => 'App\\Events\\TypingIndicator',
            'read-receipt' => 'App\\Events\\ReadReceipt',
        ];

        return $events[$eventType] ?? 'App\\Events\\MessageEvent';
    }
}
