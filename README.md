# CoolRunner Utility Package 

## Installation Guide

Add this to the project composer repositories.
````json
...
  "repositories": [
    {
      "type": "vcs",
      "url": "git@github.com:CoolRunner-dk/coolrunner-utils.git"
    }
  ]
````

Next we'll tell composer to fetch the package 

```properties
composer require coolrunner/utils
```
Add the following env properties the dotenv file, and fill the variables in curly brackets.
```dotenv
LOGGING_DB_CONNECTION=mysql
LOGGING_DB_HOST=mariadb
LOGGING_DB_PORT=3306
LOGGING_DB_USERNAME={username}
LOGGING_DB_PASSWORD={password}
```
