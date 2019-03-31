[![Latest Stable Version](https://poser.pugx.org/codedge/trnsltr/v/stable?format=flat-square)](https://packagist.org/packages/codedge/trnsltr)
[![Build Status](https://travis-ci.org/codedge/trnsltr.svg?branch=master)](https://travis-ci.org/codedge/trnsltr)
[![codecov](https://codecov.io/gh/codedge/trnsltr/branch/master/graph/badge.svg)](https://codecov.io/gh/codedge/trnsltr)
[![Total Downloads](https://poser.pugx.org/codedge/trnsltr/downloads?format=flat-square)](https://packagist.org/packages/codedge/trnsltr)
[![License](https://poser.pugx.org/codedge/trnsltr/license?format=flat-square)](https://packagist.org/packages/codedge/trnsltr)

# trnsltr - a translation caching service backed by DeepL

**trnsltr** is a translation caching service that stores already translated data in (currently) Redis. Words and texts
that are not already in cache are going to be send to the [DeepL](https://www.deepl.com) API, will be translated, stored
in cache and then being returned.

It brings a separate [Vue.js](https://vuejs.org/) frontend and a [Slim Framework](https://www.slimframework.com) based backend.

## Backend

### Configuration

The backend configuration can be done via environment variables, easily to be changed in a `.env` file. You can check
the `.env.example` file for configuration vars.

**Redis**
* `REDIS_HOST`: default `127.0.0.1`
* `REDIS_PORt`: default `6379`
* `REDIS_DATABASE`: default `1` 

If you are fine with the default you can remove all REDIS_* config keys from your `.env` file.

**DeepL**
* `DEEPL_HOST`: default `https://api.deepl.com`
* `DEEPL_API_TOKEN` [provide your API token]

## Frontend

Currently the frontend can only connect to the backend service when they are on the same host via `localhost:8081`.
