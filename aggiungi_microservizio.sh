#!/bin/bash

PATHPROGETTO=$PWD

# NOMESERVIZIO="USER INPUT" -> variabile $NOMESERVIZIO
read -p "Digita il nome del servizio ?" NOMESERVIZIO
read -p "Digita la porta usata ?" PORTA

if [ -d "$PATHPROGETTO/microservices/$NOMESERVIZIO" ]; then
  echo "La cartella esiste."
else
  mkdir -p $PATHPROGETTO/microservices/$NOMESERVIZIO
  cd $PATHPROGETTO/microservices

  echo Creazione microservizio $PATHPROGETTO/microservices/$NOMESERVIZIO
  cd $NOMESERVIZIO

#exit 

cat <<"EOF" > Dockerfile
FROM php:8.2.4-fpm
RUN pecl install xdebug
RUN docker-php-ext-enable xdebug
RUN curl -sS https://getcomposer.org/installer​ | php -- --install-dir=/usr/local/bin --filename=composer
RUN apt update -y \
       && apt install git libzip-dev zsh wget zip -y
RUN docker-php-ext-install pdo pdo_mysql zip
RUN docker-php-ext-enable pdo pdo_mysql
RUN sh -c "$(wget -O- https://github.com/deluan/zsh-in-docker/releases/download/v1.1.5/zsh-in-docker.sh)"
EOF
cat <<EOF >> Dockerfile
WORKDIR /$NOMESERVIZIO
EOF

echo Creazione progetto Laravel
cat <<EOF >> $PATHPROGETTO/docker-compose.yml
# microservice $NOMESERVIZIO
  $NOMESERVIZIO:
    container_name: $NOMESERVIZIO
    build:
      context: .
      dockerfile: ./microservices/$NOMESERVIZIO/Dockerfile
    entrypoint: php -S 0.0.0.0:80 -t public
    environment:
      XDEBUG_MODE: develop,debug
      XDEBUG_CONFIG: client_host=host.docker.internal client_port=9003
    volumes:
      - ./microservices/$NOMESERVIZIO/project:/$NOMESERVIZIO # cartella progetto
      - ./microservices/$NOMESERVIZIO/xdebug/docker-php-ext-xdebug.ini:/usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini # impostazioni per debug
      - ./microservices/$NOMESERVIZIO/php/php.ini:/usr/local/etc/php/php.ini # impostazioni php
    extra_hosts:
      - "host.docker.internal:host-gateway"
    ports:
      - $PORTA:80

EOF

    echo creazione progetto laravel
    cd $PATHPROGETTO
    
    echo cp -R ./conf/. ./microservices/$NOMESERVIZIO/.
    cp -R ./conf/. ./microservices/$NOMESERVIZIO/.
    
    # Build the image
    docker compose build
    # Run the image
    docker compose up -d
    docker compose exec -T $NOMESERVIZIO composer create-project laravel/laravel .

    echo copia file per inizializzare xdebug e database
    echo cp ./init/.env* ./microservices/$NOMESERVIZIO/project/.
    cp ./init/.env* ./microservices/$NOMESERVIZIO/project/.
    echo cp ./init/database.php ./microservices/$NOMESERVIZIO/project/config/database.php
    cp ./init/database.php ./microservices/$NOMESERVIZIO/project/config/database.php
fi

echo Directory progetto = $PATHPROGETTO
echo Nome microservizi = $NOMESERVIZIO
echo Porta = $PORTA
echo Directory microservizi = $PATHPROGETTO/microservizi/$NOMEPROGETTO
