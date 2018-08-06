# ownDynDNS
Self-hosted dynamic DNS php script for FRITZ!Box and netcup DNS API

## Authors
* @fernwerker
* @philipptempel

## Usage
### Installation
* Copy all files to your webspace
* Edit the first lines of update.php
  * username -> The username for your FRITZ!Box to authenticate (so not everyone can update your DNS)
  * password -> password for your FRITZ!Box
  * debug -> enables debug mode and generates output of update.php (normal operation has no output)
  * apiKey -> API key which is generated in netcup CCP
  * apiPassword -> API password which is generated in netcup CCP
  
* Create each host record in your netcup CCP before using the script. The script does not create non-existent records.

### FRITZ!Box Settings
* Go to "Internet" -> "DynDNS"
* Choose "custom"
* Update-URL: `https://<url of your webspace>/update.php?user=<username>&password=<pass>&ipv4=<ipaddr>&ipv6=<ip6addr>&domain=<domain>`
  * only the url needs to be adjusted, the rest is automatically filled by the FRITZ!Box
  * http or https is possible if valid SSL certificate (e.g. Let's Encrypt)
* Domainname: `<host record that is supposed to be updated>`
* Username: `<username as defined in update.php>`
* Password: `<password as definied in update.php>`

## References
* DNS API Documentation: https://ccp.netcup.net/run/webservice/servers/endpoint.php
* Source of dnsapi.php: https://ccp.netcup.net/run/webservice/servers/endpoint.php?PHPSOAPCLIENT

## License
Published under GNU General Public License v3.0  
&copy; Felix Kretschmer, 2018
