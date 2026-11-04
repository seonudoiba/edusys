<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * The current school for this request.
 *
 * Set once by hooks/Tenant_hook.php before any controller is constructed,
 * from the request host (web) or EDUSYS_SCHOOL_ID (CLI). Nothing else may set
 * or change it: not the session, not the URL, not form input.
 *
 * Static so it is available before the CodeIgniter instance exists and from
 * any layer (hooks, the database driver, models, views).
 */
class Tenant
{
    /** @var array|null The resolved `school` row (id, school_name, subdomain, is_platform, status). */
    private static $school = null;

    /** @var string|null Host the school was resolved from (null on the CLI). */
    private static $host = null;

    /** @var bool */
    private static $locked = false;

    /**
     * Records the resolved school. Can only be called once per request.
     *
     * @param array $school `school` row
     * @param string|null $host
     */
    public static function set(array $school, $host = null)
    {
        if (self::$locked) {
            throw new LogicException('The tenant for this request is already set.');
        }
        if (empty($school['id']) || (int) $school['id'] < 1) {
            throw new InvalidArgumentException('A tenant needs a school id.');
        }
        self::$school = $school;
        self::$host = $host;
        self::$locked = true;
    }

    /** Marks the request as running without a school (CLI migrations, seeds). */
    public static function set_none()
    {
        if (self::$locked) {
            throw new LogicException('The tenant for this request is already set.');
        }
        self::$locked = true;
    }

    /** True when a school is set for this request. */
    public static function has()
    {
        return self::$school !== null;
    }

    /**
     * Current school id.
     *
     * @return int
     * @throws LogicException when no school is set (fail closed rather than return 0)
     */
    public static function id()
    {
        if (self::$school === null) {
            throw new LogicException('No school is set for this request.');
        }
        return (int) self::$school['id'];
    }

    /** @return array|null */
    public static function school()
    {
        return self::$school;
    }

    /** True when the current school is the platform owner (served on the central host). */
    public static function is_platform()
    {
        return self::$school !== null && (int) self::$school['is_platform'] === 1;
    }

    /**
     * Stops the request with a 404 unless it is served on the central host (the platform school).
     * Called first in the constructors of platform controllers (school registry, plans,
     * subscriptions, registration, platform payments), so school subdomains can't reach them.
     */
    public static function require_platform()
    {
        if (!self::is_platform()) {
            show_404();
        }
    }

    /** @return string|null */
    public static function host()
    {
        return self::$host;
    }
}
