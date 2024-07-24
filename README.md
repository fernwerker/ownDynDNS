# ownDynDNS
Self-hosted dynamic DNS php-based Docker container to update netcup DNS API from consumer routers etc.  

## Authors
* Felix Kretschmer [@fernwerker](https://github.com/fernwerker)
* Philipp Tempel [@philipptempel](https://github.com/philipptempel)
* Branko Wilhelm [@b2un0](https://github.com/b2un0)
* Nils Blume [@niiwiicamo](https://github.com/niiwiicamo)

## Usage

This docker image only provides a basic http server as default. You should never expose this to the internet!
Use a reverse proxy or run everything locally.

### docker-compose.yaml
Take a look at docker-compose.yaml for inspiration.

#### The following environment variables are `required`:
env | description
---: | :--- 
DDNS_USER <br> DDNS_PASS | The username and password that the DynDNS client (e.g. your router) uses to authenticate to this container
NETCUP_APIKEY <br> NETCUP_APIPASS <br> NETCUP_CUSTOMERID | Your netcup credentials so this container can authorize against netcup


#### The following environment variables are `optional`:
env | default | description
---: | :--- | :---
DDNS_DEBUG | 0 | Includes debug information in the web response
DDNS_LOG | 1 | Creates a json log file
DDNS_LOGFILE | log.json | Log file location, relative to webroot
DDNS_RETURNIP | 1 | Returns the updated DNS record (IPv4, IPv6, TXT)
DDNS_ALLOWCREATE | 0 | Allows for a new DNS entry to be created and set instead of only updating existing
DDNS_RESTRICTDOMAIN | 0 | Allows you to override the DNS entry to be updated
DDNS_FORCEDDOMAIN | "" | When DDNS_RESTRICTDOMAIN is set, enter the registered domain name (e.g. example.com)
DDNS_FORCEDHOST | "" | When DDNS_RESTRICTDOMAIN is set, enter the DNS entry host name (e.g. _acme-challenge.test.home)


### URL contents:

#### The following parameters are supported

`You must include: user, password, domain and one of ipv4, ipv6 and txt`

parameter | example | description
---: | :--- | :---
user | dnsupdater | The DDNS_USER 
password | secretpleasechange | The DDNS_PASS
domain | `a)` home.example.com <br> `b)` example.com <br> `c)` example.com | `a)` The FQDN to update <br> `b)` The registered domain only, for multi part host names <br> `c)` The domain if you want to update the @ or * record
host | nas.home | optional; `case b)` If your domain contains more than 3 levels, e.g. "nas.home.example.com"
ipv4 | 1.2.3.4 | the ipv4 address to update a A record
ipv6 | fe80::12:34:56 | the ipv6 address to update a AAAA record
txt | acme-challenge-text | the content to update a TXT record
force | true | optional; ignore checking if the record needs to be updated, just do it anyways. Default: `false`
mode | * | optional; `case c)` If domain is your registered domain "example.com". Possible values: `*` or `both`. Default: `@`
create | true | optional; create all entries if none exist. e.g. will not create A if AAAA exists. Needs `DDNS_ALLOWCREATE=1`


#### Example URL to update A record (IPv4) of home.example.com:
https://`dyndns.example.com`/update.php?user=`username`&password=`password`&domain=`home.example.com`&ipv4=`IPv4`

#### Example URL to force update AAAA record (IPv6) of example.com:
https://`dyndns.example.com`/update.php?user=`username`&password=`password`&domain=`example.com`&ipv6=`IPv6`&force=`true`

#### Example URL to update A and AAAA records of home.example.com:
https://`dyndns.example.com`/update.php?user=`username`&password=`password`&domain=`home.example.com`&ipv4=`IPv4`&ipv6=`IPv6`

#### Example URL to update TXT record _acme-challenge of home.example.com:
https://`dyndns.example.com`/update.php?user=`username`&password=`password`&domain=`_acme-challenge.example.com`&txt=`textcontent`

#### Example URL to update A record of nas.home.example.com:
https://`dyndns.example.com`/update.php?user=`username`&password=`password`&domain=`example.com`&host=`nas.home`&ipv4=`IPv4`

#### Example URL to update AAAA wildcard record of example.com:
https://`dyndns.example.com`/update.php?user=`username`&password=`password`&domain=`example.com`&mode=`*` 

#### Example URL to create A and TXT records of new.example.com:
https://`dyndns.example.com`/update.php?user=`username`&password=`password`&domain=`new.example.com`&ipv4=`IPv4`&txt=`textcontent`&create=`true`


### AVM FRITZ!Box Settings
* Go to "Internet" -> "Freigaben" -> "DynDNS"
* Choose "Benutzerdefiniert"
* Update-URL: `https://<url of your webspace>/update.php?user=<username>&password=<pass>&ipv4=<ipaddr>&ipv6=<ip6addr>&domain=<domain>`
  * only the url needs to be adjusted, the rest is automatically filled by your AVM FRITZ!Box
  * http or https is possible if valid SSL certificate (e.g. Let's Encrypt)
* Single Domain:
  * Domainname: `<host record that is supposed to be updated>`
* Multiple Domains:
  * Domainname: `<first host record that is supposed to be updated>,<second host record that is supposed to be updated>,....`
* Username: `<DDNS_USER>`
* Password: `<DDNS_PASS>`

### Synology DSM Settings
* Go to "Control Panel" -> "External Access" -> "DDNS"
* Click on "Customize Provider" to create a profile for your own DDNS server
* Service Provider: This is the display name of your custom provider
* Update-URL: `https://<url of your webspace>/update.php?user=__USERNAME__&password=__PASSWORD__&ipv4=__MYIP__&domain=__HOSTNAME__`
  * Attention: The variables are delimited by two underscores
  * Currently Synology custom DDNS does not support IPv6, for whatever reason.
* Save your custom provider
* Click on "Add" to create a DDNS job
* Select your custom provider. Notice that an asterisk [*] has appeared in front of the name to signify that this is a custom provider.
* Hostname: `<host record that is supposed to be updated>`
* Username/Email: `<DDNS_USER>`
* Password/Key: `<DDNS_PASS>`
* External Address (IPv4): probably "Auto", uses Synology service to find own external IP
* External Address (IPv6): doesn't matter, currently not supported by Synology

### pfSense Settings
* Go to "Services" -> "Dynamic DNS"
* Click on "Add" to create a DDNS profile
* Service Type: "Custom"
* Interface to monitor: `<select you WAN interface>`
* Interface to send update from: `<select your WAN interface>`
* Update URL: `https://<url of your webspace>/update.php?user=<DDNS_USER>&password=<DDNS_PASS>&ipv4=%IP%&domain=<host record to update>`
* Leave all other fields empty / default

# run as cronjob on a **nix based device
* see [examples](./examples)

## References
* DNS API Documentation: https://ccp.netcup.net/run/webservice/servers/endpoint.php
* Source of dnsapi.php: https://ccp.netcup.net/run/webservice/servers/endpoint.php?PHPSOAPCLIENT

## License
Published under GNU General Public License v3.0  
Original: &copy; Felix Kretschmer, 2021
