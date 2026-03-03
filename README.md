# ownDynDNS
Self-hosted dynamic DNS php script to update netcup DNS API from Router like AVM FRITZ!Box or UniFi Gateways

## Authors
* Felix Kretschmer [@fernwerker](https://github.com/fernwerker)
* Philipp Tempel [@philipptempel](https://github.com/philipptempel)
* Branko Wilhelm [@b2un0](https://github.com/b2un0)

## Usage
### Installation
* Copy all files to your [webspace](https://community.netcup.com/en/tutorials/ddns-with-webhosting)
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
* Update-URL for the FRITZ!Box itself (IPv4 + IPv6): `https://<url of your webspace>/update.php?user=<username>&password=<pass>&ipv4=<ipaddr>&ipv6=<ip6addr>&domain=<domain>`
* Update-URL for a device behind the FRITZ!Box (prefix + suffix, two URLs separated by space):
  `https://<url of your webspace>/update.php?user=<username>&password=<pass>&domain=<domain>&mode=both&ipv4=<ipaddr> https://<url of your webspace>/update.php?user=<username>&password=<pass>&domain=<domain>&mode=both&ipv6=::1a2b:3c4d:5e6f:7890&ipv6prefix=<ip6lanprefix>&_ip6addr=<ip6addr>`
  * The first URL updates the A record, the second updates the AAAA record
  * When `ipv6prefix` is provided, the `ipv6` value is treated as suffix (interface ID) and combined with the prefix to form the full AAAA address
  * `ipv6prefix` accepts a bare prefix (e.g. `2001:db8:abcd:ef00`), with trailing `::` (e.g. `2001:db8:abcd:ef00::`), or with CIDR notation (e.g. `2001:db8:abcd:ef00::/64` as sent by FRITZ!Box via `<ip6lanprefix>`)
  * `_ip6addr=<ip6addr>` is required so the FRITZ!Box resolves the `<ip6lanprefix>` placeholder
  * Replace `::1a2b:3c4d:5e6f:7890` with the static interface ID of your target device
  * only the url needs to be adjusted, the rest is automatically filled by your AVM FRITZ!Box
  * http or https is possible if valid SSL certificate (e.g. Let's Encrypt)
* Single Domain:
  * Domainname: `<host record that is supposed to be updated>`
* Multiple Domains:
  * Domainname: `<first host record that is supposed to be updated>,<second host record that is supposed to be updated>,....`
* Username: `<username as defined in .env file>`
* Password: `<password as definied in .env file>`

### UniFi Gateways
* Go to "Settings" -> "Internet" -> "Choose WAN Interface" -> "Dynamic DNS" -> "+ Create New Dynamic DNS"
* Service "Choose -> `dyndns`"
* Hostname: `<host record that is supposed to be updated>`
* Username: `<username as defined in .env file>`
* Password: `<password as definied in .env file>`
* Server: `dyn.yourdomain.com/update.php/\/nic/update?user=%u&password=%p&ipv4=%i&force=0&mode=both&domain=%h`

# run as cronjob on a **nix based device
* see [examples](./examples)

## References
* DNS API Documentation: https://ccp.netcup.net/run/webservice/servers/endpoint.php
* Source of dnsapi.php: https://ccp.netcup.net/run/webservice/servers/endpoint.php?PHPSOAPCLIENT

## License
Published under GNU General Public License v3.0  
&copy; Felix Kretschmer, 2021
