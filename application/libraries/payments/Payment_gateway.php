<?php

defined('BASEPATH') or exit('No direct script access allowed');

/** A gateway refused or failed a request (message safe to show to the payer). */
class Payment_gateway_error extends RuntimeException
{
}

/**
 * One online payment gateway (Paystack, Flutterwave, Monnify, Interswitch, Remita).
 *
 * The same drivers serve school fees (keys from the school's payment_settings row) and the
 * platform's subscriptions (keys from the server environment). A driver never decides that a
 * payment is good on what the browser sends: check() asks the gateway's server.
 *
 * $order (both methods):
 *   reference    our unique reference            amount       major units (e.g. naira), float
 *   currency     ISO code, e.g. NGN               email, name, phone, description
 *   return_url   where the gateway sends the payer back (start() only)
 *   gateway_ref  what start() returned as gateway_ref, if anything (check() only)
 */
abstract class Payment_gateway
{
    /** @var array keys and mode, see each driver's KEYS */
    protected $config;

    /** Driver id (paystack, flutterwave, ...) */
    const ID = '';
    /** Human name */
    const NAME = '';
    /** Currencies the driver accepts (empty = any) */
    const CURRENCIES = array();
    /** Config keys that must not be empty */
    const REQUIRED = array();

    public function __construct(array $config)
    {
        $this->config = $config;
    }

    /** Names of the missing required settings (empty when the driver can be used). */
    public function missing()
    {
        $missing = array();
        foreach (static::REQUIRED as $key) {
            if (!isset($this->config[$key]) || trim((string) $this->config[$key]) === '') {
                $missing[] = $key;
            }
        }
        return $missing;
    }

    /** True when the configured mode is live (anything else is test/sandbox). */
    protected function live()
    {
        return !empty($this->config['live']);
    }

    /**
     * Starts a payment.
     * @return array ['redirect' => url] or ['post' => ['url' => .., 'fields' => [..]]],
     *               plus optionally 'gateway_ref' (stored, given back to check())
     * @throws Payment_gateway_error
     */
    abstract public function start(array $order);

    /**
     * Asks the gateway whether the payment went through.
     * @param array $request the parameters the payer came back with (GET and POST)
     * @return array ['paid' => bool, 'amount' => float|null (major units), 'currency' => string|null,
     *                'id' => gateway transaction id|null, 'message' => string]
     * @throws Payment_gateway_error when the gateway can't be asked
     */
    abstract public function check(array $order, array $request);

    /** Refuses currencies the gateway can't take. */
    protected function require_currency($currency)
    {
        if (static::CURRENCIES && !in_array(strtoupper((string) $currency), static::CURRENCIES, true)) {
            throw new Payment_gateway_error(static::NAME . ' only accepts ' . implode(', ', static::CURRENCIES)
                . ' payments; this school uses ' . $currency . '.');
        }
    }

    /** Amount in the smallest unit (kobo), as an integer. */
    protected static function minor($amount)
    {
        return (int) round(((float) $amount) * 100);
    }

    /**
     * HTTP request with certificate checks on.
     * @param string $method GET or POST
     * @param array $headers "Name: value" lines
     * @param array|string|null $body array = JSON, string = sent as is
     * @return array [status code, decoded JSON (or null), raw body]
     */
    protected function http($method, $url, array $headers = array(), $body = null)
    {
        $ch = curl_init($url);
        $opts = array(
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 45,
            CURLOPT_CONNECTTIMEOUT => 15,
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_SSL_VERIFYHOST => 2,
            CURLOPT_CUSTOMREQUEST => $method,
        );
        if ($body !== null) {
            if (is_array($body)) {
                $body = json_encode($body);
                $headers[] = 'Content-Type: application/json';
            }
            $opts[CURLOPT_POSTFIELDS] = $body;
        }
        $headers[] = 'Accept: application/json';
        $opts[CURLOPT_HTTPHEADER] = $headers;
        curl_setopt_array($ch, $opts);
        $raw = curl_exec($ch);
        $code = (int) curl_getinfo($ch, CURLINFO_RESPONSE_CODE);
        $error = curl_error($ch);
        curl_close($ch);
        if ($raw === false) {
            log_message('error', static::NAME . ' request failed: ' . $error . ' (' . $url . ')');
            throw new Payment_gateway_error('Could not reach ' . static::NAME . '. Please try again in a moment.');
        }
        return array($code, json_decode((string) $raw, true), (string) $raw);
    }

    /** A gateway error message for the payer, logged with the details. */
    protected function fail($what, $detail = '')
    {
        log_message('error', static::NAME . ': ' . $what . ($detail !== '' ? ' - ' . substr($detail, 0, 500) : ''));
        return new Payment_gateway_error(static::NAME . ': ' . $what);
    }
}
