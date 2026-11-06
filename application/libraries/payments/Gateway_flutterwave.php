<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Flutterwave (https://flutterwave.com), API v3. Keys: secret_key (FLWSECK_TEST-... /
 * FLWSECK-...); public_key is kept for the settings page but not needed for this flow.
 * Test or live is decided by the key itself.
 *
 * Subaccount mode (config subaccount = RS_...): the platform's key, the payment is split to the
 * school's subaccount and settled to its bank account.
 *
 * Flow: POST /v3/payments → data.link (hosted page); Flutterwave sends the payer back to
 * return_url?status=...&tx_ref=...&transaction_id=...; GET /v3/transactions/verify_by_reference.
 */
class Gateway_flutterwave extends Payment_gateway
{
    const ID = 'flutterwave';
    const NAME = 'Flutterwave';
    const REQUIRED = array('secret_key');
    const API = 'https://api.flutterwave.com/v3';

    public function start(array $order)
    {
        list($code, $json, $raw) = $this->http('POST', self::API . '/payments', $this->auth(), array(
            'tx_ref' => $order['reference'],
            'amount' => round((float) $order['amount'], 2),
            'currency' => strtoupper($order['currency']),
            'redirect_url' => $order['return_url'],
            'customer' => array_filter(array(
                'email' => $order['email'],
                'name' => (string) $order['name'],
                'phonenumber' => (string) $order['phone'],
            )),
            'customizations' => array('title' => (string) $order['description']),
        ) + (empty($this->config['subaccount']) ? array() : array(
            'subaccounts' => array(array('id' => $this->config['subaccount'])),
        )));
        if (!isset($json['status']) || $json['status'] !== 'success' || empty($json['data']['link'])) {
            throw $this->fail(isset($json['message']) ? $json['message'] : 'the payment could not be started', $raw);
        }
        return array('redirect' => $json['data']['link']);
    }

    public function check(array $order, array $request)
    {
        // Ask by OUR reference (not the transaction_id in the URL, which the payer could change).
        list($code, $json, $raw) = $this->http('GET', self::API . '/transactions/verify_by_reference?tx_ref=' . rawurlencode($order['reference']), $this->auth());
        $d = isset($json['data']) && is_array($json['data']) ? $json['data'] : array();
        if (!isset($json['status']) || $json['status'] !== 'success' || !$d) {
            return array('paid' => false, 'amount' => null, 'currency' => null, 'id' => null,
                'message' => isset($json['message']) ? $json['message'] : 'Flutterwave did not confirm the payment.');
        }
        $paid = isset($d['status'], $d['tx_ref']) && $d['status'] === 'successful' && $d['tx_ref'] === $order['reference'];
        return array(
            'paid' => $paid,
            'amount' => isset($d['amount']) ? (float) $d['amount'] : null,
            'currency' => isset($d['currency']) ? $d['currency'] : null,
            'id' => isset($d['id']) ? (string) $d['id'] : null,
            'message' => $paid ? 'Paid' : ('Flutterwave status: ' . (isset($d['status']) ? $d['status'] : 'unknown')),
        );
    }

    // ------------------------------------------------------------- subaccounts (platform key)

    /** Nigerian banks: [code => name]. */
    public function banks()
    {
        list($code, $json, $raw) = $this->http('GET', self::API . '/banks/NG', $this->auth());
        if (!isset($json['status']) || $json['status'] !== 'success' || !isset($json['data'])) {
            throw $this->fail('the bank list could not be loaded', $raw);
        }
        $out = array();
        foreach ($json['data'] as $b) {
            if (!empty($b['code'])) {
                $out[(string) $b['code']] = $b['name'];
            }
        }
        asort($out);
        return $out;
    }

    /** The account holder's name, or throws when the account doesn't exist. */
    public function resolve_account($bank_code, $account_number)
    {
        list($code, $json, $raw) = $this->http('POST', self::API . '/accounts/resolve', $this->auth(),
            array('account_number' => $account_number, 'account_bank' => $bank_code));
        if (!isset($json['status']) || $json['status'] !== 'success' || empty($json['data']['account_name'])) {
            throw $this->fail(isset($json['message']) ? $json['message'] : 'the account could not be found', $raw);
        }
        return $json['data']['account_name'];
    }

    /** Creates the subaccount; returns its id (RS_...). */
    public function create_subaccount($business_name, $bank_code, $account_number, $platform_percent, $email, $phone = '')
    {
        list($code, $json, $raw) = $this->http('POST', self::API . '/subaccounts', $this->auth(), array(
            'account_bank' => $bank_code,
            'account_number' => $account_number,
            'business_name' => $business_name,
            'business_email' => $email,
            'business_mobile' => $phone !== '' ? $phone : '00000000000',
            'country' => 'NG',
            'split_type' => 'percentage',
            'split_value' => round($platform_percent / 100, 4), // the platform's share
        ));
        if (!isset($json['status']) || $json['status'] !== 'success' || empty($json['data']['subaccount_id'])) {
            throw $this->fail(isset($json['message']) ? $json['message'] : 'the subaccount could not be created', $raw);
        }
        return $json['data']['subaccount_id'];
    }

    private function auth()
    {
        return array('Authorization: Bearer ' . trim($this->config['secret_key']));
    }
}
