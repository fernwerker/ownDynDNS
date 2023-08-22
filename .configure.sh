!/bin/bash
echo "### ownDynDNS configuration script"

# set variables
scriptversion="1.3.1"

wwwuserd="www-data"
wwwgroupd="www-data"

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

echo "the logfile is created in this directory by default. your ip history is thereby publically available."
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
    logfile=${logfile:-$log1}
    ;;
  *)
    logfile=${log1}
    ;;
esac

echo "the logfile needs to be writable by the webserver if logging is enabled."
read -p "which user does the webserver run as? [${wwwuserd}]: " wwwuser
wwwuser=${wwwuser:-$wwwuserd}

read -p "which group does the webserver run as? [${wwwgroupd}]: " wwwgroup
wwwgroup=${wwwgroup:-$wwwgroupd}

mkdir -p $(dirname $logfile) && touch $logfile || echo "### could not create logfile!"
chown $wwwuser:$wwwgroup $logfile
chmod 0640 $logfile
#echo "logfile will be created at: ${logfile}"



### Apache htaccess file config
echo "if you are using apache it is recommended to enable the .htaccess file to prevent unauthorized access to the .env file and any logfile."
echo "select if you want to enable the .htaccess file:"
echo "[1] no .htaccess file. (e.g. using nginx)"
echo "[2] block access to .env file only (default log location accessible)"
echo "[3] block access to .env file and log file"

read -p "select from the choices above [1]: " choice
case $choice in
  2)
    cat > $htaccess << EOM
<FilesMatch "\.env$">
        Order allow,deny
        Deny from all
</FilesMatch>
EOM
    rm .htaccess.example
    ;;
  3)
    mv .htaccess{.example,}
    ;;
  *)
    rm .htaccess.example
    ;;
esac

### nginx htaccess equivalent message
echo "if you are using nginx please read the docs about how to disable access to certain files and folders.\nyou might add a location block to the beginning of your site config as follows:"
echo -e "  location ~* (env|log|json) {\n    deny all;\n    return 404;\n  }"



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
echo "debug=${debug}" >> $envfile
echo "log=${log}" >> $envfile
echo "logFile=${logfile}" >> $envfile

echo "created .env file at: ${envfile}"
