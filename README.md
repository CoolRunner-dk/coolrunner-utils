# CoolRunner Utility Package

## Installation Guide

Add this to the project composer repositories.

```json
...
  "repositories": [
    {
      "type": "vcs",
      "url": "git@github.com:CoolRunner-dk/coolrunner-utils.git"
    }
  ]
```

Next we'll tell composer to fetch the package

```properties
composer require coolrunner/utils
```

Add the following env properties the dotenv file, and fill the variables in curly brackets.

```dotenv
LOGGING_DB_CONNECTION=mysql
LOGGING_DB_HOST={host}
LOGGING_DB_PORT=3306
LOGGING_DB_USERNAME={username}
LOGGING_DB_PASSWORD={password}
```

```dotenv
ADVISERING_DB_CONNECTION=mysql
ADVISERING_DB_HOST={host}
ADVISERING_DB_PORT=3306
ADVISERING_DB_USERNAME={username}
ADVISERING_DB_PASSWORD={password}
```

It's also possible to publish the package config.

```properties
php artisan vendor:publish --provider "CoolRunner\Utils\Providers\UtilsServiceProvider" --tag="config"
```
