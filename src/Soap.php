<?php

namespace netcup\DNS\API\Soap;

use Exception;
use SoapClient;
use SoapFault;

/**
 * Example client to access the domain reselling API. 
 * Please note: We cannot guarantee the function of this client.  
 * @service DomainWebserviceSoapClient
 */
class DomainWebserviceSoapClient{


	/**
	 * The WSDL URI
	 *
	 * @var string
	 */
	public static $_WsdlUri='https://ccp.netcup.net/run/webservice/servers/endpoint.php?WSDL';
	/**
	 * The PHP SoapClient object
	 *
	 * @var object
	 */
	public static $_Server=NULL;

	/**
	 * Send a SOAP request to the server
	 *
	 * @param string $method The method name
	 * @param array $param The parameters
	 * @return mixed The server response
	 */
	public static function _Call($method,$param) {
		try {
			if (is_NULL(self::$_Server)) {
				self::$_Server = new SoapClient(self::$_WsdlUri);
			}
			return self::$_Server->__soapCall($method,$param);
		} catch (Exception $exception) {
			throw new SoapFault($exception->faultstring, $exception->faultcode, $exception);
		}
	}

	/**
	 * Create a new domain for a fee.
	 * This function is avaliable for domain resellers.
	 *
	 * @param domainname $domainname Name of the domain including top-level domain.
	 * @param positiveInteger $customernumber customer number of reseller at netcup.
	 * @param Contactentries $contacts Ids of the contact handles registered previously. Format like this: $assignedhandletypes_obj = new Contactentries(); $assignedhandletypes_obj->ownerc = 111; $assignedhandletypes_obj->adminc = 222;
	 * @param Nameserverentries $nameservers object of nameservers. Please define as object in format for example: $nameservers = new Nameserverentries(); $nameserverentry1 = new Nameserverentry(); $nameserverentry1->hostname = "ns1.nameserverdomain.de"; $nameserverentry1->ipv4 = "12.34.56.78"; $nameserverentry1->ipv6 = "123:0000:4:5600::8"; $nameservers->nameserver1 = $nameserverentry1;  $nameserverentry2 = new Nameserverentry(); $nameserverentry2->hostname = "ns2.nameserverdomain.de"; $nameserverentry2->ipv4 = "12.34.56.178"; $nameserverentry2->ipv6 = "123:0000:4:5600::7"; $nameservers->nameserver2 = $nameserverentry2;
	 * @param string $apikey Unique API key generated in customer control panel.
	 * @param string $apisessionid Unique API session id created by login command.
	 * @param clientrequestid $clientrequestid Id from client side. Can contain letters and numbers. Field is optional.
	 * @return Responsemessage $responsemessage with information about result of the action like short and long resultmessages, message status, etc.
	 */
	public function createDomain($domainname,$customernumber,$contacts,$nameservers,$apikey,$apisessionid,$clientrequestid){
		return self::_Call('createDomain', func_get_args());
	}

	/**
	 * Update a domain contacts and nameserver settings.
	 * For updateing owner handle use changeOwnerDomain.
	 * This function is avaliable for domain resellers.
	 *
	 * @param domainname $domainname Name of the domain including top-level domain.
	 * @param positiveInteger $customernumber customer number of reseller at netcup.
	 * @param Contactentries $contacts Ids of the contact handles registered previously.
	 * @param Nameserverentries $nameservers object of nameservers. Please define as object in format for example: $nameservers = new Nameserverentries(); $nameserverentry1 = new Nameserverentry(); $nameserverentry1->hostname = "ns1.nameserverdomain.de"; $nameserverentry1->ipv4 = "12.34.56.78"; $nameserverentry1->ipv6 = "123:0000:4:5600::8"; $nameservers->nameserver1 = $nameserverentry1;  $nameserverentry2 = new Nameserverentry(); $nameserverentry2->hostname = "ns2.nameserverdomain.de"; $nameserverentry2->ipv4 = "12.34.56.178"; $nameserverentry2->ipv6 = "123:0000:4:5600::7"; $nameservers->nameserver2 = $nameserverentry2;
	 * @param string $apikey Unique API key generated in customer control panel.
	 * @param string $apisessionid Unique API session id created by login command.
	 * @param clientrequestid $clientrequestid Id from client side. Can contain letters and numbers. Field is optional.
	 * @param keepdnssecrecords $keepdnssecrecords TRUE Saved DNSSEC records will be preserved|keepdnssecrecords FALSE Saved DNSSEC records will be deleted. Information is only relevant if you use DNSSEC. Field is optional. Default FALSE.
	 * @param DnssecentriesObject $dnssecentries DNSSEC entries for a domain Field is optional.
	 * @return Responsemessage $responsemessage with information about result of the action like short and long resultmessages, message status, etc.
	 */
	public function updateDomain($domainname,$customernumber,$contacts,$nameservers,$apikey,$apisessionid,$clientrequestid,$keepdnssecrecords,$dnssecentries){
		return self::_Call('updateDomain', func_get_args());
	}

	/**
	 * Incomming transfer a new domain for a fee.
	 * This function is avaliable for domain resellers.
	 *
	 * @param domainname $domainname Name of the domain including top-level domain.
	 * @param positiveInteger $customernumber customer number of reseller at netcup.
	 * @param Contactentries $contacts Ids of the contact handles registered previously. Format like this: $assignedhandletypes_obj = new Contactentries(); $assignedhandletypes_obj->ownerc = 111; $assignedhandletypes_obj->adminc = 222;
	 * @param Nameserverentries $nameservers object of nameservers. Please define as object in format for example: $nameservers = new Nameserverentries(); $nameserverentry1 = new Nameserverentry(); $nameserverentry1->hostname = "ns1.nameserverdomain.de"; $nameserverentry1->ipv4 = "12.34.56.78"; $nameserverentry1->ipv6 = "123:0000:4:5600::8"; $nameservers->nameserver1 = $nameserverentry1;  $nameserverentry2 = new Nameserverentry(); $nameserverentry2->hostname = "ns2.nameserverdomain.de"; $nameserverentry2->ipv4 = "12.34.56.178"; $nameserverentry2->ipv6 = "123:0000:4:5600::7"; $nameservers->nameserver2 = $nameserverentry2;
	 * @param string $apikey Unique API key generated in customer control panel.
	 * @param string $apisessionid Unique API session id created by login command.
	 * @param string $authcode AuthInfo code for this domain.
	 * @param clientrequestid $clientrequestid Id from client side. Can contain letters and numbers. Field is optional.
	 * @return Responsemessage $responsemessage with information about result of the action like short and long resultmessages, message status, etc.
	 */
	public function transferDomain($domainname,$customernumber,$contacts,$nameservers,$apikey,$apisessionid,$authcode,$clientrequestid){
		return self::_Call('transferDomain', func_get_args());
	}

	/**
	 * End session for API user.
	 * A login has to be send before each request.
	 * This function is avaliable for domain resellers.
	 *
	 * @param positiveInteger $customernumber customer number of reseller at netcup.
	 * @param string $apikey Unique API key generated in customer control panel.
	 * @param string $apisessionid Unique API session id created by login command.
	 * @param clientrequestid $clientrequestid Id from client side. Can contain letters and numbers. Field is optional.
	 * @return Responsemessage $responsemessage with information about result of the action like short and long resultmessages, message status, etc.
	 */
	public function logout($customernumber,$apikey,$apisessionid,$clientrequestid){
		return self::_Call('logout', func_get_args());
	}

	/**
	 * Create a login session for API users.
	 * A login has to be send before each request.
	 * This function is avaliable for domain resellers.
	 *
	 * @param positiveInteger $customernumber customer number of reseller at netcup.
	 * @param string $apikey Unique API key generated in customer control panel.
	 * @param string $apipassword API password set in customer control panel.
	 * @param clientrequestid $clientrequestid Id from client side. Can contain letters and numbers. Field is optional.
	 * @return Responsemessage $responsemessage with information about result of the action like short and long resultmessages, message status, etc. In responsedata field: string $apisessionid Server generated ID to be used with further requests when login was successful.
	 */
	public function login($customernumber,$apikey,$apipassword,$clientrequestid){
		return self::_Call('login', func_get_args());
	}

	/**
	 * Get all messages that are not read.
	 * This function is avaliable for domain resellers.
	 *
	 * @param positiveInteger $messagecount maximum number of unread messages to receive.
	 * @param positiveInteger $customernumber customer number of reseller at netcup.
	 * @param string $apikey Unique API key generated in customer control panel.
	 * @param string $apisessionid Unique API session id created by login command.
	 * @param clientrequestid $clientrequestid Id from client side. Can contain letters and numbers. Field is optional.
	 * @return Responsemessage $responsemessage with information about result of the action like short and long resultmessages, message status, etc. In responsedata field: ArrayOPollObject Unread Messages for this customer.
	 */
	public function poll($messagecount,$customernumber,$apikey,$apisessionid,$clientrequestid){
		return self::_Call('poll', func_get_args());
	}

	/**
	 * Acknowledge log message from call made via API.
	 * This function is avaliable for domain resellers.
	 *
	 * @param positiveInteger $apilogid ID of message to mark as read.
	 * @param positiveInteger $customernumber customer number of reseller at netcup.
	 * @param string $apikey Unique API key generated in customer control panel.
	 * @param string $apisessionid Unique API session id created by login command.
	 * @param clientrequestid $clientrequestid Id from client side. Can contain letters and numbers. Field is optional.
	 * @return Responsemessage $responsemessage with information about result of the action like short and long resultmessages, message status, etc.
	 */
	public function ackpoll($apilogid,$customernumber,$apikey,$apisessionid,$clientrequestid){
		return self::_Call('ackpoll', func_get_args());
	}

	/**
	 * Change Ownerhandle. Current Owner has to allow or deny the ownerchange by clicking a link that is sent to him via e-mail.
	 * Process ends after 5 days if not answered.
	 * This function is avaliable for domain resellers.
	 *
	 * @param integer $new_handle_id Handle ID of the contact that should be the new owner.
	 * @param domainname $domainname Name of the domain including top-level domain.
	 * @param positiveInteger $customernumber customer number of reseller at netcup.
	 * @param string $apikey Unique API key generated in customer control panel.
	 * @param string $apisessionid Unique API session id created by login command.
	 * @param clientrequestid $clientrequestid Id from client side. Can contain letters and numbers. Field is optional.
	 * @return Responsemessage $responsemessage with information about result of the action like short and long resultmessages, message status, etc.
	 */
	public function changeOwnerDomain($new_handle_id,$domainname,$customernumber,$apikey,$apisessionid,$clientrequestid){
		return self::_Call('changeOwnerDomain', func_get_args());
	}

	/**
	 * Cancel Domain. Current Owner has to allow or deny the termination by clicking a link that is sent to him via e-mail.
	 * Process ends after 5 days if not answered.
	 * Inclusive domains that were ordered with a hosting product have to be canceled with this product.
	 * This function is avaliable for domain resellers.
	 *
	 * @param domainname $domainname Name of the domain including top-level domain.
	 * @param positiveInteger $customernumber customer number of reseller at netcup.
	 * @param string $apikey Unique API key generated in customer control panel.
	 * @param string $apisessionid Unique API session id created by login command.
	 * @param clientrequestid $clientrequestid Id from client side. Can contain letters and numbers. Field is optional.
	 * @return Responsemessage $responsemessage with information about result of the action like short and long resultmessages, message status, etc.
	 */
	public function cancelDomain($domainname,$customernumber,$apikey,$apisessionid,$clientrequestid){
		return self::_Call('cancelDomain', func_get_args());
	}

	/**
	 * Info Domain. Get Information about domain. All avaliable information for own domains. Status for other domains.
	 * This function is avaliable for domain resellers.
	 *
	 * @param domainname $domainname Name of the domain including top-level domain.
	 * @param positiveInteger $customernumber customer number of reseller at netcup.
	 * @param string $apikey Unique API key generated in customer control panel.
	 * @param string $apisessionid Unique API session id created by login command.
	 * @param clientrequestid $clientrequestid Id from client side. Can contain letters and numbers. Field is optional.
	 * @param registryinformationflag $registryinformationflag TRUE getinformation from registry (may be limited)|registryinformationflag FALSE get information from netcup data base only.  Field is optional. Default FALSE.
	 * @return Responsemessage $responsemessage Responsemessage with information about result of the action like short and long resultmessages, message status, etc.
	 */
	public function infoDomain($domainname,$customernumber,$apikey,$apisessionid,$clientrequestid,$registryinformationflag){
		return self::_Call('infoDomain', func_get_args());
	}

	/**
	 * Get auth info for domain.
	 * This function is avaliable for domain resellers.
	 *
	 * @param domainname $domainname Name of the domain including top-level domain.
	 * @param positiveInteger $customernumber customer number of reseller at netcup.
	 * @param string $apikey Unique API key generated in customer control panel.
	 * @param string $apisessionid Unique API session id created by login command.
	 * @param clientrequestid $clientrequestid Id from client side. Can contain letters and numbers. Field is optional.
	 * @return Responsemessage $responsemessage with information about result of the action like short and long resultmessages, message status, etc.
	 */
	public function getAuthcodeDomain($domainname,$customernumber,$apikey,$apisessionid,$clientrequestid){
		return self::_Call('getAuthcodeDomain', func_get_args());
	}

	/**
	 * Get Information about a handle.
	 * This function is avaliable for domain resellers.
	 *
	 * @param positiveInteger $handle_id Id of the contact
	 * @param positiveInteger $customernumber customer number of reseller at netcup.
	 * @param string $apikey Unique API key generated in customer control panel.
	 * @param string $apisessionid Unique API session id created by login command.
	 * @param clientrequestid $clientrequestid Id from client side. Can contain letters and numbers. Field is optional.
	 * @return Responsemessage $responsemessage with information about result of the action like short and long resultmessages, message status, etc.
	 */
	public function infoHandle($handle_id,$customernumber,$apikey,$apisessionid,$clientrequestid){
		return self::_Call('infoHandle', func_get_args());
	}

	/**
	 * Get ids and name of all handles of a user. If Organisation is set, also value of organisation field.
	 * This function is avaliable for domain resellers.
	 *
	 * @param positiveInteger $customernumber customer number of reseller at netcup.
	 * @param string $apikey Unique API key generated in customer control panel.
	 * @param string $apisessionid Unique API session id created by login command.
	 * @param clientrequestid $clientrequestid Id from client side. Can contain letters and numbers. Field is optional.
	 * @return Responsemessage $responsemessage with information about result of the action like short and long resultmessages, message status, etc.
	 */
	public function listallHandle($customernumber,$apikey,$apisessionid,$clientrequestid){
		return self::_Call('listallHandle', func_get_args());
	}

	/**
	 * Delete a contact handle in data base.
	 * You can only delete a handle in the netcup database, if it is not used with a domain.
	 * This function is avaliable for domain resellers.
	 *
	 * @param positiveInteger $handle_id Id of the contact.
	 * @param positiveInteger $customernumber customer number of reseller at netcup.
	 * @param string $apikey Unique API key generated in customer control panel.
	 * @param string $apisessionid Unique API session id created by login command.
	 * @param clientrequestid $clientrequestid Id from client side. Can contain letters and numbers. Field is optional.
	 * @return Responsemessage $responsemessage with information about result of the action like short and long resultmessages, message status, etc.
	 */
	public function deleteHandle($handle_id,$customernumber,$apikey,$apisessionid,$clientrequestid){
		return self::_Call('deleteHandle', func_get_args());
	}

	/**
	 * Update a existing contact handle in data base and at registries where it is used.
	 * Handle is created at a registry as soon as it is used.
	 * This function is avaliable for domain resellers.
	 *
	 * @param positiveInteger $customernumber customer number of reseller at netcup.
	 * @param string $apikey Unique API key generated in customer control panel.
	 * @param string $apisessionid Unique API session id created by login command.
	 * @param positiveInteger $handle_id Id of the contact that will be updated.
	 * @param handletyp $type type of the handle like organisation or person
	 * @param max80chars $name full name of the contact.
	 * @param organisation $organisation organisation like company name.
	 * @param max63chars $street street
	 * @param max12chars $postalcode postcode
	 * @param max63chars $city city
	 * @param countrycode2char $countrycode countrycode in ISO 3166 ALPHA-2 format. 2 char codes like CH for Switzerland.
	 * @param telephone $telephone telephone number
	 * @param email $email email address
	 * @param clientrequestid $clientrequestid Id from client side. Can contain letters and numbers. Field is optional.
	 * @param ArrayOfOptionalhandleattributes $optionalhandleattributes Optional handle attributes as array of Optionalhandleattribute. Please define in format for example: $optionalhandleattribute = new Optionalhandleattribute(); $optionalhandleattribute->item = 'state'; $optionalhandleattribute->value = utf8_encode("Bayern"); $optionalhandleattributes[] = $optionalhandleattribute; $optionalhandleattribute = new Optionalhandleattribute(); $optionalhandleattribute->item = 'handlecomment'; $optionalhandleattribute->value = utf8_encode("Important customer"); $optionalhandleattributes[] = $optionalhandleattribute; Possible values are: "fax" fax number in format +49.12345678, "state" max40chars state for example Sachsen, "handlecomment" max80chars comment for the handle. Value will not be transfered to any registry., "birthdate" dateformatyyyymmdd date of birth in format YYYY-MM-DD, "birthplace" max70chars place of birth, "birthcountrycountrycode" countrycode2char country of birth, "birthstate" max63chars state of birth, "birthplacepostalcode" max12chars place of the postcode, "registrationnumber" max70chars registernumber, "idcardnumber" max70chars passportnumber, "idcardissuedate" dateformatyyyymmdd passport date of issue in format YYYY-MM-DD, "idcardissueauthority" max70chars passport issuing authority, "taxnumber" max70chars tax number, "vatnumber" max70chars sales tax number, "aeroensid" max70chars aeroensid mandatory for .aero domains, "aeroenspassword" max70chars aeroenspassword mandatory for .aero domains, "xxxmemberid" max20chars xxxmemberid mandatory for .xxx domains, "xxxmemberpasswort" max20chars xxxmemberpasswort mandatory for .xxx domains, "proprofession" max70chars profession mandatory for .pro domains, "traveluin" max20chars traveluin mandatory for .travel domains, "trademarknumber" max80chars brandnumber, "trademarkcountrycode" countrycode2char registry place of the brand, "coopverificationcode" max20chars coopverificationcode, "asiatypeofentity" asiatypeofentity type of legal peronality mandatory for .asia domains. Allowed values are: naturalPerson, corporation, cooperative, partnership, government, politicalParty, society, institution, "asiaformofidentity" asiaformofidentity form of identity mandatory for .asia domains Allowed values are: certificate, legislation, passport, politicalPartyRegistry, societyRegistry, "asiaidentnumber" max255chars identificationnumber mandatory for .asia domains, "jobstitelposition" max70chars title or position mandatory for .jobs domains, "jobswebsite" url URL of website mandatory for .jobs domains, "jobsindustrytype" max128chars type of company mandatory for .jobs domains, "jobscontactisadmin" yesno Yes or No mandatory for .jobs domains, "jobsassociationmember" yesno Yes or No mandatory for .jobs domains, "esnumbertype" esnumbertype NIF/NIE/DNI-number type mandatory for .es domains, "esnifnienumber" max70chars NIF/NIE/DNI-number mandatory for .es domains
	 * @return Responsemessage $responsemessage with information about result of the action like short and long resultmessages, message status, etc.
	 */
	public function updateHandle($customernumber,$apikey,$apisessionid,$handle_id,$type,$name,$organisation,$street,$postalcode,$city,$countrycode,$telephone,$email,$clientrequestid,$optionalhandleattributes){
		return self::_Call('updateHandle', func_get_args());
	}

	/**
	 * Create a contact handle in data base. Contact handles are mandatory for ordering domains.
	 * Fields type, name and organisation can not be changed by an update.
	 * Field email can not be changed if domain is used at a global top-level domain.
	 * This function is avaliable for domain resellers.
	 *
	 * @param positiveInteger $customernumber customer number of reseller at netcup.
	 * @param string $apikey Unique API key generated in customer control panel.
	 * @param string $apisessionid Unique API session id created by login command.
	 * @param handletyp $type type of the handle like organisation or person
	 * @param max80chars $name full name of the contact.
	 * @param organisation $organisation organisation like company name.
	 * @param max63chars $street street
	 * @param max12chars $postalcode postal code
	 * @param max63chars $city city
	 * @param countrycode2char $countrycode countrycode in ISO 3166 ALPHA-2 format. 2 char codes like CH for Switzerland.
	 * @param telephone $telephone telephone number
	 * @param email $email email address
	 * @param clientrequestid $clientrequestid Id from client side. Can contain letters and numbers. Field is optional.
	 * @param ArrayOfOptionalhandleattributes $optionalhandleattributes Optional handle attributes as array of Optionalhandleattribute. Please define in format for example: $optionalhandleattribute = new Optionalhandleattribute(); $optionalhandleattribute->item = 'state'; $optionalhandleattribute->value = utf8_encode("Bayern"); $optionalhandleattributes[] = $optionalhandleattribute; $optionalhandleattribute = new Optionalhandleattribute(); $optionalhandleattribute->item = 'handlecomment'; $optionalhandleattribute->value = utf8_encode("Important customer"); $optionalhandleattributes[] = $optionalhandleattribute; Possible values are: "fax" fax number in format +49.12345678, "state" max40chars state for example Sachsen, "handlecomment" max80chars comment for the handle. Value will not be transfered to any registry., "birthdate" dateformatyyyymmdd date of birth in format YYYY-MM-DD, "birthplace" max70chars place of birth, "birthcountrycountrycode" countrycode2char country of birth, "birthstate" max63chars state of birth, "birthplacepostalcode" max12chars place of the postcode, "registrationnumber" max70chars registernumber, "idcardnumber" max70chars passportnumber, "idcardissuedate" dateformatyyyymmdd passport date of issue in format YYYY-MM-DD, "idcardissueauthority" max70chars passport issuing authority, "taxnumber" max70chars tax number, "vatnumber" max70chars sales tax number, "aeroensid" max70chars aeroensid mandatory for .aero domains, "aeroenspassword" max70chars aeroenspassword mandatory for .aero domains, "xxxmemberid" max20chars xxxmemberid mandatory for .xxx domains, "xxxmemberpasswort" max20chars xxxmemberpasswort mandatory for .xxx domains, "proprofession" max70chars profession mandatory for .pro domains, "traveluin" max20chars traveluin mandatory for .travel domains, "trademarknumber" max80chars brandnumber, "trademarkcountrycode" countrycode2char registry place of the brand, "coopverificationcode" max20chars coopverificationcode, "asiatypeofentity" asiatypeofentity type of legal peronality mandatory for .asia domains. Allowed values are: naturalPerson, corporation, cooperative, partnership, government, politicalParty, society, institution, "asiaformofidentity" asiaformofidentity form of identity mandatory for .asia domains Allowed values are: certificate, legislation, passport, politicalPartyRegistry, societyRegistry, "asiaidentnumber" max255chars identificationnumber mandatory for .asia domains, "jobstitelposition" max70chars title or position mandatory for .jobs domains, "jobswebsite" url URL of website mandatory for .jobs domains, "jobsindustrytype" max128chars type of company mandatory for .jobs domains, "jobscontactisadmin" yesno Yes or No mandatory for .jobs domains, "jobsassociationmember" yesno Yes or No mandatory for .jobs domains, "esnumbertype" esnumbertype NIF/NIE/DNI-number type mandatory for .es domains, "esnifnienumber" max70chars NIF/NIE/DNI-number mandatory for .es domains
	 * @return Responsemessage $responsemessage with information about result of the action like short and long resultmessages, message status, etc.
	 */
	public function createHandle($customernumber,$apikey,$apisessionid,$type,$name,$organisation,$street,$postalcode,$city,$countrycode,$telephone,$email,$clientrequestid,$optionalhandleattributes){
		return self::_Call('createHandle', func_get_args());
	}

	/**
	 * Get information about all domains that a customer owns. For detailed information please use infoDomain
	 * This function is avaliable for domain resellers.
	 *
	 * @param positiveInteger $customernumber customer number of reseller at netcup.
	 * @param string $apikey Unique API key generated in customer control panel.
	 * @param string $apisessionid Unique API session id created by login command.
	 * @param clientrequestid $clientrequestid Id from client side. Can contain letters and numbers. Field is optional.
	 * @return Responsemessage $responsemessage with information about result of the action like short and long resultmessages, message status, etc. Contains DomainObject objects in responsedata field.
	 */
	public function listallDomains($customernumber,$apikey,$apisessionid,$clientrequestid){
		return self::_Call('listallDomains', func_get_args());
	}

	/**
	 * Get price for a top-level domain.
	 * Current discounts are considered, but can be limited by time or amount.
	 * Prices for premium domains can be higher.
	 * This function is avaliable for domain resellers.
	 *
	 * @param positiveInteger $customernumber customer number of reseller at netcup.
	 * @param string $apikey Unique API key generated in customer control panel.
	 * @param string $apisessionid Unique API session id created by login command.
	 * @param topleveldomain $topleveldomain Name of the top-level domain without dot. For example de.
	 * @param clientrequestid $clientrequestid Id from client side. Can contain letters and numbers. Field is optional.
	 * @return Responsemessage $responsemessage with information about result of the action like short and long resultmessages, message status, etc. Contains TopleveldomainObject objects in responsedata field.
	 */
	public function priceTopleveldomain($customernumber,$apikey,$apisessionid,$topleveldomain,$clientrequestid){
		return self::_Call('priceTopleveldomain', func_get_args());
	}

	/**
	 * Get all records of a zone.
	 * Zone must be owned by customer.
	 *
	 * @param domainname $domainname Name of the domain including top-level domain.
	 * @param positiveInteger $customernumber customer number of reseller at netcup.
	 * @param string $apikey Unique API key generated in customer control panel.
	 * @param string $apisessionid Unique API session id created by login command.
	 * @param clientrequestid $clientrequestid Id from client side. Can contain letters and numbers. Field is optional.
	 * @return Responsemessage $responsemessage with information about result of the action like short and long resultmessages, message status, etc.
	 */
	public function infoDnsRecords($domainname,$customernumber,$apikey,$apisessionid,$clientrequestid){
		return self::_Call('infoDnsRecords', func_get_args());
	}

	/**
	 * Update DNS records of a zone. Deletion of other records is optional.
	 * When DNSSEC is active, the zone is updated in the nameserver with zone resign after a few minutes.
	 *
	 * @param domainname $domainname Name of the domain including top-level domain.
	 * @param positiveInteger $customernumber customer number of reseller at netcup.
	 * @param string $apikey Unique API key generated in customer control panel.
	 * @param string $apisessionid Unique API session id created by login command.
	 * @param clientrequestid $clientrequestid Id from client side. Can contain letters and numbers. Field is optional.
	 * @param Dnsrecordset $dnsrecordset Object that contains DNS Records.
	 * @return Responsemessage $responsemessage with information about result of the action like short and long resultmessages, message status, etc.
	 */
	public function updateDnsRecords($domainname,$customernumber,$apikey,$apisessionid,$clientrequestid,$dnsrecordset){
		return self::_Call('updateDnsRecords', func_get_args());
	}

	/**
	 * Update DNS zone.
	 * When DNSSEC is active, the zone is updated in the nameserver with zone resign after a few minutes.
	 *
	 * @param domainname $domainname Name of the domain including top-level domain.
	 * @param positiveInteger $customernumber customer number of reseller at netcup.
	 * @param string $apikey Unique API key generated in customer control panel.
	 * @param string $apisessionid Unique API session id created by login command.
	 * @param clientrequestid $clientrequestid Id from client side. Can contain letters and numbers. Field is optional.
	 * @param Dnszone $dnszone Object that contains settings for DNS zone.
	 * @return Responsemessage $responsemessage with information about result of the action like short and long resultmessages, message status, etc.
	 */
	public function updateDnsZone($domainname,$customernumber,$apikey,$apisessionid,$clientrequestid,$dnszone){
		return self::_Call('updateDnsZone', func_get_args());
	}

	/**
	 * Get information about dns zone in local nameservers.
	 * Zone must be owned by reseller.
	 *
	 * @param domainname $domainname Name of the domain including top-level domain.
	 * @param positiveInteger $customernumber customer number of reseller at netcup.
	 * @param string $apikey Unique API key generated in customer control panel.
	 * @param string $apisessionid Unique API session id created by login command.
	 * @param clientrequestid $clientrequestid Id from client side. Can contain letters and numbers. Field is optional.
	 * @return Responsemessage $responsemessage with information about result of the action like short and long resultmessages, message status, etc.
	 */
	public function infoDnsZone($domainname,$customernumber,$apikey,$apisessionid,$clientrequestid){
		return self::_Call('infoDnsZone', func_get_args());
	}
}

/**
 * Nameserver entry.
 * Hostname is mandatory.
 *
 * @pw_element string $hostname Hostname of the nameserver
 * @pw_element string $ipv4 IPv4 address
 * @pw_element string $ipv6 IPv6 address
 * @pw_complex Nameserverentry
 */
class Nameserverentry{
	/**
	 * Hostname of the nameserver
	 *
	 * @var string
	 */
	public $hostname;
	/**
	 * IPv4 address
	 *
	 * @var string
	 */
	public $ipv4;
	/**
	 * IPv6 address
	 *
	 * @var string
	 */
	public $ipv6;
}

/**
 * Contains Nameserverentry objects.
 * At least two different nameserver needed.
 *
 * @pw_element Nameserverentry $nameserver1 Object with information about nameserver.
 * @pw_element Nameserverentry $nameserver2 Object with information about nameserver.
 * @pw_element Nameserverentry $nameserver3 Object with information about nameserver.
 * @pw_element Nameserverentry $nameserver4 Object with information about nameserver.
 * @pw_element Nameserverentry $nameserver5 Object with information about nameserver.
 * @pw_element Nameserverentry $nameserver6 Object with information about nameserver.
 * @pw_element Nameserverentry $nameserver7 Object with information about nameserver.
 * @pw_element Nameserverentry $nameserver8 Object with information about nameserver.
 * @pw_complex Nameserverentries
 */
class Nameserverentries{
	/**
	 * Object with information about nameserver.
	 *
	 * @var Nameserverentry
	 */
	public $nameserver1;
	/**
	 * Object with information about nameserver.
	 *
	 * @var Nameserverentry
	 */
	public $nameserver2;
	/**
	 * Object with information about nameserver.
	 *
	 * @var Nameserverentry
	 */
	public $nameserver3;
	/**
	 * Object with information about nameserver.
	 *
	 * @var Nameserverentry
	 */
	public $nameserver4;
	/**
	 * Object with information about nameserver.
	 *
	 * @var Nameserverentry
	 */
	public $nameserver5;
	/**
	 * Object with information about nameserver.
	 *
	 * @var Nameserverentry
	 */
	public $nameserver6;
	/**
	 * Object with information about nameserver.
	 *
	 * @var Nameserverentry
	 */
	public $nameserver7;
	/**
	 * Object with information about nameserver.
	 *
	 * @var Nameserverentry
	 */
	public $nameserver8;
}

/**
 * Response message of a request send to the api.
 *
 * @pw_element string $serverrequestid Unique ID for the request, created by the server
 * @pw_element string $clientrequestid Unique ID for the request, created by the client
 * @pw_element string $action Name of the function that was called.
 * @pw_element string $status Staus of the Message like "error", "started", "pending", "warning" or "success".
 * @pw_element positiveInteger $statuscode Staus code of the Message like 2011.
 * @pw_element string $shortmessage Short message with information about the processing of the messsage.
 * @pw_element string $longmessage Long message with information about the processing of the messsage.
 * @pw_element string $responsedata Data from the response like domain object.
 * @pw_complex Responsemessage
 */
class Responsemessage{
	/**
	 * Unique ID for the request, created by the server
	 *
	 * @var string
	 */
	public $serverrequestid;
	/**
	 * Unique ID for the request, created by the client
	 *
	 * @var string
	 */
	public $clientrequestid;
	/**
	 * Name of the function that was called.
	 *
	 * @var string
	 */
	public $action;
	/**
	 * Staus of the Message like "error", "started", "pending", "warning" or "success".
	 *
	 * @var string
	 */
	public $status;
	/**
	 * Staus code of the Message like 2011.
	 *
	 * @var positiveInteger
	 */
	public $statuscode;
	/**
	 * Short message with information about the processing of the messsage.
	 *
	 * @var string
	 */
	public $shortmessage;
	/**
	 * Long message with information about the processing of the messsage.
	 *
	 * @var string
	 */
	public $longmessage;
	/**
	 * Data from the response like domain object.
	 *
	 * @var string
	 */
	public $responsedata;
}

/**
 * Domain Object
 *
 * @pw_element string $domainname Name of the domain.
 * @pw_element Nameserverentries $nameserverentry nameserserver1 to nameserver8 with Nameserverentry objects.
 * @pw_element positiveInteger $customernumber customer number of reseller at netcup.
 * @pw_element Contactentries $assignedcontacts Ids of the contact handles for each contact type.
 * @pw_element boolean $ownerchangerunning TRUE when ownerchange has to be confirmed|boolean FALSE no ownerchange is running.
 * @pw_element boolean $cancellationrunning TRUE when cancellation has to be confirmed|boolean FALSE no cancellation is running
 * @pw_element string $nextbilling Date when domain is billed the next time in format YYYY-MM-DD
 * @pw_element positiveInteger $runtimemonths Runtime in months for this domain.
 * @pw_element string $lastupdate Information when the domain was updated last time at the registry. Not avaliable for all registries.
 * @pw_element string $domaincreated Information when the domain was created at the registry. Not avaliable for all registries.
 * @pw_element string $deletiondate Information when the domain is going to be deleted at the registry. Not avaliable for all registries.
 * @pw_element string $authcode AuthInfo code for this domain.
 * @pw_element string $state Domain can be inclusive or additional
 * @pw_element Registrycontacts $registrycontacts Handle Names like ABC12365445 or DENIC-415-R-1351 for each contact type.
 * @pw_element float $priceperruntime Price for next billing of the domain.
 * @pw_element dnssectype $dnssectype Type of the DNSSEC records to provide. Valid values are "unknown", "digest", "publickey" or "unavailable".
 * @pw_element DnssecentriesObject $dnssecentries Dnssecentries for this domain (optional).
 * @pw_complex DomainObject
 */
class DomainObject{
	/**
	 * Name of the domain.
	 *
	 * @var string
	 */
	public $domainname;
	/**
	 * nameserserver1 to nameserver8 with Nameserverentry objects.
	 *
	 * @var Nameserverentries
	 */
	public $nameserverentry;
	/**
	 * customer number of reseller at netcup.
	 *
	 * @var positiveInteger
	 */
	public $customernumber;
	/**
	 * Ids of the contact handles for each contact type.
	 *
	 * @var Contactentries
	 */
	public $assignedcontacts;
	/**
	 * TRUE when ownerchange has to be confirmed|boolean FALSE no ownerchange is running.
	 *
	 * @var boolean
	 */
	public $ownerchangerunning;
	/**
	 * TRUE when cancellation has to be confirmed|boolean FALSE no cancellation is running
	 *
	 * @var boolean
	 */
	public $cancellationrunning;
	/**
	 * Date when domain is billed the next time in format YYYY-MM-DD
	 *
	 * @var string
	 */
	public $nextbilling;
	/**
	 * Runtime in months for this domain.
	 *
	 * @var positiveInteger
	 */
	public $runtimemonths;
	/**
	 * Information when the domain was updated last time at the registry. Not avaliable for all registries.
	 *
	 * @var string
	 */
	public $lastupdate;
	/**
	 * Information when the domain was created at the registry. Not avaliable for all registries.
	 *
	 * @var string
	 */
	public $domaincreated;
	/**
	 * Information when the domain is going to be deleted at the registry. Not avaliable for all registries.
	 *
	 * @var string
	 */
	public $deletiondate;
	/**
	 * AuthInfo code for this domain.
	 *
	 * @var string
	 */
	public $authcode;
	/**
	 * Domain can be inclusive or additional
	 *
	 * @var string
	 */
	public $state;
	/**
	 * Handle Names like ABC12365445 or DENIC-415-R-1351 for each contact type.
	 *
	 * @var Registrycontacts
	 */
	public $registrycontacts;
	/**
	 * Price for next billing of the domain.
	 *
	 * @var float
	 */
	public $priceperruntime;
	/**
	 * Type of the DNSSEC records to provide. Valid values are "unknown", "digest", "publickey" or "unavailable".
	 *
	 * @var dnssectype
	 */
	public $dnssectype;
	/**
	 * Dnssecentries for this domain (optional).
	 *
	 * @var DnssecentriesObject
	 */
	public $dnssecentries;
}

/**
 * Handle Names like ABC12365445 or DENIC-415-R-1351
 *
 * @pw_element string $ownerc name Name of handle at registry.
 * @pw_element string $adminc name Name of handle at registry.
 * @pw_element string $techc name Name of handle at registry.
 * @pw_element string $zonec name Name of handle at registry.
 * @pw_element string $billingc name Name of handle at registry.
 * @pw_element string $onsitec name Name of handle at registry.
 * @pw_complex Registrycontacts
 */
class Registrycontacts{
	/**
	 * name Name of handle at registry.
	 *
	 * @var string
	 */
	public $ownerc;
	/**
	 * name Name of handle at registry.
	 *
	 * @var string
	 */
	public $adminc;
	/**
	 * name Name of handle at registry.
	 *
	 * @var string
	 */
	public $techc;
	/**
	 * name Name of handle at registry.
	 *
	 * @var string
	 */
	public $zonec;
	/**
	 * name Name of handle at registry.
	 *
	 * @var string
	 */
	public $billingc;
	/**
	 * name Name of handle at registry.
	 *
	 * @var string
	 */
	public $onsitec;
}



/**
 * An optional handle attribute.
 *
 * @pw_element string $item name of the optional attribute
 * @pw_element string $value Value of optional attribute
 * @pw_complex Optionalhandleattribute
 */
class Optionalhandleattribute{
	/**
	 * name of the optional attribute
	 *
	 * @var string
	 */
	public $item;
	/**
	 * Value of optional attribute
	 *
	 * @var string
	 */
	public $value;
}

/**
 * Contact handle
 *
 * @pw_element positiveInteger $id
 * @pw_element string $type type of the handle like organisation or person
 * @pw_element string $name name of the contact.
 * @pw_element string $organisation organisation like company name.
 * @pw_element string $street street
 * @pw_element string $postalcode postal code
 * @pw_element string $city city
 * @pw_element string $countrycode countrycode in ISO 3166 ALPHA-2 format. 2 char codes like CH for Switzerland.
 * @pw_element string $telephone telephone number
 * @pw_element string $email email address
 * @pw_element ArrayOfOptionalhandleattributes $optionalhandleattributes Optional handle attributes in a array of type Optionalhandleattribute
 * @pw_element boolean $assignedtodomain is handle in use.
 * @pw_complex HandleObject
 */
class HandleObject{
	/**
	 * @var positiveInteger
	 */
	public $id;
	/**
	 * type of the handle like organisation or person
	 *
	 * @var string
	 */
	public $type;
	/**
	 * name of the contact.
	 *
	 * @var string
	 */
	public $name;
	/**
	 * organisation like company name.
	 *
	 * @var string
	 */
	public $organisation;
	/**
	 * street
	 *
	 * @var string
	 */
	public $street;
	/**
	 * postal code
	 *
	 * @var string
	 */
	public $postalcode;
	/**
	 * city
	 *
	 * @var string
	 */
	public $city;
	/**
	 * countrycode in ISO 3166 ALPHA-2 format. 2 char codes like CH for Switzerland.
	 *
	 * @var string
	 */
	public $countrycode;
	/**
	 * telephone number
	 *
	 * @var string
	 */
	public $telephone;
	/**
	 * email address
	 *
	 * @var string
	 */
	public $email;
	/**
	 * Optional handle attributes in a array of type Optionalhandleattribute
	 *
	 * @var ArrayOfOptionalhandleattributes
	 */
	public $optionalhandleattributes;
	/**
	 * is handle in use.
	 *
	 * @var boolean
	 */
	public $assignedtodomain;
}




/**
 * Handle Ids of contacts.
 *
 * @pw_element string $ownerc Id of contact handle.
 * @pw_element string $adminc Id of contact handle.
 * @pw_element string $techc Id of contact handle.
 * @pw_element string $zonec Id of contact handle.
 * @pw_element string $billingc Id of contact handle.
 * @pw_element string $onsitec Id of contact handle.
 * @pw_complex Contactentries
 */
class Contactentries{
	/**
	 * Id of contact handle.
	 *
	 * @var string
	 */
	public $ownerc;
	/**
	 * Id of contact handle.
	 *
	 * @var string
	 */
	public $adminc;
	/**
	 * Id of contact handle.
	 *
	 * @var string
	 */
	public $techc;
	/**
	 * Id of contact handle.
	 *
	 * @var string
	 */
	public $zonec;
	/**
	 * Id of contact handle.
	 *
	 * @var string
	 */
	public $billingc;
	/**
	 * Id of contact handle.
	 *
	 * @var string
	 */
	public $onsitec;
}

/**
 * Object that is returned after successful login.
 *
 * @pw_element string $apisessionid Unique API session id created by login command.
 * @pw_complex SessionObject
 */
class SessionObject{
	/**
	 * Unique API session id created by login command.
	 *
	 * @var string
	 */
	public $apisessionid;
}

/**
 * Object that is returned after a poll command.
 *
 * @pw_element positiveInteger $id id
 * @pw_element string $action action
 * @pw_element string $status status
 * @pw_element positiveInteger $statuscode statuscode
 * @pw_element string $shortmessage shortmessage
 * @pw_element string $longmessage longmessage
 * @pw_element string $apikey apikey
 * @pw_element string $serverrequestid serverrequestid
 * @pw_element string $clientrequestid clientrequestid
 * @pw_element string $requestdatetime requestdatetime
 * @pw_element string $domainorhandle domainorhandle
 * @pw_element string $messageformat messageformat
 * @pw_element string $apisessionid apisessionid
 * @pw_complex PollObject
 */
class PollObject{
	/**
	 * id
	 *
	 * @var positiveInteger
	 */
	public $id;
	/**
	 * action
	 *
	 * @var string
	 */
	public $action;
	/**
	 * status
	 *
	 * @var string
	 */
	public $status;
	/**
	 * statuscode
	 *
	 * @var positiveInteger
	 */
	public $statuscode;
	/**
	 * shortmessage
	 *
	 * @var string
	 */
	public $shortmessage;
	/**
	 * longmessage
	 *
	 * @var string
	 */
	public $longmessage;
	/**
	 * apikey
	 *
	 * @var string
	 */
	public $apikey;
	/**
	 * serverrequestid
	 *
	 * @var string
	 */
	public $serverrequestid;
	/**
	 * clientrequestid
	 *
	 * @var string
	 */
	public $clientrequestid;
	/**
	 * requestdatetime
	 *
	 * @var string
	 */
	public $requestdatetime;
	/**
	 * domainorhandle
	 *
	 * @var string
	 */
	public $domainorhandle;
	/**
	 * messageformat
	 *
	 * @var string
	 */
	public $messageformat;
	/**
	 * apisessionid
	 *
	 * @var string
	 */
	public $apisessionid;
}

/**
 * Object that conaints information about a top-level domain.
 *
 * @pw_element topleveldomain $topleveldomainname Name of the top-level domain.
 * @pw_element float $priceperruntime Price for next billing of the domain.
 * @pw_element float $setupfee Set-up fee paid once.
 * @pw_element positiveInteger $runtimemonths Runtime in months for this domain.
 * @pw_complex TopleveldomainObject
 */
class TopleveldomainObject{
	/**
	 * Name of the top-level domain.
	 *
	 * @var topleveldomain
	 */
	public $topleveldomainname;
	/**
	 * Price for next billing of the domain.
	 *
	 * @var float
	 */
	public $priceperruntime;
	/**
	 * Set-up fee paid once.
	 *
	 * @var float
	 */
	public $setupfee;
	/**
	 * Runtime in months for this domain.
	 *
	 * @var positiveInteger
	 */
	public $runtimemonths;
}

/**
 * DNSSEC Entry
 * Use infoDomain to find out which DNSSEC Type is needed.
 *
 * @pw_element dnssectype $dnssectype Type of the DNSSEC records to provide (digest or publickey).
 * @pw_element publickey $publickey Public key of DNSKEY or digest key for digest keys. Base64 hash of public dnssec key or ds record
 * @pw_element integer $flags Identifies Type of DNSKEY for example 257 or digesttype for digest keys.
 * @pw_element positiveInteger $algorithm Code number for used algorithm
 * @pw_element positiveInteger $keytag Keytag
 * @pw_complex DnssecentryObject
 */
class DnssecentryObject{
	/**
	 * Type of the DNSSEC records to provide (digest or publickey).
	 *
	 * @var dnssectype
	 */
	public $dnssectype;
	/**
	 * Public key of DNSKEY or digest key for digest keys. Base64 hash of public dnssec key or ds record
	 *
	 * @var publickey
	 */
	public $publickey;
	/**
	 * Identifies Type of DNSKEY for example 257 or digesttype for digest keys.
	 *
	 * @var integer
	 */
	public $flags;
	/**
	 * Code number for used algorithm
	 *
	 * @var positiveInteger
	 */
	public $algorithm;
	/**
	 * Keytag
	 *
	 * @var positiveInteger
	 */
	public $keytag;
}

/**
 * Contains DnssecentryObject objects.
 *
 * @pw_element DnssecentryObject $dnssecentry1 Dnssec entry
 * @pw_element DnssecentryObject $dnssecentry2 Dnssec entry
 * @pw_element DnssecentryObject $dnssecentry3 Dnssec entry
 * @pw_element DnssecentryObject $dnssecentry4 Dnssec entry
 * @pw_element DnssecentryObject $dnssecentry5 Dnssec entry
 * @pw_element DnssecentryObject $dnssecentry6 Dnssec entry
 * @pw_element DnssecentryObject $dnssecentry7 Dnssec entry
 * @pw_element DnssecentryObject $dnssecentry9 Dnssec entry
 * @pw_element DnssecentryObject $dnssecentry10 Dnssec entry
 * @pw_element DnssecentryObject $dnssecentry11 Dnssec entry
 * @pw_element DnssecentryObject $dnssecentry12 Dnssec entry
 * @pw_element DnssecentryObject $dnssecentry13 Dnssec entry
 * @pw_element DnssecentryObject $dnssecentry14 Dnssec entry
 * @pw_element DnssecentryObject $dnssecentry16 Dnssec entry
 * @pw_element DnssecentryObject $dnssecentry17 Dnssec entry
 * @pw_element DnssecentryObject $dnssecentry18 Dnssec entry
 * @pw_element DnssecentryObject $dnssecentry19 Dnssec entry
 * @pw_element DnssecentryObject $dnssecentry20 Dnssec entry
 * @pw_complex DnssecentriesObject
 */
class DnssecentriesObject{
	/**
	 * Dnssec entry
	 *
	 * @var DnssecentryObject
	 */
	public $dnssecentry1;
	/**
	 * Dnssec entry
	 *
	 * @var DnssecentryObject
	 */
	public $dnssecentry2;
	/**
	 * Dnssec entry
	 *
	 * @var DnssecentryObject
	 */
	public $dnssecentry3;
	/**
	 * Dnssec entry
	 *
	 * @var DnssecentryObject
	 */
	public $dnssecentry4;
	/**
	 * Dnssec entry
	 *
	 * @var DnssecentryObject
	 */
	public $dnssecentry5;
	/**
	 * Dnssec entry
	 *
	 * @var DnssecentryObject
	 */
	public $dnssecentry6;
	/**
	 * Dnssec entry
	 *
	 * @var DnssecentryObject
	 */
	public $dnssecentry7;
	/**
	 * Dnssec entry
	 *
	 * @var DnssecentryObject
	 */
	public $dnssecentry9;
	/**
	 * Dnssec entry
	 *
	 * @var DnssecentryObject
	 */
	public $dnssecentry10;
	/**
	 * Dnssec entry
	 *
	 * @var DnssecentryObject
	 */
	public $dnssecentry11;
	/**
	 * Dnssec entry
	 *
	 * @var DnssecentryObject
	 */
	public $dnssecentry12;
	/**
	 * Dnssec entry
	 *
	 * @var DnssecentryObject
	 */
	public $dnssecentry13;
	/**
	 * Dnssec entry
	 *
	 * @var DnssecentryObject
	 */
	public $dnssecentry14;
	/**
	 * Dnssec entry
	 *
	 * @var DnssecentryObject
	 */
	public $dnssecentry16;
	/**
	 * Dnssec entry
	 *
	 * @var DnssecentryObject
	 */
	public $dnssecentry17;
	/**
	 * Dnssec entry
	 *
	 * @var DnssecentryObject
	 */
	public $dnssecentry18;
	/**
	 * Dnssec entry
	 *
	 * @var DnssecentryObject
	 */
	public $dnssecentry19;
	/**
	 * Dnssec entry
	 *
	 * @var DnssecentryObject
	 */
	public $dnssecentry20;
}

/**
 * DNS record.
 *
 * @pw_element positiveInteger $id Unique of the record. Leave id empty for new records.
 * @pw_element string $hostname Hostname of the record. Use '@' for root of domain.
 * @pw_element recordtypes $type Type of Record like A or MX.
 * @pw_element string $priority Required for MX records.
 * @pw_element string $destination Target of the record.
 * @pw_element deleterecordflag $deleterecord TRUE when record will be deleted|boolean FALSE when record will persist
 * @pw_element string $state State of the record. Read only, inputs are ignored.
 * @pw_complex Dnsrecord
 */
class Dnsrecord{
	/**
	 * Unique of the record. Leave id empty for new records.
	 *
	 * @var positiveInteger
	 */
	public $id;
	/**
	 * Hostname of the record. Use '@' for root of domain.
	 *
	 * @var string
	 */
	public $hostname;
	/**
	 * Type of Record like A or MX.
	 *
	 * @var recordtypes
	 */
	public $type;
	/**
	 * Required for MX records.
	 *
	 * @var string
	 */
	public $priority;
	/**
	 * Target of the record.
	 *
	 * @var string
	 */
	public $destination;
	/**
	 * TRUE when record will be deleted|boolean FALSE when record will persist
	 *
	 * @var deleterecordflag
	 */
	public $deleterecord;
	/**
	 * State of the record. Read only, inputs are ignored.
	 *
	 * @var string
	 */
	public $state;
}

/**
 * DNS record set
 *
 * @pw_element ArrayOfDnsrecord $dnsrecords Array of DNS records for a zone.
 * @pw_complex Dnsrecordset
 */
class Dnsrecordset{
	/**
	 * Array of DNS records for a zone.
	 *
	 * @var ArrayOfDnsrecord
	 */
	public $dnsrecords;
}

/**
 * DNS zone
 *
 * @pw_element domainname $name Name of the zone - this is a domain name.
 * @pw_element positiveInteger $ttl time-to-live Time in seconds a domain name is cached locally before expiration and return to authoritative nameservers for updated information. Recommendation: 3600 to 172800
 * @pw_element positiveInteger $serial serial of zone. Readonly.
 * @pw_element positiveInteger $refresh Time in seconds a secondary name server waits to check for a new copy of a DNS zone. Recommendation: 3600 to 14400
 * @pw_element positiveInteger $retry Time in seconds primary name server waits if an attempt to refresh by a secondary name server failed. Recommendation: 900 to 3600
 * @pw_element positiveInteger $expire Time in seconds a secondary name server will hold a zone before it is no longer considered authoritative. Recommendation: 592200 to 1776600
 * @pw_element boolean $dnssecstatus Status of DNSSSEC in this nameserver. Enabling DNSSEC possible every 24 hours.
 * @pw_complex Dnszone
 */
class Dnszone{
	/**
	 * Name of the zone - this is a domain name.
	 *
	 * @var domainname
	 */
	public $name;
	/**
	 * time-to-live Time in seconds a domain name is cached locally before expiration and return to authoritative nameservers for updated information. Recommendation: 3600 to 172800
	 *
	 * @var positiveInteger
	 */
	public $ttl;
	/**
	 * serial of zone. Readonly.
	 *
	 * @var positiveInteger
	 */
	public $serial;
	/**
	 * Time in seconds a secondary name server waits to check for a new copy of a DNS zone. Recommendation: 3600 to 14400
	 *
	 * @var positiveInteger
	 */
	public $refresh;
	/**
	 * Time in seconds primary name server waits if an attempt to refresh by a secondary name server failed. Recommendation: 900 to 3600
	 *
	 * @var positiveInteger
	 */
	public $retry;
	/**
	 * Time in seconds a secondary name server will hold a zone before it is no longer considered authoritative. Recommendation: 592200 to 1776600
	 *
	 * @var positiveInteger
	 */
	public $expire;
	/**
	 * Status of DNSSSEC in this nameserver. Enabling DNSSEC possible every 24 hours.
	 *
	 * @var boolean
	 */
	public $dnssecstatus;
}