<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Paystack (https://paystack.com). Keys: secret_key (sk_test_... / sk_live_...).
 * Test or live is decided by the key itself.
 *
 * Subaccount mode (config subaccount = ACCT_...): the platform's key, the payment settles to the
 * school's bank account; Paystack's fee is charged to the subaccount (bearer = subaccount).
 *
 * Flow: POST /transaction/initialize → authorization_url; Paystack sends the payer back to
 * return_url?reference=...; GET /transaction/verify/{reference}.
 */
class Gateway_paystack extends Payment_gateway
{
    const ID = 'paystack';
    const NAME = 'Paystack';
    const CURRENCIES = array('NGN', 'GHS', 'ZAR', 'KES', 'USD', 'XOF', 'EGP');
    const REQUIRED = array('secret_key');
    const API = 'https://api.paystack.co';

    public function start(array $order)
    {
        $this->require_currency($order['currency']);
        list($code, $json, $raw) = $this->http('POST', self::API . '/transaction/initialize', $this->auth(), array(
            'email' => $order['email'],
            'amount' => self::minor($order['amount']),
            'currency' => strtoupper($order['currency']),
            'reference' => $order['reference'],
            'callback_url' => $order['return_url'],
            'metadata' => array('custom_fields' => array(array(
                'display_name' => 'Payment for', 'variable_name' => 'description', 'value' => (string) $order['description'],
            ))),
        ) + (empty($this->config['subaccount']) ? array() : array(
            'subaccount' => $this->config['subaccount'],
            'bearer' => 'subaccount',
        )));
        if (empty($json['status']) || empty($json['data']['authorization_url'])) {
            throw $this->fail(isset($json['message']) ? $json['message'] : 'the payment could not be started', $raw);
        }
        return array('redirect' => $json['data']['authorization_url']);
    }

    public function check(array $order, array $request)
    {
        list($code, $json, $raw) = $this->http('GET', self::API . '/transaction/verify/' . rawurlencode($order['reference']), $this->auth());
        $d = isset($json['data']) && is_array($json['data']) ? $json['data'] : array();
        if (empty($json['status']) || !$d) {
            return array('paid' => false, 'amount' => null, 'currency' => null, 'id' => null,
                'message' => isset($json['message']) ? $json['message'] : 'Paystack did not confirm the payment.');
        }
        $paid = isset($d['status'], $d['reference']) && $d['status'] === 'success' && $d['reference'] === $order['reference'];
        return array(
            'paid' => $paid,
            'amount' => isset($d['amount']) ? $d['amount'] / 100 : null,
            'currency' => isset($d['currency']) ? $d['currency'] : null,
            'id' => isset($d['id']) ? (string) $d['id'] : null,
            'message' => $paid ? 'Paid' : ('Paystack status: ' . (isset($d['status']) ? $d['status'] : 'unknown')
                . (isset($d['gateway_response']) ? ' (' . $d['gateway_response'] . ')' : '')),
        );
    }

    // ------------------------------------------------------------- subaccounts (platform key)

    /** Nigerian banks: [code => name]. */
    public function banks()
    {
        list($code, $json, $raw) = $this->http('GET', self::API . '/bank?country=nigeria&perPage=200', $this->auth());
        if (empty($json['status']) || !isset($json['data'])) {
            throw $this->fail('the bank list could not be loaded', $raw);
        }
        $out = array();
        foreach ($json['data'] as $b) {
            if (!empty($b['code']) && (!isset($b['active']) || $b['active'])) {
                $out[(string) $b['code']] = $b['name'];
            }
        }
        asort($out);
        return $out;
    }

    /** The account holder's name, or throws when the account doesn't exist. */
    public function resolve_account($bank_code, $account_number)
    {
        list($code, $json, $raw) = $this->http('GET', self::API . '/bank/resolve?account_number=' . rawurlencode($account_number)
            . '&bank_code=' . rawurlencode($bank_code), $this->auth());
        if (empty($json['status']) || empty($json['data']['account_name'])) {
            throw $this->fail(isset($json['message']) ? $json['message'] : 'the account could not be found', $raw);
        }
        return $json['data']['account_name'];
    }

    /** Creates the subaccount; returns its code (ACCT_...). */
    public function create_subaccount($business_name, $bank_code, $account_number, $platform_percent, $email)
    {
        list($code, $json, $raw) = $this->http('POST', self::API . '/subaccount', $this->auth(), array_filter(array(
            'business_name' => $business_name,
            'settlement_bank' => $bank_code,
            'account_number' => $account_number,
            'percentage_charge' => (float) $platform_percent,
            'primary_contact_email' => $email,
        ), function ($v) { return $v !== '' && $v !== null; }));
        if (empty($json['status']) || empty($json['data']['subaccount_code'])) {
            throw $this->fail(isset($json['message']) ? $json['message'] : 'the subaccount could not be created', $raw);
        }
        return $json['data']['subaccount_code'];
    }

    private function auth()
    {
        return array('Authorization: Bearer ' . trim($this->config['secret_key']), 'Cache-Control: no-cache');
    }
}
