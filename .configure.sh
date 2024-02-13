#!/bin/bash

# set variables
scriptversion="1.6"

defaultenvfile=".env.dist"

if [ ! -z $1 ]
then
  dir=$1
  endpoint=$(basename ${dir})

  # set up log file location suggestion
  log1="/var/log/dnsupdater/${endpoint}.json"
  log2="${dir}/log.json"
  
else
  echo "### ownDynDNS configuration script"

  wwwuserd="www-data"
  wwwgroupd="www-data"

  dir=$(pwd)
  while [ ! -f $dir/$defaultenvfile ]
  do
    echo "current directory does not contain ${defaultenvfile} !"
    read -p "enter directory where ownDynDNS is located: " dir
  done

  # source .env.dist
  source $dir/$defaultenvfile

  # set up log file location suggestions
  log1="$logFile"
  log2="/var/log/dnsupdater/log.json"
fi


envfile="${dir}/.env"


### main script
#echo "found ${defaultenvfile}. using current directory"

read -p "enter a custom username for dns updates [random]: " user
user=${user:-$(tr -dc A-Za-z0-9 </dev/urandom | head -c 16)}
echo "using username: ${user}"

read -s -p "enter a custom password for dns updates [random]: " pass
pass=${pass:-$(tr -dc A-Za-z0-9 </dev/urandom | head -c 16)}
echo ""
echo "using password: ${pass}"

if [ -z $DDNS_NETCUP_API_KEY ]
then
  read -s -p "enter your netcup DNS API Key: " apikey
  echo ""
else
  echo -e "Found DDNS_NETCUP_API_KEY. Leave empty to use or enter new DNS API Key\n"
  read -p "DNS API Key [${DDNS_NETCUP_API_KEY}]: " apikey
  apikey=${apikey:-$DDNS_NETCUP_API_KEY}
fi

if [ -z $DDNS_NETCUP_API_PASSWORD ]
then
  read -s -p "enter your netcup API Password: " apipass
  echo ""
else
  echo "Found DDNS_NETCUP_API_PASSWORD. Leave empty to use or enter new DNS API Password"
  read -p "DNS API Password [${DDNS_NETCUP_API_PASSWORD}]: " apipass
  echo ""
  apipass=${apipass:-$DDNS_NETCUP_API_PASSWORD}
fi

if [ -z $DDNS_NETCUP_CUSTOMER_ID ]
then
  read -s -p "enter your netcup customer ID: " custid
  echo ""
else
  echo "Found DDNS_NETCUP_CUSTOMER_ID. Leave empty to use or enter new customer ID"
  read -p "Netcup customer ID [${DDNS_NETCUP_CUSTOMER_ID}]: " custid
  echo ""
  custid=${custid:-$DDNS_NETCUP_CUSTOMER_ID}
fi

read -p "do you wish to enable debugging? [y/N]: " debug
echo ""
if [[ ${debug,,::1} == "y" ]]
then
  #echo "enabling debugging"
  debug="true"
else
  #echo "disabling debugging"
  debug="false"
fi

read -p "do you want to enable logging? [Y/n]: " log
echo ""
if [[ ${log,,::1} != "n" ]]
then
  #echo "enabling logging"
  log="true"
else
  #echo "disabling logging"
  log="false"
fi

echo "the logfile is created in this directory by default. your ip history is thereby publically available."
echo "select where the logfile should be created if enabled:"
echo "[1] default: ${log1}"
echo "[2] private: ${log2}"
echo "[3] custom location"

read -p "select from the choices above [1]: " choice
echo ""
case $choice in
  2)
    logfile=${log2}
    ;;
  3)
    read -p "enter logfile location: " logfile
    echo ""
    logfile=${logfile:-$log1}
    ;;
  *)
    logfile=${log1}
    ;;
esac

if [ -z ${endpoint} ]
then
  echo "the logfile needs to be writable by the webserver if logging is enabled."
  read -p "which user does the webserver run as? [${wwwuserd}]: " wwwuser
  echo ""
  wwwuser=${wwwuser:-$wwwuserd}

  read -p "which group does the webserver run as? [${wwwgroupd}]: " wwwgroup
  echo ""
  wwwgroup=${wwwgroup:-$wwwgroupd}

  mkdir -p $(dirname $logfile) && touch $logfile || echo "### could not create logfile!"
  echo ""
  chown $wwwuser:$wwwgroup $logfile
  chmod 0640 $logfile
  #echo "logfile will be created at: ${logfile}"
fi


### Apache htaccess file config
echo "if you are using apache it is recommended to enable the .htaccess file to prevent unauthorized access to the .env file and any logfile."
echo "select if you want to enable the .htaccess file:"
echo "[1] no .htaccess file. (e.g. using nginx)"
echo "[2] block access to .env file only (default log location accessible)"
echo "[3] block access to .env file and log file"
echo ""

read -p "select from the choices above [1]: " choice
echo ""
case $choice in
  2)
    cat > $htaccess << EOM
<FilesMatch "\.env$">
        Order allow,deny
        Deny from all
</FilesMatch>envfile
EOM
    if [ -z $endpoint ]
    then
      rm .htaccess.example
    fi
    ;;
  3)
    mv .htaccess{.example,}
    ;;
  *)
    if [ -z $endpoint ]
    then
      rm .htaccess.example
    fi
    ;;
esac

### nginx htaccess equivalent message
echo "if you are using nginx please read the docs about how to disable access to certain files and folders.\nyou might add a location block to the beginning of your site config as follows:"
echo -e "  location ~* (env|log|json) {\n    deny all;\n    return 404;\n  }"

read -p "do you wish to enable result return? [y/N]: " returnip
echo ""
if [[ ${returnip,,::1} == "y" ]]
then
  #echo "enabling return ip"
  returnip="true"
else
  #echo "disabling return ip"
  returnip="false"
fi

read -p "do you want to allow creation of new entries on the fly? [y/N]: " allowcreate
echo ""
if [[ ${allowcreate,,::1} == "y" ]]
then
  #echo "enabling return ip"
  allowcreate="true"
else
  #echo "disabling return ip"
  allowcreate="false"
fi

read -p "do you want to restrict updates to a specific domain entry? [Y/n]: " restrictdomain
echo ""
if [[ ${restrictdomain,,::1} == "n" ]]
then
  restrictdomain="false"
else
  restrictdomain="true"
  echo "enter the FQDN you want to restrict updates to. If you are using third\
 level domains, e.g. nas.home.example.com you should only enter example.com"
  echo "use the \"host\" variable for nas.home in that case."
  echo ""
  read -p "domain or FQDN: " domain
  echo ""
  read -p "host if third level domain: " host
  echo ""
fi


### create the .env file
if [ -f $envfile ]
then
  echo "${envfile} already exists!"
  read -p "overwrite? [y/N]: " overwrite
  echo ""
  if [[ ! ${overwrite,,::1} == y ]]
  then
    echo "script cancelled. exiting"
    echo ""
    exit 1
  fi
fi

touch $envfile
echo "# file created at $(date)" >$envfile
echo "# by configuration script version ${scriptversion}" >> $envfile
echo "username=\"${user}\"" >> $envfile
echo "password=\"${pass}\"" >> $envfile
echo "apiKey=\"${apikey}\"" >> $envfile
echo "apiPassword=\"${apipass}\"" >> $envfile
echo "customerId=\"${custid}\"" >> $envfile
echo "debug=${debug}" >> $envfile
echo "log=${log}" >> $envfile
echo "logFile=${logfile}" >> $envfile
echo "returnIp=${returnip}" >> $envfile
echo "allowCreate=${allowcreate}" >> $envfile
echo "restrictDomain=${restrictdomain}" >> $envfile
if [ ! -z ${domain} ]
then
  echo "domain=${domain}" >> $envfile
fi
if [ ! -z ${host} ]
then
  echo "host=${host}" >> $envfile
fi

echo "created .env file at: ${envfile}"
echo ""
