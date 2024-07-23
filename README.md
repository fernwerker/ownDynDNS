# ownDynDNS
Self-hosted dynamic DNS php-based Docker container to update netcup DNS API from consumer routers etc.  

## Authors
* Felix Kretschmer [@fernwerker](https://github.com/fernwerker)
* Philipp Tempel [@philipptempel](https://github.com/philipptempel)
* Branko Wilhelm [@b2un0](https://github.com/b2un0)
* Nils Blume [@niiwiicamo](https://github.com/niiwiicamo)

## Usage

### docker-compose.yaml
```

```

## URL possible uses:
### Required parameters in URL:

<b>user, password and domain</b> are <i> always needed</i>, as well as at least one of the following: <br>
<b>ipv4, ipv6, txt</b>


Parameter | Example | Explanation
---: | :--- | :---
user | dnsupdater | username to authenticate against this script as defined in .env file. If anonymous login is allowed in .env: `anonymous`
password | secretpleasechange | password for that user as defined in .env file
domain | home.example.com | `case A)` If `host` is not specified: the FQDN for your host
domain | example.com | `case B)` If you want to update the @ or * record
domain | example.com | `case C)` If `host`is specified: only the domain part as registered at netcup "nas.home.example.com"
host | nas.home | `case C)` If your domain contains more than 3 levels "nas.home.example.com"
ipv4 | 1.2.3.4 | the ipv4 address to update an existing A record
ipv6 | fe80::12:34:56 | the ipv6 address to update an existing AAAA record
txt | acme-challenge-text | the content to update an existing TXT record
force | true | ignore checking if the record needs to be updated, just do it anyways. Default: `false`
mode | * | `case B)` If domain is your registered domain "example.com". Possible values: `*` or `both`. Default: `@`
create | true | create all entries if none exist. e.g. will not create A if AAAA exists. Needs `allowCreate=true` in .env
customerId | 12345 | uses the URL provided credentials instead of the ones stored in .env. Needs `allowNetcupCreds=true` in .env
apiKey | 12345 | uses the URL provided credentials instead of the ones stored in .env. Needs `allowNetcupCreds=true` in .env
apiPassword | 12345 | uses the URL provided credentials instead of the ones stored in .env. Needs `allowNetcupCreds=true` in .env



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
* Username: `<username as defined in .env file>`
* Password: `<password as definied in .env file>`

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
* Username/Email: `<username as defined in .env file>`
* Password/Key: `<password as defined in .env file>`
* External Address (IPv4): probably "Auto", uses Synology service to find own external IP
* External Address (IPv6): doesn't matter, currently not supported by Synology

### pfSense Settings
* Go to "Services" -> "Dynamic DNS"
* Click on "Add" to create a DDNS profile
* Service Type: "Custom"
* Interface to monitor: `<select you WAN interface>`
* Interface to send update from: `<select your WAN interface>`
* Update URL: `https://<url of your webspace>/update.php?user=<user from .env>&password=<password from .env>&ipv4=%IP%&domain=<host record to update>`
* Leave all other fields empty / default

# run as cronjob on a **nix based device
* see [examples](./examples)

## References
* DNS API Documentation: https://ccp.netcup.net/run/webservice/servers/endpoint.php
* Source of dnsapi.php: https://ccp.netcup.net/run/webservice/servers/endpoint.php?PHPSOAPCLIENT

## License
Published under GNU General Public License v3.0  
&copy; Felix Kretschmer, 2021
