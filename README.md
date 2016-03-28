# basic-Api
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/lebaz20/basic-Api/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/lebaz20/basic-Api/?branch=master) [![Build Status](https://scrutinizer-ci.com/g/lebaz20/basic-Api/badges/build.png?b=master)](https://scrutinizer-ci.com/g/lebaz20/basic-Api/build-status/master)

basic Api using [symfony 3 framework](http://symfony.com/doc/current/index.html)

First authenticate with User Credentials via `/authenticate/user.json` and token is returned as a value for key 'token'

Then Token is used for organizations calls via `/rest/organizations.json` ,where token is sent in the header as `HTTP_Authorization: Bearer {Token}`

Token expire every 10 minutes by default

Public and Private keys used to secure token should be renewed via the command `bin/console rest:generateAuthKey`