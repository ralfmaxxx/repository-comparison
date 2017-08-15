# Application skeleton

[![Build Status](https://travis-ci.org/ralfmaxxx/repository-comparison.svg?branch=master)](https://travis-ci.org/ralfmaxxx/repository-comparison)

## Requirements

It requires `docker` and `docker-compose`.

## Travis

Check it out [here](https://travis-ci.org/ralfmaxxx/repository-comparison)

## Installation

In project directory run command:

```bash
docker-compose -f infrastructure/docker/docker-compose.yml up -d
```

To install project you have to run this:

```bash
docker-compose -f infrastructure/docker/docker-compose.yml exec php /bin/bash -c "wait-for.sh mysql:3306 && composer install --no-interaction"
```

Next you can visit:

```
http://localhost
```

## Running tests

If you want to run all tests:

```bash
docker-compose -f infrastructure/docker/docker-compose.yml exec php /bin/bash -c "bin/phing all"
```

Which consists of:

```bash
docker-compose -f infrastructure/docker/docker-compose.yml exec php /bin/bash -c "bin/phing quality"
```

and:

```bash
docker-compose -f infrastructure/docker/docker-compose.yml exec php /bin/bash -ic "bin/phing tests"
```
