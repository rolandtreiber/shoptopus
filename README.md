```
       MMMMMMMMM                                                                                                        
     MMMMMMMMMMMMM                  MMM                                                                                 
    MMMMMMMMMMMMMMM                 MMM                               OMM~                                              
    MMMMMMMMMMMMMMMM                MMM            .                  OMM=                   .            .             
   MMMMMMMMMMMMMMMMM        ~MMMMM  MMM?MMMMM  . MMMMMM    MMMMMMMM  7MMMMMM   MMMMMM    MMMMMMMM   MMM    MMM .MMMMMM  
   MMMM. MMMMM. MMMM.      ZMMMMMM, MMMM  MMMM. MMMMMMMMM  MMMM MMMM 7MMMMMM .MMMMOMMMM  MMMM $MMM  MMM    MMM MMMMMMM  
  . MMMMMMMMMMMMMMMD       +MMMI    MMM    MMM MMM    MMM. MM.   +MMM 7MM7   MMM   .MMM  MMM    MMM MMM    MMM MMMM     
.MM :MMMMMMMMMNMMMM MMM      NMMMMM MMM    MMM.MMM    MMM  MM     MMM 7MM7   MMM    OMM7 MMM   .MMM.MMM    MMM   MMMMMO 
MMM. MMMMMM=8MMMMM  MMM     MM .MMM.MMM    MMM NMMM  MMMM  MMM   MMM   MMM    MMM. +MMM  MMM:  MMMM MMM$  MMMM  MM  MMM 
MMMMMMMMMMMMMMMMMMMMMMM     MMMMMMZ MMM    MMM   MMMMMMM   MMMMMMMM .  MMMMM  .MMMMMMM   MMMMMMMMI   MMMMMMMMM DMMMMMM. 
IMMMMMMMMMMMMMMMMMMMMMM      .  .                  .       MMM                           MMM .                          
  MMMMMMMMMMMMMMMMMMM                                      MMM                           MMM                            
 .MMMMMMMMM MMMMMMMMM                                      MMM                           MMM                            
  MMMMMMM     MMMMMMMN  
```  

# Introduction
Welcome to Shoptopus. It is a multi-purpose e-commerce platform based on Laravel.

# Installation
## Docker
- Use the official [guide](https://docs.docker.com/engine/install/) to install docker on your system
- Use the official [guide](https://docs.docker.com/compose/install/) to install docker compose

## Install the ssl certificates
- Install [mkcert](https://mkcert.org/) on your system.
- Once mkcert is available, locate the following folder: `[PROJECT ROOT]/docker-config/reverse-proxy/ssl`
- Then inside this folder, run `$ mkcert -install shoptopus.test`
- If all is well, you should see a message saying your certificate has been installed as well as see `shoptopus.test.key` and `shoptopus.test-key.pem` files appearing in the folder.

> You do not need to do anything with the certificates. They will be picked up and used by traefik.

## Add the url-s to your hosts file.
- Locate the `/etc/hosts` file in your system and add the following values to it:
```
127.0.0.1  shoptopus.test
127.0.0.1  es.shoptopus.test
127.0.0.1  kb.shoptopus.test
```

## Start the containers
- Run `$ docker-compose up -d`

## Composer packages
- Run `$ docker-compose run sh-composer install`

## Set up the databases
> The database host in your .env should be set to the mysql container's name (sh-mysql)

## Clear the config
- Run `$ docker-compose run sh-artisan optimize:clear`

## Migrations and test data
- Run `$ docker-compose run sh-artisan shop:fresh --seed`

## Tests
- Run `$ docker-compose run sh-artisan test`

## Connect to the databases
Both databases are available on `127.0.0.1:3306`  
Local development credentials are `homestead` and `secret`  
The databases are `shoptopus` and `shoptopus_logs`

# Commands
You can use artisan and composer commands as normal, however you need to **prepend docker-compose run**
### Examples
`$ php artisan tinker` -> `$ docker-compose run sh-artisan tinker`\
`$ php artisan test` -> `$ docker-compose run sh-artisan test`\
`$ php artisan composer install` -> `$ docker-compose run sh-composer install`

# ElasticSearch
Available 
- externally (from your machine) on http://es.shoptopus.test:9200
- internally (from another container) on http://sh-elasticsearch.test:9200

# Kibana
Available 
- externally (from your machine) on http://kb.shoptopus.test:5601
- internally (from another container) on http://sh-kibana.test:5601

# Audit Viewer utility
![Screenshot](./docs/screenshots/audit-viewer.png)
The audit viewer utility is a Spring Boot application with a "baked-in" React frontend all in one jar file for convenience.
It connects to the logs database and allows easy access to the audits providing advanced filtering and searching features.

The simplest way to start the application is to run:\
```$ java -jar ./audit-viewer.jar```
This will start the application with the following default settings:
- POST: 5555
- DB_HOST: 127.0.0.1
- DB_NAME: shoptopus_logs
- DB_USER: root
- DB_PASSWORD: secret

In case you need to override any of the above, you can do so by specifying them as parameters like so:\
```java -jar ./audit-viewer.jar --PORT=1111 --DB_HOST=127.0.0.1 --DB_NAME=shoptopus_logs --DB_USER=root --DB_PASSWORD=secret```

You can access the application through the specified port, such as http://localhost:5555

This utility was designed with production use in mind allowing for easy tracing and debugging in case something would behave incorrectly.
