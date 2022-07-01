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

## Start the containers
- Run `$ docker-compose up -d`

## Composer packages
- Run `$ docker-compose run composer install`

## Set the databases host
> In order to let the containers talk to each other, we need their ips.
- Run `docker inspect -f '{{.Name}} - {{range .NetworkSettings.Networks}}{{.IPAddress}}{{end}}' $(docker ps -aq)`

You should see an output similar to this:
```
/shoptopus_artisan_run_50ccf76b1cf3 - 
/shoptopus_artisan_run_9e0b7c656046 - 
/shoptopus_artisan_run_8531083b13ac - 
/artisan - 
/nginx - 172.22.0.5
/composer - 
/php - 172.22.0.3
/npm - 
/mailhog - 172.22.0.6
/mysql - 172.22.0.2
/redis - 172.22.0.4
```
- Take the ip of the mysql container (yours will be different)
- Update the `DB_HOST` in your .env file for both the `shoptopus` and `shoptopus_logs` databases.

## Clear the config
- Run `$ docker-compose run artisan optimize:clear`

## Migrations and test data
- Run `$ docker-compose run artisan shop:fresh --seed`

## Tests
- Run `$ docker-compose run artisan test`

## Connect to the databases
Both databases are available on `127.0.0.1:3306`  
Local development credentials are `homestead` and `secret`  
The databases are `shoptopus` and `shoptopus_logs`

# Commands
You can use artisan and composer commands as normal, however you need to **prepend docker-compose run**
### Examples
`$ php artisan tinker` -> `$ docker-compose run php artisan tinker`
`$ php artisan test` -> `$ docker-compose run php artisan test`
`$ php artisan composer install` -> `$ docker-compose run composer install`
