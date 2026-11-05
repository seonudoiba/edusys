<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * A database operation that would cross or bypass the school boundary.
 * Thrown by the tenant-aware driver; never caught by application code.
 */
class Tenant_violation extends RuntimeException
{
}
