# ownDynDNS auth script for certbot dns-01 challenge

# install curl
$(apk --no-cache add curl)

ACME_PREFIX="_acme-challenge"

if [ -z "${DDNS_HOST}" ]; then DDNS_HOST=${ACME_PREFIX}; else DDNS_HOST=${ACME_PREFIX}.${DDNS_HOST}; fi

PAYLOAD="force=true&user=${DDNS_USER}&password=${DDNS_PASS}&txt=${CERTBOT_VALIDATION}&domain=${DDNS_DOMAIN}&host=${DDNS_HOST}&create=true"

# echo ${PAYLOAD}

curl -sSL -X POST --data "${PAYLOAD}" ${DDNS_SCRIPT} \
&& sleep 300
