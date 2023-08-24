# Multiple Endpoints with separate credentials
For advanced use you might want to have separate users that can each only update one domain entry.

In that case it might be beneficial to habe multiple endpoints, e.g. `https://dyndns.example.com/endpointN/update.php` where endpointN is any directory name you wish.

## Setting up multiple endpoints
The directory structure of your webroot might look like this:
<pre>
├── index.html
├── src
│   ├── Config.php
│   ├── Handler.php
│   ├── Payload.php
│   └── Soap.php
├── fritzbox        # this is a subdomain
│   ├── .env
│   └── update.php
├── nas             # this is another
│   ├── .env
│   └── update.php
├── examplenet      # uses another netcup account
│   ├── .env
│   └── update.php
└── subdomain1      # and another subdomain
    ├── .env
    └── update.php
</pre>

Here the update.php files are copied from the mydomain example directory. All .env files contain different user credentials and may even use different netcup credentials.

## Setting up domain restrictions per .env file
It is nice to have multiple sets of credentials, but if anyone can update any entry of any domain this defeats the purpose.

That is why you can enable domain restriction per .env file and thereby per set of user credentials.

In these cases you the domain you send in your url will be ignored in favour of the one configured in the .env file. <b>You still need to send a placeholder for validation purposes.</b>

Example .env file for fritzbox.example.com.<br>
Callable by: `https://dyndns.example.com/fritzbox/update.php?user=fritzbox&password=changeme&domain=placeholder&ipv4=1.2.3.4`
<pre>
username="fritzbox"
password="changemeplease"
apiKey="j1meo213em823jd2q9"
apiPassword="12345secret"
customerId="12345"
debug=false
log=true
logFile=/var/log/dnsupdater/fritzbox.json
restrictDomain=true
domain=fritzbox.example.com
</pre>

Example .env file for nas.home.example.com.<br>
Callable by: `https://dyndns.example.com/nas/update.php?user=nas&password=changeme&domain=placeholder&ipv4=1.2.3.4`
<pre>
username="nas"
password="changemeplease"
apiKey="j1meo213em823jd2q9"
apiPassword="12345secret"
customerId="12345"
debug=false
log=true
logFile=/var/log/dnsupdater/nas.json
restrictDomain=true
domain=example.com    # for explicit use of third-level domain
host=nas.home         # we use the optional host parameter
</pre>