# ownDynDNS
Self-hosted dynamic DNS php script to update netcup DNS API from Router like AVM FRITZ!Box  

## Authors
* Felix Kretschmer [@fernwerker](https://github.com/fernwerker)
* Philipp Tempel [@philipptempel](https://github.com/philipptempel)
* Branko Wilhelm [@b2un0](https://github.com/b2un0)

## Usage
### Installation
* Copy all files to your webspace
* create a copy of `.env.dist` as `.env` and configure:
  * `username` -> The username for your Router to authenticate (so not everyone can update your DNS)
  * `password` -> password for your Router
  * `apiKey` -> API key which is generated in netcup CCP
  * `apiPassword` -> API password which is generated in netcup CCP
  * `customerId` -> your netcup Customer ID
  * `debug` -> true|false enables debug mode and generates output of update.php (normal operation has no output)
  
* Create each host record in your netcup CCP (DNS settings) before using the script. The script does not create any missing records.

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
