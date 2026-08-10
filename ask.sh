DB_NAME=vulkansq1l
DB_USER=vulkansql1
DB_PASSWORD=Eleervar

ask() {
  echo -e "What you like to do?, enter a Task Id from list below: \n"
  echo -e "TaskID\tFile\t\tDescription"
  echo -e "1\t Run - Docker Test Enviroment"
  echo -e "2\t Stop Docker"
  echo -e "3\t Clean Docker - Clean the docker containers and volumes "
  echo -e "4\t Clean All - Clean the docker containers and volumes and images "
  echo -e "5\t Rename to host 127.0.0.1"
  echo -e "6\t Export Database"
  echo -e "7\t Import Database"
  echo -e "8\t Logfile /wordpress/wp-content/debug.log"
  echo -e "9\t Wpcli - to use commandline WP-CLI"
  echo -e "10\t Run - Docker Test Enviroment verbose"
  echo -e "11\t Activate all Plugins "
  echo -e "12\t Dactivate all Plugins "
  echo -e "13\t Activate Plugis woo,alma,bakery js"
  echo -e "14\t Theme twentytwentyfive"
  echo -e "15\t Theme impeka-child"
  echo -e "16\t Get/Rsync from Production to Local"
  echo -e "17\t Rename to host 192.168.43.2"
  echo -e "18\t Upload functions.php to online theme"
  echo -e "0\t Exit"
}

ask

until [ "$task" = "0" ]; do
  read task

  if [ "$task" = "1" ]; then
    echo "...${task}"
    cd dockers
    docker-compose up -d

  elif [ "$task" = "10" ]; then
    echo "...${task}"
    cd dockers
    docker-compose up

  elif [ "$task" = "2" ]; then
    echo "...${task}"
    cd dockers
    docker-compose down

  elif [ "$task" = "3" ]; then
    echo "...${task}"
    docker rm --force $(docker ps -qa)
    docker volume rm $(docker volume ls -q --filter dangling=true)
    docker network prune --force

  elif [ "$task" = "4" ]; then
    echo "...${task}"
    docker rm --force $(docker ps -qa)
    docker volume rm $(docker volume ls -q --filter dangling=true)
    docker network prune
    docker rmi --force $(docker images -aq)

  elif [ "$task" = "5" ]; then
    echo "...${task}"
    docker exec wpcli wp search-replace "https://www.lanzarote-vulkane.de" "http://127.0.0.1" --skip-columns=guid --allow-root
    docker exec wpcli wp search-replace "http://www.lanzarote-vulkane.de" "http://127.0.0.1" --skip-columns=guid --allow-root
    docker exec wpcli wp search-replace "lanzarote-vulkane.de" "127.0.0.1" --skip-columns=guid --allow-root

  elif [ "$task" = "6" ]; then
    echo "...task ${task}..."
    cd dockers
    docker run -i --rm --net=host salamander1/mysqldump --verbose -h db -u "${DB_NAME}" -p"${DB_PASSWORD}" "${DB_NAME}" | gzip >"init/${DB_NAME}.sql.gz"

  elif [ "$task" = "7" ]; then
    echo "...task ${task} "
    cd dockers
    zcat "init/$DB_NAME.sql.gz" | docker run -i --rm --net=host salamander1/mysql -h 127.0.0.1 -u "${DB_NAME}" -p"${DB_PASSWORD}" "${DB_NAME}"

  elif [ "$task" = "8" ]; then
    echo "...task ${task} "
    tail wordpress/wp-content/debug.log -f

  elif [ "$task" = "9" ]; then
    echo "...task ${task} "
    docker exec -it wpcli bash

  elif [ "$task" = "11" ]; then
    echo "...task ${task} "
    docker exec wpcli wp plugin activate --all --allow-root
    docker exec wpcli wp plugin list --all --allow-root

  elif [ "$task" = "12" ]; then
    echo "...task ${task} "
    docker exec wpcli wp plugin deactivate --all --allow-root
    docker exec wpcli wp plugin list --all --allow-root

  elif [ "$task" = "13" ]; then
    echo "...task ${task} "
    docker exec wpcli wp plugin activate woocommerce alma-gateway-for-woocommerce js_composer --allow-root
    docker exec wpcli wp plugin list --all --allow-root

  elif [ "$task" = "14" ]; then
    echo "...task ${task} "
    docker exec wpcli wp theme install twentytwentyfive --activate --allow-root
    docker exec wpcli wp theme list --all --allow-root

  elif [ "$task" = "15" ]; then
    echo "...task ${task} "
    docker exec wpcli wp theme activate impeka-child --allow-root
    docker exec wpcli wp theme list --all --allow-root

  elif [ "$task" = "16" ]; then
    echo "...task ${task} "
    rsync --exclude backup --exclude .original --exclude updraft --exclude *.log --exclude cache --delete -e ssh -avz gkizjkr-helmut@ssh.cluster100.hosting.ovh.net:~/www/ wordpress/
    cp wp-config.php wordpress/
    sudo chmod 777 wordpress/wp-content/uploads/ -Rf

  elif [ "$task" = "17" ]; then
    echo "...${task}"
    docker exec wpcli wp search-replace "https://ambrejolie.com" "http://192.168.43.2" --skip-columns=guid --allow-root
    docker exec wpcli wp search-replace "http://ambrejolie.com" "http://192.168.43.2" --skip-columns=guid --allow-root
    docker exec wpcli wp search-replace "ambrejolie.com" "192.168.43.2" --skip-columns=guid --allow-root

  elif [ "$task" = "18" ]; then
    echo "...${task}"
    scp ./wordpress/wp-content/themes/impeka-child/functions.php gkizjkr-helmut@ssh.cluster100.hosting.ovh.net:~/www/wp-content/themes/impeka-child/

  elif [ "$task" = "0" ]; then
    echo "...task ${task} "
    echo "Goodbye! - Exit"

  else
    echo "Goodbye! - Exit"

  fi

  ask

done
