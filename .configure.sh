#!/bin/bash
echo "### ownDynDNS configuration script"

# set variables
scriptversion="1.0"

defaultenvfile=".env.dist"

dir=$(pwd)
while [ ! -f $dir/$defaultenvfile ]
do
  echo "current directory does not contain ${defaultenvfile} !"
  read -p "enter directory where ownDynDNS is located: " dir
done

# source .env.dist
source $dir/$defaultenvfile

envfile="${dir}/.env"

log1="$logFile"
log2="/var/log/dnsupdater/log.json"


### main script
#echo "found ${defaultenvfile}. using current directory"

read -p "enter a custom username for dns updates [random]: " user
user=${user:-$(tr -dc A-Za-z0-9 </dev/urandom | head -c 16)}
#echo "using username: ${user}"

read -s -p "enter a custom password for dns updates [random]: " pass
pass=${pass:-$(tr -dc A-Za-z0-9 </dev/urandom | head -c 16)}
echo ""
#echo "using password: ${pass}"

read -s -p "enter your netcup DNS API Key: " apikey
echo ""
#echo "using api key: ${apikey}"

read -s -p "enter your netcup API Password: " apipass
echo ""
#echo "using api password: ${apipass}"

read -p "enter your netcup customer ID: " custid
#echo "using customer id: ${custid}"

read -p "do you wish to enable debugging? [y/N]: " debug
if [[ ${debug,,::1} == "y" ]]
then
  #echo "enabling debugging"
  debug="true"
else
  #echo "disabling debugging"
  debug="false"
fi

read -p "do you want to enable logging? [Y/n]: " log
if [[ ${log,,::1} != "n" ]]
then
  #echo "enabling logging"
  log="true"
else
  #echo "disabling logging"
  log="false"
fi

echo "the logfile is created in this directory by default."
echo "your ip history is thereby publically available."
echo "select where the logfile should be created if enabled:"
echo "[1] default: ${log1}"
echo "[2] private: ${log2}"
echo "[3] custom location"

read -p "select from the choices above [1]: " choice
case $choice in
  2)
    logfile=${log2}
    ;;
  3)
    read -p "enter logfile location: " logfile
    logfile={logfile:-$log1}
    ;;
  *)
    logfile=${log1}
    ;;
esac

mkdir -p $(dirname $logfile) && touch $logfile || echo "### could not create logfile!"
#echo "logfile will be created at: ${logfile}"

### create the .env file
if [ -f $envfile ]
then
  echo "${envfile} already exists!"
  read -p "overwrite? [y/N]: " overwrite
  if [[ ! ${overwrite,,::1} == y ]]
  then
    echo "script cancelled. exiting"
    exit 1
  fi
fi

#echo "creating .env file"
touch $envfile
echo "# file created at $(date)" >$envfile
echo "# by configuration script version ${scriptversion}" >> $envfile
echo "username=\"${user}\"" >> $envfile
echo "password=\"${pass}\"" >> $envfile
echo "apiKey=\"${apikey}\"" >> $envfile
echo "apiPassword=\"${apipass}\"" >> $envfile
echo "customerId=\"${custid}\"" >> $envfile
echo "debug=\"${debug}\"" >> $envfile
echo "log=\"${log}\"" >> $envfile
echo "logfile=\"${logfile}\"" >> $envfile

echo "created .env file at: ${envfile}"
