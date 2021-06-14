# config-migration
A simple (but effective!) system for managing different versions of configuration files and their settings.

[![Build Status](https://travis-ci.com/AndyM84/config-migration.svg?branch=master)](https://travis-ci.com/AndyM84/config-migration)

## Development Setup
There are no dependencies other than PHP (of course) to use the system, however if you'd like to use the source
code and run the tests, feel free to clone or download the repo and run the following commands from the repository
root:

```bash
composer update
vendor/bin/phpunit -c phpunit.xml ./Tests
```

## Installation
As with most things, I've made this available via [Composer](https://packagist.org/packages/andym84/config), you can add `"andym84/config": "^1.0"` to your `composer.json` or you can simply execute the command:

```
composer require andym84/config
```

Alternatively, of course, you're welcome to simply download the source code and reference it manually.

## Usage
Usage of the system requires at least two things:

1. A script that executes the `Migrator`
2. A folder that contains one or more instruction files

Assuming you'll use a directory called `cfgMigrations` for your instruction files, your usage may be as simple
as this:

```php
require('vendor/autoload.php');

use AndyM84\Config\ConfigContainer;
use AndyM84\Config\Migrator;

// Perform any required migrations
$migrator = new Migrator('cfgMigrations');
$migrator->migrate();

// Consume the config file for easy use
$cfg = new ConfigContainer(file_get_contents('siteSettings.json'));

echo($cfg->get('configVersion')); // Echos whatever version your configs are at after migration
```

Consider the following two instruction files in your `cfgMigrations` directory, `0-1.cfg` and `1-2.cfg`.

#### cfgMigrations/0-1.cfg
```
siteVersion[str] + 1.0.0
siteTitle[str] + Default Title
```

#### cfgMigrations/1-2.cfg
```
siteTitle > frontTitle
backTitle[str] + Default Backend Title
smtpHost[str] + localhost
```

If you run these through a migration, the system will produce the following `siteSettings.json` file:

```json
{
    "schema": {
        "configVersion": "int",
        "siteVersion": "str",
        "frontTitle": "str",
        "backTitle": "str",
        "smtpHost": "str"
    },
    "settings": {
        "configVersion": 2,
        "siteVersion": "1.0.0",
        "frontTitle": "Default Title",
        "backTitle": "Default Backend Title",
        "smtpHost": "localhost"
    }
}
```

## Config Migrations
Each config migration is a file with simple per-line instructions.  The file name is in the format `<VERSION1>-<VERSION2>.cfg`,
which allows the system to know which version it should look to migrate from and to.  Instructions contain 2-3 segments:

```
<field-name-and-type> <operator>[ <value>]
```

The first two segments are required, and the third (`value`) is optional depending on the operator being used.
The following operators are available:

```
+ Add field w/ value
> Rename field
= Change field value
- Remove field
```

Valid field types:

```
int Integer
flt Float
str String
bln Boolean
```

Finally, when used, the `value` segment can contain any character (excluding the newline), as well as these special
values:

```
""          Empty string
${propName} Interpolates the value of an existing property (will not be replaced if the property doesn't exist)
```

Finally, an example migration script, `3-4.cfg`, which migrates the config file from version 3 to 4:

```
someVersion[str] + 1.1.2
ownerName[str] + Andrew Male
ownerFirstName -
ownerLastName > ownerSurname
```

This file will perform the following actions, in order:

* Add `someVersion` string property with value `1.1.2`
* Add `ownerName` string property with value `Andrew Male`
* Remove `ownerFirstName` property
* Rename `ownerLastName` property to `ownerSurname`
* Set `configVersion` integer property to `4` based on the file name
