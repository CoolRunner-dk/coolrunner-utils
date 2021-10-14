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

---
### Arr
Available Array Macros, implemented from this package 

[`makeArray`](#makeArray)
[`fromCsv`](#fromCsv)
[`fromXml`](#fromXml)
[`nestByPrefix`](#nestByPrefix)
[`prefix`](#prefix)
[`flatten`](#flatten)
[`isMultidimensional`](#isMultidimensional)
[`isAssociative`](#isAssociative)
[`renameKeys`](#renameKeys)
[`mapKeys`](#mapKeys)
[`mapKeysRecursive`](#mapKeysRecursive)
[`mask`](#mask)
[`toCss`](#toCss)

<a name="makeArray"></a> *# makeArray()*
```php
Arr::makeArray();
```
<a name="fromCsv"></a> *# fromCsv()*
```php
Arr::fromCsv();
```
<a name="fromXml"></a> *# fromXml()*
```php
Arr::fromXml();
```
<a name="nestByPrefix"></a> *# nestByPrefix()*
```php
Arr::nestByPrefix();
```
<a name="prefix"></a> *# prefix()*
```php
Arr::prefix();
```
<a name="flatten"></a> *# flatten()*
```php
Arr::flatten();
```
<a name="isMultidimensional"></a> *# isMultidimensional()*
```php
Arr::isMultidimensional();
```
<a name="isAssociative"></a> *# isAssociative()*
```php
Arr::isAssociative();
```
<a name="renameKeys"></a> *# renameKeys()*
```php
Arr::renameKeys();
```
<a name="mapKeys"></a> *# mapKeys()*
```php
Arr::mapKeys();
```
<a name="mapKeysRecursive"></a> *# mapKeysRecursive()*
```php
Arr::mapKeysRecursive();
```
<a name="mask"></a> *# mask()*
```php
Arr::mask();
```
<a name="toCss"></a> *# toCss()*
```php
Arr::toCss();
```
---

### Builder


---
### Carbon

---

## Aliases

### Num

---
### Bytes

---
