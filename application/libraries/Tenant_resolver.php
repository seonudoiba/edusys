<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Maps a request's Host header to a school lookup. Pure logic, no database
 * or CodeIgniter dependencies, so it can be unit-tested in isolation.
 *
 * Only the Host header is used. X-Forwarded-Host, session values, URL
 * parameters and form fields never choose the school.
 */
class Tenant_resolver
{
    const LOOKUP_PLATFORM = 'platform';   // central host: the is_platform school
    const LOOKUP_SUBDOMAIN = 'subdomain'; // <label>.<base domain>
    const LOOKUP_DOMAIN = 'domain';       // another host: a school's verified custom domain
    const REJECT = 'reject';              // not one of ours: 404

    /** @var string */
    private $base_domain;

    /** @var string[] */
    private $central_hosts;

    /** @var string[] */
    private $reserved;

    public function __construct($base_domain, array $central_hosts, array $reserved = array())
    {
        $this->base_domain = self::normalize_host($base_domain);
        $this->central_hosts = array_map(array(__CLASS__, 'normalize_host'), $central_hosts);
        $this->reserved = array_map('strtolower', $reserved);
    }

    /** True when both the base domain and at least one central host are configured. */
    public function is_configured()
    {
        return $this->base_domain !== '' && $this->central_hosts !== array();
    }

    /**
     * Classifies a raw Host header value.
     *
     * @param string|null $host
     * @return array ['type' => LOOKUP_PLATFORM|LOOKUP_SUBDOMAIN|REJECT, 'host' => string,
     *                'port' => int|null, 'subdomain' => string|null]
     */
    public function classify($host)
    {
        $port = self::port((string) $host);
        $host = self::normalize_host((string) $host);
        $result = array('type' => self::REJECT, 'host' => $host, 'port' => $port, 'subdomain' => null);
        if ($host === '' || !self::is_valid_host($host)) {
            return $result;
        }
        if (in_array($host, $this->central_hosts, true)) {
            $result['type'] = self::LOOKUP_PLATFORM;
            return $result;
        }
        $suffix = '.' . $this->base_domain;
        if ($this->base_domain === '' || $host === $this->base_domain) {
            return $result;
        }
        if (substr($host, -strlen($suffix)) !== $suffix) {
            // Not under the base domain: maybe a school's own domain (looked up, verified ones only).
            if (strpos($host, '.') !== false && preg_match('/^[a-z0-9.-]+$/', $host) && !filter_var($host, FILTER_VALIDATE_IP)) {
                $result['type'] = self::LOOKUP_DOMAIN;
            }
            return $result;
        }
        $label = substr($host, 0, -strlen($suffix));
        if (!self::is_valid_label($label) || in_array($label, $this->reserved, true)) {
            return $result; // also rejects nested labels such as a.b.edusys.ng (they contain dots)
        }
        $result['type'] = self::LOOKUP_SUBDOMAIN;
        $result['subdomain'] = $label;
        return $result;
    }

    /** Lowercase, without port and trailing dot. */
    public static function normalize_host($host)
    {
        $host = strtolower(trim($host));
        if ($host === '') {
            return '';
        }
        if ($host[0] === '[') {
            return ''; // IPv6 literal: never a school
        }
        $colon = strrpos($host, ':');
        if ($colon !== false) {
            $port = substr($host, $colon + 1);
            if ($port === '' || !ctype_digit($port)) {
                return '';
            }
            $host = substr($host, 0, $colon);
        }
        return rtrim($host, '.');
    }

    /** Explicit port in a Host header value, or null. */
    public static function port($host)
    {
        $host = trim($host);
        if ($host === '' || $host[0] === '[' || !preg_match('/:(\d{1,5})$/', $host, $m)) {
            return null;
        }
        $port = (int) $m[1];
        return ($port >= 1 && $port <= 65535) ? $port : null;
    }

    /**
     * Base URL for a classified host: scheme://host[:port]/, omitting the scheme's default port.
     *
     * @param array $match result of classify()
     * @param bool $https
     */
    public static function base_url(array $match, $https)
    {
        $scheme = $https ? 'https' : 'http';
        $port = $match['port'];
        $show_port = $port !== null && !($https ? $port === 443 : $port === 80);
        return $scheme . '://' . $match['host'] . ($show_port ? ':' . $port : '') . '/';
    }

    /** Host made of DNS-safe characters (underscore allowed for local development names). */
    public static function is_valid_host($host)
    {
        return strlen($host) <= 253 && (bool) preg_match('/^[a-z0-9_]([a-z0-9_.-]*[a-z0-9_])?$/', $host)
            && strpos($host, '..') === false;
    }

    /** A single DNS label: letters, digits and inner hyphens, 1-63 characters. */
    public static function is_valid_label($label)
    {
        return (bool) preg_match('/^[a-z0-9]([a-z0-9-]{0,61}[a-z0-9])?$/', $label);
    }
}
