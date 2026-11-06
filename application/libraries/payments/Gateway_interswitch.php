<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Interswitch Web Checkout / Quickteller Business (https://business.quickteller.com).
 * Keys: merchant_code (e.g. MX12345), pay_item_id; live = 0 (QA) or 1 (production).
 * Naira only (ISO 4217 numeric 566). Card, bank transfer, USSD, wallet on Interswitch's page.
 *
 * Flow: the payer's browser POSTs a form to Interswitch's /collections/w/pay; Interswitch POSTs
 * the payer back to return_url (txnref, payRef, resp...); GET gettransaction.json with our
 * reference and the expected amount (in kobo) gives the authoritative result (ResponseCode 00).
 */
class Gateway_interswitch extends Payment_gateway
{
    const ID = 'interswitch';
    const NAME = 'Interswitch';
    const CURRENCIES = array('NGN');
    const REQUIRED = array('merchant_code', 'pay_item_id');

    public function start(array $order)
    {
        $this->require_currency($order['currency']);
        return array('post' => array(
            'url' => ($this->live() ? 'https://newwebpay.interswitchng.com' : 'https://newwebpay.qa.interswitchng.com') . '/collections/w/pay',
            'fields' => array(
                'merchant_code' => trim($this->config['merchant_code']),
                'pay_item_id' => trim($this->config['pay_item_id']),
                'pay_item_name' => substr((string) $order['description'], 0, 100),
                'txn_ref' => $order['reference'],
                'amount' => self::minor($order['amount']),
                'currency' => '566',
                'cust_email' => $order['email'],
                'cust_name' => (string) $order['name'],
                'cust_mobile_no' => (string) $order['phone'],
                'site_redirect_url' => $order['return_url'],
                'display_mode' => 'PAGE',
            ),
        ));
    }

    public function check(array $order, array $request)
    {
        $url = ($this->live() ? 'https://webpay.interswitchng.com' : 'https://qa.interswitchng.com')
            . '/collections/api/v1/gettransaction.json?merchantcode=' . rawurlencode(trim($this->config['merchant_code']))
            . '&transactionreference=' . rawurlencode($order['reference'])
            . '&amount=' . self::minor($order['amount']);
        list($code, $json, $raw) = $this->http('GET', $url);
        if (!is_array($json) || !isset($json['ResponseCode'])) {
            return array('paid' => false, 'amount' => null, 'currency' => null, 'id' => null,
                'message' => 'Interswitch did not confirm the payment' . ($code ? ' (HTTP ' . $code . ')' : '') . '.');
        }
        $paid = $json['ResponseCode'] === '00'
            && (!isset($json['MerchantReference']) || $json['MerchantReference'] === $order['reference']);
        return array(
            'paid' => $paid,
            'amount' => isset($json['Amount']) ? $json['Amount'] / 100 : null,
            'currency' => 'NGN',
            'id' => isset($json['PaymentReference']) ? (string) $json['PaymentReference'] : null,
            'message' => $paid ? 'Paid' : ('Interswitch: ' . (isset($json['ResponseDescription']) ? $json['ResponseDescription'] : $json['ResponseCode'])),
        );
    }
}
