# [UPDATED: CORS, Docker Redis & Unit test](#updated)
- [CORS](#cors)
- [Redis in Docker](#redis-in-docker)
- [Unit test](#unit-test)
- [Updated Installation](#updated-installation)

# Table of Contents
- [Installation](#installation)
- [CORS](#cors)
- [APIs / Postman Collection](#apis--postman-collection)
  - [Documentation](#Documentation)
  - [Collection](#Collection)
- [Implementation](#implementation)
   - [Docker Environment Setup](#docker-environment-setup)
   - [Scrapper Implementation](#scrapper-implementation)
   - [APIs](#apis)
      - [Create/Add Info](#createadd-info)
      - [Index](#index)
      - [Update](#update)
      - [Delete](#delete)
   - [Additionally Implemented](#additionally-implemented)
- [Remaining Tasks](#remaining-tasks)
- [NOTE](#note)
  - [Scraping](#scraping)
  - [Cache Clear](#cache-clear)


# Installation:
After pulling from the repository
- Copy `.env.example` and paste as `.env`. Change anything such as port number or db configuration if you feel necessary
- Run command `docker compose build --no-cache` for a fresh build
- Run command: `docker compose up -d`
- Go to the bash script of the php related container: run commnad: `docker compose exec php-service bash`
- **Inside the container:** 
  - To install composer packages, run command: `composer install`
  - To install npm packages, run command: `npm install`

# APIS / POSTMAN COLLECTION:

## Documentation
There are 4 APIs. This is postman collection link: https://documenter.getpostman.com/view/1449184/2s9Y5cu1WN 

Use it to understand the APIs in details.

## Collection
- This is the json file of the postman collection:  **Nordstreet Scrape.postman_collection.json** You can find it in the root directory


# Implementation
1. Docker environment setup is done for Nginx, PHP 8.2, MySQL 8 & Redis
2. Also, added a Postman image in docker to visualize the database.
3. Implemented Scrapper using puppeteer stealth (npm package) and scrape.do for free proxy server
4. Company profile information is extracted from scrapped data and stored in the database, using  Symfony 6.2 framework.
5. 4 APIs are implemented: 
   1. Create/Add info: 
      1. While storing, the registration codes are stored in redis. So that, next time for the existing codes, can reduce the unnecessary scrapping time.
      2. Because of this we don't need any duplicate data checking in DB. Hence, it also reduces DB query time.
   2. Index: 
      1. Pagination added. 
      2. Also make sure that, if the same pagination information is passed, then no DB operation will take place. It will take data from redis cache
   3. Update:
      1. Checked if the registration code is already exist in another row in DB. Because codes are unique. So, no 2 rows will have same code.
   4. Delete: If ID exist in the DB, then soft delete mechanism is used so that no data is completely removed, but only updated the deleted time.

**Additionally implemented:**
- **Custom validation rule** for each request.
- **Custom Exception handler** for individual error.
- **Log:** customized log handler for the error log, inside the project directory for error response. It helps to identify each error properly. Every day a new file (named with the datetime) will generate when the error occurs.
- **Added indexing** for column registration code

# Remaining tasks:
- RabbitMQ: I can implement it if extra time is provided.
- UI: I planned to do an UI using react or vue. I can implement it if extra time is provided.
- I skipped the additional task for now.


# NOTE:

### Scraping
I used [scrape.do](scrape.do). It provides a limited free plan with a token. \
For easy testing the API, I provide my scraping token in the env file. \
Each scrapping takes mane requests, because for each time 3 pages need to scrape in order to get correct company info. \
So, you can use your own token by opening an account in [scrape.do](scrape.do site.


### Cache clear:
If you face this error:
```bash
Failed to remove directory &quot;/var/www/project/var/cache/dev/ContainerCWmhSbR&quot;: rmdir(/var/www/project/var/cache/dev/._Ed+): Directory not empty (500 Internal Server Error) 
```
For this, **clear cache**: 
```shell
php bin/console cache:clear
```


# [UPDATED]
If you don't have this updated code, please update,

And also, check the env file for the update, in case you missed.

## CORS
- Run `composer install` to install the package for cors control.
- For this backend, allow CORS origin from specific site. \
  You can see this in `.env.example` file. The variable for this is: `CORS_ALLOW_ORIGIN=` \
  Update it in `.env` if you feel necessary.
- Then clear the cache: `php bin/console cache:clear`


## Redis in Docker
- updated docker compose and dockerfile for redis setup

## Unit test
- Latest phpunit package has conflict with latest symfony version. So, phpunit package is downgraded for this
- To run unit test, run command from inside docker container:
```shell
php bin/phpunit
```
- If you want to see each individual test case result, run command:
```shell
php bin/phpunit --testdox
```

### Note on Unit Test
- Faced API calling using WebTestCase class. So, instead, used guzzle client.
- The variable value `TEST_BASE_URL` in `.env.test` file used the nginx service name because of docker container. 
If you want to use the project directly then, you may use localhost
- **The proper way** should be, create a mock db so that it does not affect the real DB. But for this time being, I hit the main DB to test.
- 6 unit test cases are written. One of them is written as to be failed purposely. So, 5 test cases pass, 1 get failure.


# Updated Installation
- The dockerfile is updated, so build the docker file again.
```shell
docker compose build --no-cache
```

- Then run: `docker compose up -d`

- new package is installed for this, you have to run `composer install` or `composer update` command again to install the new packages.
```shell
docker compose exec php-service bash
```
```shell
composer install # or run, composer update
```

- **After installing everything run a cache clear command, because many things are updated now**
```shell
php bin/console cache:clear
```