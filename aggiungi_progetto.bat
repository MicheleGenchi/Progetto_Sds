@echo off
set rootPath=%userprofile%\Documents\Progetti_php

echo Enter your nome progetto : 
set /p NOMEPROGETTO=

echo Enter port progetto : 
set /p PORTA=

if exist microservices\%NOMEPROGETTO% goto progetto_esiste

rem kill all container
FOR /f "tokens=*" %%i IN ('docker ps -q') DO docker stop %%i

rem scrivi Dockerfile
md microservices\%NOMEPROGETTO%
cd microservices\%NOMEPROGETTO%
echo %NOMEPROGETTO% %PORTA%
(
echo FROM php:8.2.4-fpm
echo RUN pecl install xdebug
echo RUN docker-php-ext-enable xdebug
echo RUN curl -sS https://getcomposer.org/installer ^| php -- --install-dir=/usr/local/bin --filename=composer
echo RUN apt update -y \
echo        ^&^& apt install git zip -y 
echo RUN docker-php-ext-install pdo pdo_mysql
echo WORKDIR /%NOMEPROGETTO%
)>Dockerfile

:laravel
echo creazione progetto laravel

xcopy %rootPath%\conf . /E /H /C /I /Q  
call composer create-project laravel/laravel project

cd %rootPath%
rem DOCKER-COMPOSE
(
echo.    
echo # microservice %NOMEPROGETTO%
echo   %NOMEPROGETTO%:
echo     container_name: %NOMEPROGETTO%
echo     build:
echo       context: .
echo       dockerfile: microservices/%NOMEPROGETTO%/Dockerfile
echo     entrypoint: php -S 0.0.0.0:80 -t public
echo     environment:
echo       XDEBUG_MODE: develop,debug
echo       XDEBUG_CONFIG: client_host=host.docker.internal client_port=9003
echo     volumes:
echo       - ./microservices/%NOMEPROGETTO%/project:/%NOMEPROGETTO% # cartella progetto
echo       - ./microservices/%NOMEPROGETTO%/xdebug/docker-php-ext-xdebug.ini:/usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini # impostazioni per debug
echo       - ./microservices/%NOMEPROGETTO%/php/php.ini:/usr/local/etc/php/php.ini # impostazioni php
echo     extra_hosts:
echo       - "host.docker.internal:host-gateway"
echo     ports:
echo       - %PORTA%:80
)>>docker-compose.yml

docker compose build --no-cache
docker compose up -d

echo microservizio pronto
GOTO fine

:progetto_esiste 
    echo microservizio %NOMEPROGETTO% gia' presente

:fine
    cd %rootPath%
    copy init\*.* microservices\%NOMEPROGETTO%\project\*.*
    copy init\database.php microservices\%NOMEPROGETTO%\project\config\*.*
    echo buon lavoro!