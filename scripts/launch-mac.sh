#!/bin/sh
api_dir='/Users/fagathe/workspace/perso/tavern-bo'
api_host='dev.bo-tavern.agathefrederick.fr'
port='5500'
db_driver='mysql'
# enregistrer le nouveau nom de domaine dans le host de la machine
# echo "127.0.0.1\t${api_host}" | sudo tee -a /etc/hosts

# lance le service postgresql
brew services start $db_driver
cd $api_dir
echo 'cd api dir'
echo 'ouvrir le projet sur vscode'
code .
bin/console c:c -n
echo "open http://${api_host}:${port} in browser"
open http://$api_host:$port
            
# lance le serveur interne de php
php -S $api_host:$port -t public

# stop le service postgres lorsqu'on stop le script
trap "brew services stop ${db_driver}" EXIT
