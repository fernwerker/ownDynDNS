#!/usr/bin/env bash

# you can run this script from **ix bases device to update (different) Records

USER="max_mustermann"
PASS="s3cr3t"
DOMAIN="my-home-nas.de"
#DOMAIN="nas.my-home.de"
SCRIPT="https://<url of your webspace>/update.php"
FORCE=0
MODE="both" # can be undefined, "@", "*" or "both"

IPV4=$(curl -4 -q ident.me)
IPV6=$(curl -6 -q ident.me)

echo ${IPV4}
echo ${IPV6}

# PAYLOAD_IPV4="force=${FORCE}&user=${USER}&password=${PASS}&ipv4=${IPV4}&domain=${DOMAIN}&mode=${MODE}"
# curl -X POST --data "${PAYLOAD_IPV4}" ${SCRIPT}

# PAYLOAD_IPV6="force=${FORCE}&user=${USER}&password=${PASS}&ipv6=${IPV6}&domain=${DOMAIN}&mode=${MODE"}
# curl -X POST --data "${PAYLOAD_IPV6}" ${SCRIPT}

PAYLOAD_BOTH="force=${FORCE}&user=${USER}&password=${PASS}&ipv4=${IPV4}&ipv6=${IPV6}&domain=${DOMAIN}&mode=${MODE}"
curl -X POST --data "${PAYLOAD_BOTH}" ${SCRIPT}
