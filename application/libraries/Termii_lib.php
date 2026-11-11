<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Termii (Nigerian SMS provider, https://termii.com): one message to one number.
 *
 *   channel  "dnd"     delivers to numbers on the Do-Not-Disturb list (transactional route; needs an
 *                      approved sender ID). Use this for school messages.
 *            "generic" promotional route; does not reach DND numbers.
 *
 * Numbers are sent in international form without "+": 2348031234567 (Smsgateway::phone()).
 */
class Termii_lib
{
    const URL = 'https://api.ng.termii.com/api/sms/send';

    private $api_key;
    private $sender_id;
    private $channel;

    public function __construct($params = array())
    {
        $this->api_key = isset($params['api_key']) ? (string) $params['api_key'] : '';
        $this->sender_id = isset($params['sender_id']) ? (string) $params['sender_id'] : '';
        $this->channel = isset($params['channel']) && in_array($params['channel'], array('dnd', 'generic'), true) ? $params['channel'] : 'dnd';
    }

    /** @return bool true when Termii accepted the message */
    public function sendSMS($to, $message)
    {
        $body = json_encode(array(
            'api_key' => $this->api_key,
            'to' => ltrim((string) $to, '+'),
            'from' => $this->sender_id,
            'sms' => (string) $message,
            'type' => 'plain',
            'channel' => $this->channel,
        ));
        $ch = curl_init(self::URL);
        curl_setopt_array($ch, array(
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $body,
            CURLOPT_HTTPHEADER => array('Content-Type: application/json', 'Accept: application/json'),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 20,
        ));
        $response = curl_exec($ch);
        $status = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);
        $data = is_string($response) ? json_decode($response, true) : null;
        $ok = $status >= 200 && $status < 300 && is_array($data) && !empty($data['message_id']);
        if (!$ok) {
            log_message('error', 'Termii SMS to ' . $to . ' failed: HTTP ' . $status . ' ' . ($error ?: substr((string) $response, 0, 300)));
        }
        return $ok;
    }
}
