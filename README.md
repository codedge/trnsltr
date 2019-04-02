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
* `REDIS_DATABASE`: default `0` 

If you are fine with the default you can remove all REDIS_* config keys from your `.env` file.

**DeepL**
* `DEEPL_HOST`: default `https://api.deepl.com`
* `DEEPL_API_TOKEN` [provide your API token]

**JWT (Json Web Tokens)**
For authentication [JWT (Json Web Tokens)](https://jwt.io/) are used. Please specify a strong secret to encrypt the token.

* `APP_API_SECRET`: default `secret`

A requirement for this to work is a working database connection to some `users` table where all your users are saved.
When this is set up, you can use the `/auth/token` route to get a valid token.

```shell
$ curl -d "email=john.doe@example.com&password=test123" -X POST http://trnsltr.localhost:8081/auth/token
```
Response:
```json
{
    "status":"Success",
    "token":"eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpZCI6MSwiZW1haWwiOiJobEBpbnF1ZW5jZS5jb20ifQ.qVLAR8MtHKxg38sCu7sWRZVjLdsd4eONHah4_HcrnHE"
}
```


## Frontend

You can run frontend and backend separately. To configure where your backend is reachable create a `.env` file in your `frontend/` folder
or copy the `.env.example` file.

The variable `VUE_APP_API_URL` is the url to the backend API. Under which URL you run the frontend is totally up to you.

For authentication against the backend please generate a valid token 

```shell
VUE_APP_API_URL=localhost
VUE_APP_API_TOKEN=abc123
```


