# ownDynDNS auth script for certbot dns-01 challenge

# install curl
apk --no-cache add curl

ACME_PREFIX="_acme-challenge"

# check if wildcard cert is requested, as that needs to be stripped
if [[ "${DDNS_HOST}" == '*.'* ]]; then DDNS_HOST=${DDNS_HOST#\*\.}; fi

# prepend acme prefix to host part
if [ -z "${DDNS_HOST}" ]; then DDNS_HOST=${ACME_PREFIX}; else DDNS_HOST=${ACME_PREFIX}.${DDNS_HOST}; fi

PAYLOAD="force=true&user=${DDNS_USER}&password=${DDNS_PASS}&txt=${CERTBOT_VALIDATION}&domain=${DDNS_DOMAIN}&host=${DDNS_HOST}&create=true"

# echo ${PAYLOAD}

curl -sSL -X POST --data "${PAYLOAD}" ${DDNS_SCRIPT} \
&& sleep 300
