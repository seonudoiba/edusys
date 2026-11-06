<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Monnify by Moniepoint (https://monnify.com). Keys: api_key (MK_TEST_... / MK_PROD_...),
 * secret_key, contract_code; live = 0 (sandbox.monnify.com) or 1 (api.monnify.com).
 * Naira only. Card, bank transfer and USSD on Monnify's hosted page.
 *
 * Flow: POST /api/v1/auth/login (Basic api_key:secret_key) → access token;
 * POST /api/v1/merchant/transactions/init-transaction → checkoutUrl; Monnify sends the payer back
 * to return_url?paymentReference=...; GET /api/v2/merchant/transactions/query?paymentReference=...
 */
class Gateway_monnify extends Payment_gateway
{
    const ID = 'monnify';
    const NAME = 'Monnify';
    const CURRENCIES = array('NGN');
    const REQUIRED = array('api_key', 'secret_key', 'contract_code');

    /** @var string|null */
    private $token;

    public function start(array $order)
    {
        $this->require_currency($order['currency']);
        list($code, $json, $raw) = $this->http('POST', $this->base() . '/api/v1/merchant/transactions/init-transaction', $this->auth(), array(
            'amount' => round((float) $order['amount'], 2),
            'customerName' => (string) ($order['name'] ?: $order['email']),
            'customerEmail' => $order['email'],
            'paymentReference' => $order['reference'],
            'paymentDescription' => (string) $order['description'],
            'currencyCode' => 'NGN',
            'contractCode' => trim($this->config['contract_code']),
            'redirectUrl' => $order['return_url'],
            'paymentMethods' => array('CARD', 'ACCOUNT_TRANSFER', 'USSD'),
        ));
        if (empty($json['requestSuccessful']) || empty($json['responseBody']['checkoutUrl'])) {
            throw $this->fail(isset($json['responseMessage']) ? $json['responseMessage'] : 'the payment could not be started', $raw);
        }
        return array(
            'redirect' => $json['responseBody']['checkoutUrl'],
            'gateway_ref' => isset($json['responseBody']['transactionReference']) ? $json['responseBody']['transactionReference'] : null,
        );
    }

    public function check(array $order, array $request)
    {
        list($code, $json, $raw) = $this->http('GET', $this->base() . '/api/v2/merchant/transactions/query?paymentReference='
            . rawurlencode($order['reference']), $this->auth());
        $d = isset($json['responseBody']) && is_array($json['responseBody']) ? $json['responseBody'] : array();
        if (empty($json['requestSuccessful']) || !$d) {
            return array('paid' => false, 'amount' => null, 'currency' => null, 'id' => null,
                'message' => isset($json['responseMessage']) ? $json['responseMessage'] : 'Monnify did not confirm the payment.');
        }
        $status = isset($d['paymentStatus']) ? $d['paymentStatus'] : '';
        $paid = in_array($status, array('PAID', 'OVERPAID'), true)
            && isset($d['paymentReference']) && $d['paymentReference'] === $order['reference'];
        return array(
            'paid' => $paid,
            'amount' => isset($d['amountPaid']) ? (float) $d['amountPaid'] : null,
            'currency' => isset($d['currency']) ? $d['currency'] : 'NGN',
            'id' => isset($d['transactionReference']) ? $d['transactionReference'] : null,
            'message' => $paid ? 'Paid' : ('Monnify status: ' . ($status ?: 'unknown')),
        );
    }

    private function base()
    {
        return $this->live() ? 'https://api.monnify.com' : 'https://sandbox.monnify.com';
    }

    private function auth()
    {
        if ($this->token === null) {
            $basic = base64_encode(trim($this->config['api_key']) . ':' . trim($this->config['secret_key']));
            list($code, $json, $raw) = $this->http('POST', $this->base() . '/api/v1/auth/login', array('Authorization: Basic ' . $basic), '');
            if (empty($json['requestSuccessful']) || empty($json['responseBody']['accessToken'])) {
                throw $this->fail('the API key or secret key was refused' . ($this->live() ? '' : ' (test mode: use the sandbox keys)'), $raw);
            }
            $this->token = $json['responseBody']['accessToken'];
        }
        return array('Authorization: Bearer ' . $this->token);
    }
}
