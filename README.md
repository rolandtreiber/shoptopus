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

![Screenshot](./docs/screenshots/mkcert-ssl.png)

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

![Screenshot](./docs/screenshots/containers-running.jpg)

## Composer packages
- Run `$ docker-compose run sh-composer install`

![Screenshot](./docs/screenshots/composer-install.jpg)

## Set up the databases
> The database host in your .env should be set to the mysql container's name (sh-mysql)

## Clear the config
- Run `$ docker-compose run sh-artisan optimize:clear`

## Migrations and test data
- Run `$ docker-compose run sh-artisan shop:fresh --seed`

## Tests
- Run `$ docker-compose run sh-artisan test`

![Screenshot](./docs/screenshots/tests.jpg)

## Connect to the databases
Both databases are available on `127.0.0.1:3306`  
Local development credentials are `homestead` and `secret`  
The databases are `shoptopus` and `shoptopus_logs`

![Screenshot](./docs/screenshots/db-connection.jpg)

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

![Screenshot](./docs/screenshots/elasticsearch.jpg)

# Kibana
Available 
- externally (from your machine) on http://kb.shoptopus.test:5601
- internally (from another container) on http://sh-kibana.test:5601

![Screenshot](./docs/screenshots/kibana.jpg)

# Laravel Scout
Products are imported into Elasticsearch and we can take advantage of the full text search capabilities.
The `$ docker-compose run sh-artisan shop:fresh --seed` command automatically flushes and imports product data.

Alternatively you can call: 
- `$ docker-compose run sh-artisan scout:flush "App\Models\Product"` to delete the product indices
- `$ docker-compose run sh-artisan scout:import "App\Models\Product"` to import the product indices

## Accessing the data in Kibana
- Navigate to **hanburger menu -> Management -> Stack Management**
- Navigate to **Kibana -> Index Patterns**
- You should see **products*** listed as available index pattern.\
- Type products* into the textbox and click **Create index pattern**\
- Navigate to **hamburger menu -> Analytics -> Discover**
- Select **products***
- You should see your data

![Screenshot](./docs/screenshots/products-index.png)

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

# Larastan

[Larastan](https://github.com/nunomaduro/larastan) is a wrapper for Phpstan, that is a code quality tool for php applications. It is optimized specifically to Laravel.

> According to the docs, the main benefits are:
>- Adds static typing to Laravel to improve developer productivity and code quality
>- Supports most of Laravel's beautiful magic
>- Discovers bugs in your code

Larastan is installed for Shoptopus and the code should not have any error flagged by it.
### Running the analysis
Once the composer packages are installed, simply run:
```./vendor/bin/phpstan --memory-limit=2G```\
There is also a shell script as a shorthand for the exact same thing in the `phpstan` file.\
To run it, simply use: ```$ ./phpstan```

Ideally you should see **[OK] No errors** displayed at the end like so:
![Screenshot](./docs/screenshots/larastan-no-errors.png)

In case there is any error, either correct it or ignore it by adding ```// @phpstan-ignore-next-line``` above the problematic line.\
In the latter case, it would be nice to also provide a short explanation why the error was not fixable.

