<?php
require_once "dnsapi.php";
//
// CONFIG SECTION
// Info: 	
// Hostname record needs to exist before script is working
// This script will not create missing hostnames in the DNS zone
//

$username 		= "dyndns.username";
$password 		= "secret";
$dataFile		= "data.json";
$debug			= false;

// netcup API information
$apiKey			= "netcup DNS API Key";
$apiPassword	= "netcup DNS API Password";
$customerId		= "netcup customer id";

//
// NO CONFIGURATION BEYOND THIS LINE
//
$getUsername	= $_GET['user'];
$getPassword	= $_GET['password'];
$getDomain 		= $_GET['domain'];
$getIpv4 		= $_GET['ipv4'];
$getIpv6 		= $_GET['ipv6'];

// FUNCTIONS
// is called to write dataFile and exit
function write_and_exit(){
	global $dataFile, $data;
	if(!@file_put_contents($dataFile, json_encode($data))){
		echo("[ERROR] unable to write $dataFile <br>");
	}
	exit();
}

// is called to append log to data object and enable debug output
function logging($text){
	global $data, $debug, $getDomain;
	array_push($data[$getDomain]['log'], $text);
	if($debug == true){
		echo("[DEBUG] $text <br>");
	}
}
// function to get domain and reduce host from it
function reduceHost($domain){
    $domainParts = explode('.', $domain);
    array_shift($domainParts);
    $tld = implode('.', $domainParts);
    return $tld;
}

// INIT
if($debug == true){
	error_reporting( E_ALL );
	ini_set('display_errors', 1);
}

// PRESET VALIDATION
// get data from object, create if not existent
if(file_exists($dataFile)){
	$data = json_decode(@file_get_contents($dataFile), true);
} else {
	touch($dataFile);
}

// check for domain parameter and exit if NULL
if(empty($getDomain)){
	logging("no domain given. exiting...");
	write_and_exit();
}
// init log array
$data[$getDomain]['log'] = [];

// authenticate, store number of failed logins
if($getUsername != $username or $getPassword != $password){
	logging("authentication failed. exiting...");
	$data[$getDomain]['failed_logins']++;
	write_and_exit();
} else {
	$data[$getDomain]['failed_logins']=0;
}

// DOMAIN SPECIFIC PROCESSING
// write current timestamp to data array
$data[$getDomain]['timestamp'] = time();

// validate IP addresses (v4 and v6) and write to data array
if(filter_var($getIpv4, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)){
	logging("valid IPv4");
	// write to data array
	$data[$getDomain]['ipv4'] = $getIpv4;	
} else {
	logging("no valid IPv4");
	// write to data array
	$data[$getDomain]['ipv4'] = NULL;	
}

if(filter_var($getIpv6, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)){
	logging("valid IPv6");
	// write to data array
	$data[$getDomain]['ipv6'] = $getIpv6;	
} else {
	logging("no valid IPv6");
	// write to data array
	$data[$getDomain]['ipv6'] = NULL;	
}

if($data[$getDomain]['ipv4'] == NULL && $data[$getDomain]['ipv6'] == NULL){
	logging("no valid IP found");
	write_and_exit();
}

// broadcast new IP to DNS API
$clientRequestId = md5($getDomain);
$dnsClient = new DomainWebserviceSoapClient();
$clientHandle = $dnsClient->login($customerId, $apiKey, $apiPassword, $clientRequestId);
$infoHandle = $dnsClient->infoDnsRecords(reduceHost($getDomain), $customerId, $apiKey, $clientHandle->responsedata->apisessionid, $clientRequestId);#

$dnsrecords = $infoHandle->responsedata->dnsrecords;
foreach($dnsrecords as $key => &$record){
	// write IPv4 update set if valid address and existent record
	if($record->hostname == explode('.', "$getDomain")[0] && $record->type == "A" && $data[$getDomain]['ipv4'] != NULL){
		$record->destination = $data[$getDomain]['ipv4'];
	}
	// write IPv6 update set if valid address and existent record
	if($record->hostname == explode('.', "$getDomain")[0] && $record->type == "AAAA" && $data[$getDomain]['ipv6'] != NULL){
		$record->destination = $data[$getDomain]['ipv6'];
	}
}

//$clientHandle->clientrequestid = md5(microtime(true));
$recordSet = new Dnsrecordset();
$recordSet->dnsrecords = $dnsrecords;

$updateHandle = $dnsClient->updateDnsRecords(reduceHost($getDomain), $customerId, $apiKey, $clientHandle->responsedata->apisessionid, $clientRequestId, $recordSet);
logging("dns recordset updated");

$result = $dnsClient->logout($customerId, $apiKey, $clientHandle->responsedata->apisessionid, $clientRequestId);
logging("api logout");
// finish
write_and_exit();
?>
