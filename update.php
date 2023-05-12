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

// Get the domains from the URL parameter and split them from the comma separated string
$domains = explode(',', $_REQUEST['domain']);

// Loop through each domain and call the Handler
foreach ($domains as $domain) {
    // Create a new request object with the current domain
    $request = $_REQUEST;
    $request['domain'] = trim($domain);


    // Call the Handler with the current domain
    (new netcup\DNS\API\Handler($config, $request))->doRun();
}
