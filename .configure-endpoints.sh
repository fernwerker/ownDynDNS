#!/bin/bash

scriptversion="1.0"
srcfolder="src"
updatephp="update.php"
configuresh=".configure.sh"

### banner
clear
echo "##############################################"
echo "### ownDynDNS multi-endpoint configuration ###"
echo "###           script version $scriptversion           ###"
echo "##############################################"
echo ""

echo "This script will set up multiple endpoints within the same webspace.\
 That means you can use multiple sets of user credentials each with their own\
 permissions regarding which domains to update."
echo ""
echo "It is recommended you use the webroot of your desired webspace, although\
 you could place this directory structure anywhere you like, e.g. in a\
 subdirectory of your homepage like example.com/dyndns/[this tree] ."
echo ""
echo "This script assumes you have already downloaded the update.php script\
 and the src directory including its contents."
echo ""

### set up dir variable for this script
dir=$(pwd)
while [ ! -d $dir/$srcfolder ]
do
  echo "current directory does not contain ${srcfolder} !"
  read -p "enter directory where ownDynDNS is located: " dir
done

### set up user and group for permissions later
echo "This script will automatically set the necessary file permissions for\
 your webserver. This might be www-data:www-data, please check if you run\
 into any issues."
echo ""
read -p "enter the user the webserver is running as [www-data]: " wwwuserd
read -p "enter the group the webserver is running as [www-data]: " wwwgroupd
wwwuserd=${wwwuserd:-"www-data"}
wwwgroupd=${wwwgroupd:-"www-data"}


createEndpoint() {
  local endpoint=$1
  mkdir $dir/$endpoint
  cp $dir/$updatephp $dir/$endpoint
  chmod +x $dir/$configuresh
  $dir/$configuresh $dir/$endpoint
  chown $wwwuserd:$wwwgroupd $dir/$endpoint/$updatephp
  chmod 440 $dir/$endpoint/$updatephp
  chown $wwwuserd:$wwwgroupd $dir/$endpoint/.env
  chmod 440 $dir/$endpoint/.env
}

echo "##############################################"
echo "You will now start adding endpoints which are just subdirectories\
 that contain the update.php file as well as a customized .env file."
echo ""

### endpoint creation loop
while true
do
  read -p "enter endpoint name [Empty to quit]: " endpoint
  if [ -z $endpoint ]; then break; fi
  createEndpoint $endpoint
done
