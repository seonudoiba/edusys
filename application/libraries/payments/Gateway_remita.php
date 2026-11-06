<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Remita by SystemSpecs (https://remita.net). Keys: merchant_id, service_type_id, api_key;
 * live = 0 (demo.remita.net) or 1 (login.remita.net). Naira only.
 * Card, bank transfer, USSD and bank branch on Remita's page (the RRR can also be paid at a bank).
 *
 * Flow: POST .../merchant/api/paymentinit (hash sha512(merchantId.serviceTypeId.orderId.amount.apiKey))
 * → RRR (Remita Retrieval Reference); the payer's browser POSTs to /remita/ecomm/finalize.reg;
 * Remita sends the payer back to return_url?RRR=...&orderID=...; GET .../{merchantId}/{RRR}/{hash}/status.reg
 * (hash sha512(RRR.apiKey.merchantId)): status 00 or 01 = paid.
 */
class Gateway_remita extends Payment_gateway
{
    const ID = 'remita';
    const NAME = 'Remita';
    const CURRENCIES = array('NGN');
    const REQUIRED = array('merchant_id', 'service_type_id', 'api_key');

    public function start(array $order)
    {
        $this->require_currency($order['currency']);
        $mid = trim($this->config['merchant_id']);
        $st = trim($this->config['service_type_id']);
        $key = trim($this->config['api_key']);
        $amount = number_format((float) $order['amount'], 2, '.', '');
        $hash = hash('sha512', $mid . $st . $order['reference'] . $amount . $key);
        list($code, $json, $raw) = $this->http('POST', $this->base() . '/remita/exapp/api/v1/send/api/echannelsvc/merchant/api/paymentinit',
            array('Authorization: remitaConsumerKey=' . $mid . ',remitaConsumerToken=' . $hash), array(
                'serviceTypeId' => $st,
                'amount' => $amount,
                'orderId' => $order['reference'],
                'payerName' => (string) ($order['name'] ?: $order['email']),
                'payerEmail' => $order['email'],
                'payerPhone' => (string) $order['phone'],
                'description' => (string) $order['description'],
            ));
        $json = $json ?: self::unwrap($raw);
        if (!isset($json['statuscode']) || $json['statuscode'] !== '025' || empty($json['RRR'])) {
            throw $this->fail(isset($json['status']) ? $json['status'] : 'the payment could not be started', $raw);
        }
        $rrr = (string) $json['RRR'];
        return array(
            'gateway_ref' => $rrr,
            'post' => array(
                'url' => $this->base() . '/remita/ecomm/finalize.reg',
                'fields' => array(
                    'merchantId' => $mid,
                    'hash' => hash('sha512', $mid . $rrr . $key),
                    'rrr' => $rrr,
                    'responseurl' => $order['return_url'],
                ),
            ),
        );
    }

    public function check(array $order, array $request)
    {
        $rrr = isset($order['gateway_ref']) ? (string) $order['gateway_ref'] : '';
        if ($rrr === '') {
            return array('paid' => false, 'amount' => null, 'currency' => null, 'id' => null, 'message' => 'No Remita reference (RRR) for this payment.');
        }
        $mid = trim($this->config['merchant_id']);
        $hash = hash('sha512', $rrr . trim($this->config['api_key']) . $mid);
        list($code, $json, $raw) = $this->http('GET', $this->base() . '/remita/exapp/api/v1/send/api/echannelsvc/' . rawurlencode($mid)
            . '/' . rawurlencode($rrr) . '/' . $hash . '/status.reg',
            array('Authorization: remitaConsumerKey=' . $mid . ',remitaConsumerToken=' . $hash));
        $json = $json ?: self::unwrap($raw);
        if (!isset($json['status'])) {
            return array('paid' => false, 'amount' => null, 'currency' => null, 'id' => $rrr, 'message' => 'Remita did not confirm the payment.');
        }
        $paid = in_array((string) $json['status'], array('00', '01'), true)
            && (!isset($json['orderId']) || $json['orderId'] === $order['reference']);
        return array(
            'paid' => $paid,
            'amount' => isset($json['amount']) ? (float) $json['amount'] : null,
            'currency' => 'NGN',
            'id' => $rrr,
            'message' => $paid ? 'Paid' : ('Remita: ' . (isset($json['message']) ? $json['message'] : $json['status'])),
        );
    }

    private function base()
    {
        return $this->live() ? 'https://login.remita.net' : 'https://demo.remita.net';
    }

    /** Remita wraps some answers as "jsonp ({...})". */
    private static function unwrap($raw)
    {
        if (preg_match('/\{.*\}/s', (string) $raw, $m)) {
            $j = json_decode($m[0], true);
            return is_array($j) ? $j : array();
        }
        return array();
    }
}
