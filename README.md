# Repository Comparison

[![Build Status](https://travis-ci.org/ralfmaxxx/repository-comparison.svg?branch=master)](https://travis-ci.org/ralfmaxxx/repository-comparison)

## Requirements

It requires `docker` and `docker-compose`.

## Versions

* `1.0.0` - on Monday (this tag can be used for checking this)

* `1.0.1` - remove not used files

* `1.1.0` - fix domain

* `1.2.0` - fix performance + better error handling

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

## Generating documentation

Please run:

```bash
docker-compose -f infrastructure/docker/docker-compose.yml exec php /bin/bash -ic "bin/phing documentation"
```

Visit `http://localhost/api.json`. You have to use some client for user interface. Please try this one: `https://editor.swagger.io/` (or find a docker container).

But keep in mind that you have to turn off CORS to have it up and running if you chose `https://editor.swagger.io/`.

## Remarks

Unfortunately I did not create user interface for API. This can be tested using Postman or documentation.