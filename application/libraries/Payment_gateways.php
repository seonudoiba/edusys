<?php

defined('BASEPATH') or exit('No direct script access allowed');

require_once APPPATH . 'libraries/payments/Payment_gateway.php';

/**
 * Builds payment gateway drivers (application/libraries/payments/) from either
 *   - a school's own keys (payment_settings row, System Settings > Payment Methods): school fees, or
 *   - the platform's keys (server environment, /etc/edusys/edusys.env): school subscriptions.
 */
class Payment_gateways
{
    /** id => human name; the drivers this library knows */
    const DRIVERS = array(
        'paystack' => 'Paystack',
        'flutterwave' => 'Flutterwave',
        'monnify' => 'Monnify',
        'interswitch' => 'Interswitch',
        'remita' => 'Remita',
    );

    /**
     * The platform's keys, by gateway: setting => environment variable. A gateway is offered on
     * the subscription pages when all its *required* settings are set (see each driver).
     * *_LIVE: "true" for live/production, anything else = test/sandbox.
     */
    const PLATFORM_ENV = array(
        'paystack' => array('secret_key' => 'EDUSYS_PAYSTACK_SECRET_KEY'),
        'flutterwave' => array('secret_key' => 'EDUSYS_FLUTTERWAVE_SECRET_KEY', 'public_key' => 'EDUSYS_FLUTTERWAVE_PUBLIC_KEY'),
        'monnify' => array('api_key' => 'EDUSYS_MONNIFY_API_KEY', 'secret_key' => 'EDUSYS_MONNIFY_SECRET_KEY',
            'contract_code' => 'EDUSYS_MONNIFY_CONTRACT_CODE', 'live' => 'EDUSYS_MONNIFY_LIVE'),
        'interswitch' => array('merchant_code' => 'EDUSYS_INTERSWITCH_MERCHANT_CODE', 'pay_item_id' => 'EDUSYS_INTERSWITCH_PAY_ITEM_ID',
            'live' => 'EDUSYS_INTERSWITCH_LIVE'),
        'remita' => array('merchant_id' => 'EDUSYS_REMITA_MERCHANT_ID', 'service_type_id' => 'EDUSYS_REMITA_SERVICE_TYPE_ID',
            'api_key' => 'EDUSYS_REMITA_API_KEY', 'live' => 'EDUSYS_REMITA_LIVE'),
    );

    /**
     * A school's keys, by gateway: setting => payment_settings column (the columns are generic;
     * the settings page labels them per gateway). gateway_mode: 0 test, 1 live.
     */
    const SCHOOL_COLUMNS = array(
        'paystack' => array('secret_key' => 'api_secret_key'),
        'flutterwave' => array('secret_key' => 'api_secret_key', 'public_key' => 'api_publishable_key'),
        'monnify' => array('api_key' => 'api_publishable_key', 'secret_key' => 'api_secret_key', 'contract_code' => 'account_no', 'live' => 'gateway_mode'),
        'interswitch' => array('merchant_code' => 'api_username', 'pay_item_id' => 'account_no', 'live' => 'gateway_mode'),
        'remita' => array('merchant_id' => 'api_username', 'service_type_id' => 'account_no', 'api_key' => 'api_secret_key', 'live' => 'gateway_mode'),
    );

    public static function known($id)
    {
        return isset(self::DRIVERS[$id]);
    }

    public static function name($id)
    {
        return isset(self::DRIVERS[$id]) ? self::DRIVERS[$id] : ucfirst((string) $id);
    }

    /** @return Payment_gateway */
    public function driver($id, array $config)
    {
        if (!self::known($id)) {
            throw new InvalidArgumentException('Unknown payment gateway ' . $id);
        }
        $class = 'Gateway_' . $id;
        require_once APPPATH . 'libraries/payments/' . $class . '.php';
        return new $class($config);
    }

    /** Gateways that can pay a school through a subaccount of the platform's account. */
    const SUBACCOUNT_GATEWAYS = array('paystack', 'flutterwave');

    /**
     * Driver with the school's keys from its payment_settings row (object or array).
     *
     * Paystack and Flutterwave also work without the school's own keys: when the school only has a
     * subaccount (created on its Payment Methods page from its bank account, code in api_signature),
     * the platform's keys are used and the gateway settles each payment to the school's bank.
     */
    public function for_school($id, $row)
    {
        $row = (array) $row;
        $config = array();
        foreach (self::SCHOOL_COLUMNS[$id] as $setting => $column) {
            $config[$setting] = isset($row[$column]) ? trim((string) $row[$column]) : '';
        }
        if (isset($config['live'])) {
            $config['live'] = (int) $config['live'] === 1;
        }
        if (in_array($id, self::SUBACCOUNT_GATEWAYS, true) && $config['secret_key'] === '' && !empty($row['api_signature'])) {
            $platform = $this->platform_config($id);
            $config['secret_key'] = $platform['secret_key'];
            $config['subaccount'] = trim((string) $row['api_signature']);
        }
        return $this->driver($id, $config);
    }

    /** Driver with the platform's keys from the environment. */
    public function for_platform($id)
    {
        return $this->driver($id, $this->platform_config($id));
    }

    /** The platform's settings for a gateway, from the environment. */
    public function platform_config($id)
    {
        $config = array();
        foreach (self::PLATFORM_ENV[$id] as $setting => $var) {
            $value = getenv($var);
            $config[$setting] = $value === false ? '' : trim($value);
            if (stripos($config[$setting], 'change_me') !== false || stripos($config[$setting], 'change-me') !== false) {
                $config[$setting] = ''; // the template's placeholder is not a key
            }
        }
        if (isset($config['live'])) {
            $config['live'] = strtolower($config['live']) === 'true' || $config['live'] === '1';
        }
        return $config;
    }

    /** True when the platform can create subaccounts on $id (it has the secret key). */
    public function subaccounts_available($id)
    {
        return in_array($id, self::SUBACCOUNT_GATEWAYS, true) && !$this->for_platform($id)->missing();
    }

    /** The platform's share of each subaccount payment, percent (EDUSYS_PLATFORM_SPLIT_PERCENT, default 0). */
    public static function platform_split_percent()
    {
        $p = (float) getenv('EDUSYS_PLATFORM_SPLIT_PERCENT');
        return max(0.0, min(50.0, $p));
    }

    /** Gateways the platform has keys for (shown on the registration and renewal pages), id => name. */
    public function platform_enabled()
    {
        $out = array();
        foreach (array_keys(self::DRIVERS) as $id) {
            if (!$this->for_platform($id)->missing()) {
                $out[$id] = self::name($id);
            }
        }
        return $out;
    }

    /** For the health page: id => 'live' | 'test' | 'not set' (+ missing settings). */
    public function platform_status()
    {
        $out = array();
        foreach (array_keys(self::DRIVERS) as $id) {
            $d = $this->for_platform($id);
            $missing = $d->missing();
            if ($missing) {
                $vars = array();
                foreach ($missing as $m) {
                    $vars[] = self::PLATFORM_ENV[$id][$m];
                }
                $out[$id] = 'not set (' . implode(', ', $vars) . ')';
                continue;
            }
            if ($id === 'paystack' || $id === 'flutterwave') {
                $key = getenv(self::PLATFORM_ENV[$id]['secret_key']);
                $out[$id] = (stripos($key, 'test') !== false) ? 'test key' : 'live key';
            } else {
                $live = getenv(self::PLATFORM_ENV[$id]['live']);
                $out[$id] = in_array(strtolower((string) $live), array('true', '1'), true) ? 'live' : 'test (sandbox)';
            }
        }
        return $out;
    }
}
