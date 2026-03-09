<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Notifier Library
 *
 * Modular notification sender. Currently supports Telegram.
 * Add send_whatsapp(), send_slack(), send_email() etc. as needed
 * without touching the core polling logic.
 */
class Notifier {

    protected $CI;
    protected $telegram_enabled;
    protected $telegram_bot_token;
    protected $telegram_chat_id;

    public function __construct()
    {
        $this->CI =& get_instance();
        $this->telegram_enabled   = $this->CI->config->item('telegram_enabled');
        $this->telegram_bot_token = $this->CI->config->item('telegram_bot_token');
        $this->telegram_chat_id   = $this->CI->config->item('telegram_chat_id');
    }

    /**
     * Send notification via all enabled channels.
     */
    public function send($message)
    {
        if ($this->telegram_enabled) {
            $this->send_telegram($message);
        }

        // Future channels:
        // if ($this->CI->config->item('slack_enabled')) $this->send_slack($message);
        // if ($this->CI->config->item('whatsapp_enabled')) $this->send_whatsapp($message);
    }

    /**
     * Send message via Telegram Bot API.
     */
    public function send_telegram($message)
    {
        if (empty($this->telegram_bot_token) || empty($this->telegram_chat_id)) {
            log_message('error', 'Notifier: Telegram bot_token atau chat_id belum dikonfigurasi.');
            return FALSE;
        }

        $url = "https://api.telegram.org/bot{$this->telegram_bot_token}/sendMessage";

        $post_data = [
            'chat_id'    => $this->telegram_chat_id,
            'text'       => $message,
            'parse_mode' => 'HTML',
        ];

        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL            => $url,
            CURLOPT_POST           => TRUE,
            CURLOPT_POSTFIELDS     => $post_data,
            CURLOPT_RETURNTRANSFER => TRUE,
            CURLOPT_TIMEOUT        => 10,
            CURLOPT_SSL_VERIFYPEER => TRUE,
        ]);

        $response = curl_exec($ch);
        $error    = curl_error($ch);
        curl_close($ch);

        if ($error) {
            log_message('error', 'Notifier Telegram error: ' . $error);
            return FALSE;
        }

        $result = json_decode($response, TRUE);
        if ( ! isset($result['ok']) || ! $result['ok']) {
            log_message('error', 'Notifier Telegram API error: ' . $response);
            return FALSE;
        }

        return TRUE;
    }
}
