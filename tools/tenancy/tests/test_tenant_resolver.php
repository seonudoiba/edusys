<?php
if (PHP_SAPI !== 'cli') { http_response_code(404); exit; }
// Unit tests for application/libraries/Tenant_resolver.php and Tenant.php.
// Run: php tools/tenancy/tests/test_tenant_resolver.php

define('BASEPATH', __DIR__);
require __DIR__ . '/../../../application/libraries/Tenant_resolver.php';
require __DIR__ . '/../../../application/libraries/Tenant.php';

$failures = 0;
$count = 0;
function check($label, $expected, $actual)
{
    global $failures, $count;
    $count++;
    if ($expected !== $actual) {
        $failures++;
        echo "FAIL $label\n  expected: " . var_export($expected, true) . "\n  actual:   " . var_export($actual, true) . "\n";
    }
}

$r = new Tenant_resolver('edusys.ng', array('edusys.ng', 'www.edusys.ng'), array('www', 'admin', 'api'));
$type = function ($host) use ($r) { $m = $r->classify($host); return $m['type'] . ($m['subdomain'] !== null ? ':' . $m['subdomain'] : ''); };

// Central host and aliases
check('central', 'platform', $type('edusys.ng'));
check('central www', 'platform', $type('www.edusys.ng'));
check('central uppercase + port + trailing dot', 'platform', $type('EduSys.NG.:443'));

// School subdomains
check('subdomain', 'subdomain:greenfield', $type('greenfield.edusys.ng'));
check('subdomain uppercase + port', 'subdomain:greenfield', $type('GreenField.Edusys.ng:8080'));
check('subdomain digits and hyphen', 'subdomain:st-mary-2', $type('st-mary-2.edusys.ng'));
check('subdomain 63 chars', 'subdomain:' . str_repeat('a', 63), $type(str_repeat('a', 63) . '.edusys.ng'));

// Rejections
check('empty host', 'reject', $type(''));
check('null host', 'reject', $type(null));
check('other domain', 'reject', $type('greenfield.example.com'));
check('suffix trick', 'reject', $type('greenfieldedusys.ng'));
check('lookalike parent', 'reject', $type('greenfield.edusys.ng.evil.com'));
check('nested subdomain', 'reject', $type('a.b.edusys.ng'));
check('reserved label', 'reject', $type('admin.edusys.ng'));
check('reserved www is central only when listed', 'platform', $type('www.edusys.ng'));
check('label too long', 'reject', $type(str_repeat('a', 64) . '.edusys.ng'));
check('leading hyphen', 'reject', $type('-bad.edusys.ng'));
check('trailing hyphen', 'reject', $type('bad-.edusys.ng'));
check('underscore label', 'reject', $type('bad_label.edusys.ng'));
check('empty label', 'reject', $type('.edusys.ng'));
check('double dot', 'reject', $type('a..edusys.ng'));
check('ip address', 'reject', $type('127.0.0.1'));
check('ipv6 literal', 'reject', $type('[::1]:80'));
check('non-numeric port', 'reject', $type('greenfield.edusys.ng:abc'));
check('header injection chars', 'reject', $type("greenfield.edusys.ng\r\nX: y"));
check('space', 'reject', $type('green field.edusys.ng'));
check('unicode', 'reject', $type('grëenfield.edusys.ng'));

// Ports and base URLs
$m = $r->classify('XYZ.edusys.ng:8099');
check('port kept', 8099, $m['port']);
check('base_url with port', 'http://xyz.edusys.ng:8099/', Tenant_resolver::base_url($m, false));
check('base_url default http port', 'http://xyz.edusys.ng/', Tenant_resolver::base_url($r->classify('xyz.edusys.ng:80'), false));
check('base_url default https port', 'https://xyz.edusys.ng/', Tenant_resolver::base_url($r->classify('xyz.edusys.ng:443'), true));
check('base_url 80 under https is shown', 'https://xyz.edusys.ng:80/', Tenant_resolver::base_url($r->classify('xyz.edusys.ng:80'), true));
check('base_url no port', 'https://edusys.ng/', Tenant_resolver::base_url($r->classify('edusys.ng'), true));
check('port out of range ignored', null, Tenant_resolver::port('x.edusys.ng:99999'));

// Configuration
check('configured', true, $r->is_configured());
$unset = new Tenant_resolver('', array());
check('not configured', false, $unset->is_configured());
check('unconfigured rejects subdomains', 'reject', $unset->classify('x.edusys.ng')['type']);

// Local development base domain with an underscore (Laragon folder names)
$local = new Tenant_resolver('edusys_ng.test', array('edusys_ng.test'));
check('local central', 'platform', $local->classify('edusys_ng.test')['type']);
check('local subdomain', 'subdomain', $local->classify('xyz.edusys_ng.test')['type']);

// Tenant context
check('no tenant yet', false, Tenant::has());
$threw = false;
try { Tenant::id(); } catch (LogicException $e) { $threw = true; }
check('id() without a tenant fails closed', true, $threw);
Tenant::set(array('id' => '2', 'school_name' => 'XYZ', 'subdomain' => 'xyz', 'is_platform' => '0', 'status' => '1'), 'xyz.edusys.ng');
check('tenant id', 2, Tenant::id());
check('tenant not platform', false, Tenant::is_platform());
check('tenant host', 'xyz.edusys.ng', Tenant::host());
$threw = false;
try { Tenant::set(array('id' => 1, 'is_platform' => 1)); } catch (LogicException $e) { $threw = true; }
check('tenant cannot be changed once set', true, $threw);
check('tenant unchanged after refused set', 2, Tenant::id());

echo ($failures ? "$failures of $count checks FAILED\n" : "All $count checks passed\n");
exit($failures ? 1 : 0);
