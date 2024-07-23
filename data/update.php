<?php

error_reporting(-1);
ini_set('display_errors', 1);
ini_set('html_errors', 0);

header('Content-Type: text/plain; charset=utf-8');

require_once __DIR__ . '/src/Soap.php';
require_once __DIR__ . '/src/Config.php';
require_once __DIR__ . '/src/Payload.php';
require_once __DIR__ . '/src/Handler.php';

if (!file_exists('.env')) {
    throw new RuntimeException('.env file missing');
}

$config = parse_ini_file('.env', false, INI_SCANNER_TYPED);

if (getenv('DDNS_USER') !== false) {
    $config['username'] = getenv('DDNS_USER');
}
if (getenv('DDNS_PASS') !== false) {
    $config['password'] = getenv('DDNS_PASS');
}
if (getenv('NETCUP_APIKEY') !== false) {
    $config['apiKey'] = getenv('NETCUP_APIKEY');
}
if (getenv('NETCUP_APIPASS') !== false) {
    $config['apiPassword'] = getenv('NETCUP_APIPASS');
}
if (getenv('NETCUP_CUSTOMERID') !== false) {
    $config['customerId'] = getenv('NETCUP_CUSTOMERID');
}
if (getenv('DDNS_DEBUG') !== false) {
    $config['debug'] = getenv('DDNS_DEBUG');
}
if (getenv('DDNS_LOG') !== false) {
    $config['log'] = getenv('DDNS_LOG');
}
if (getenv('DDNS_LOGFILE') !== false) {
    $config['logFile'] = getenv('DDNS_LOGFILE');
}
if (getenv('DDNS_RETURNIP') !== false) {
    $config['returnIp'] = getenv('DDNS_RETURNIP');
}
if (getenv('DDNS_ALLOWCREATE') !== false) {
    $config['allowCreate'] = getenv('DDNS_ALLOWCREATE');
}
if (getenv('DDNS_RESTRICTDOMAIN') !== false) {
    $config['restrictDomain'] = getenv('DDNS_RESTRICTDOMAIN');
}
if (getenv('DDNS_FORCEDDOMAIN') !== false) {
    $config['domain'] = getenv('DDNS_FORCEDDOMAIN');
}
if (getenv('DDNS_FORCEDHOST') !== false) {
    $config['host'] = getenv('DDNS_FORCEDHOST');
}

(new netcup\DNS\API\Handler($config, $_REQUEST))->doRun();
