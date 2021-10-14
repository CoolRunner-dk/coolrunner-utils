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

## Guzzle Manager



## Logging

### Client Log

---
### Input Log

---
### Auditing


## Mixins

### Str
Available [\Str](https://laravel.com/docs/8.x/helpers#strings-method-list) Macros, implemented from this package

[`utf8Encode`](#utf8Encode)
[`replaceLastOccurrence`](#replaceLastOccurrence)
[`randomNumber`](#randomNumber)
[`detectCsvDelimiter`](#detectCsvDelimiter)
[`isBase64`](#isBase64)
[`randomString`](#randomString)
[`normalize`](#normalize)

---
### Arr
Available [\Arr](https://laravel.com/docs/8.x/helpers#arrays-and-objects-method-list) Macros, implemented from this package 

[`makeArray`](#makeArray)
[`fromCsv`](#fromCsv)
[`fromXml`](#fromXml)
[`nestByPrefix`](#nestByPrefix)
[`prefix`](#prefix)
[`flattenWithKeys`](#flattenWithKeys)
[`isMultidimensional`](#isMultidimensional)
[`isAssociative`](#isAssociative)
[`renameKeys`](#renameKeys)
[`mapKeys`](#mapKeys)
[`mapKeysRecursive`](#mapKeysRecursive)
[`mask`](#mask)
[`toCss`](#toCss)

---

### Builder
Available [Builder](https://laravel.com/docs/8.x/queries) Macros, implemented from this package

[`getRawQuery`](#getRawQuery)
[`getRawQueryParts`](#getRawQueryParts)

---
### Carbon
Available [Carbon](https://carbon.nesbot.com/docs/) Macros, implemented from this package

[`lastBusinessDay`](#lastBusinessDay)

---

## Aliases

### Num

---
### Bytes

---
