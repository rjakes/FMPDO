# FmPdo

FmPdo is a drop-in php module for refactoring legacy FileMaker.php web applications to use a SQL database.
 
FmPdo provides alternative implementation of FileMaker.php functions so that changes to existing application logic are
reduced to the absolute minimum. In many cases, all that is required is refactoring the adapter configs.

The legacy solution can be refactored to a SQL backend all at once, or incrementally, as required.

## Features

### Minimal changes to legacy apps
* FmPdo methods calls and responses are the same format as FileMaker.php

### Easy to Integrate
* Refactor all or some of your persistence to SQL tables

### Flexible
* Choose any major SQL database supported by PDO; easily change databases at a later date (thanks to PDO).
 
## System Requirements

*  PHP 5.3 to 7.0
    * PHP 5.3 and 5.4 support is deprecated
    * PHP 5.5+ is recommended
    * Use of (class name scalars)[https://wiki.php.net/rfc/class_name_scalars] is planned, and this will move minimum
    version up to 5.5
*  PDO Driver for desired database
    * MySQL and SQLite are included by default in most PHP stacks


## License

FmPdo is free for commercial and non-commercial use, licensed under the business-friendly standard MIT license.


# FmPdo Documentation

* index.php contains sample calls to supported methods, more formal docs are on the way

## Example Quickstart
* Put the code in a place where your browser can load it (existing web root or create a host)
* Import sample_data/fmpdo.sql into your favorite SQL database
* Edit config.php with your connection settings
* Load index.php with your browser

## Conversion Quickstart
* Create SQL tables that mirror existing FMP tables
* If you intend to use FileMaker External SQL Sources, ensure that the SQL columns are in the same position as the
FileMaker fields
* Add or change the database adapter

```
// this is a typical FileMaker.php instantiation
$databaseName = 'myDB';
$server = '127.0.0.1';
$userName = 'uname';
$passWord = 'pword';

$fm = new FileMaker($databaseName,$server,$userName,$passWord);
```

```
// this is a typical FmPdo instantiation
$dbConfig = array(
    'driver' => 'mysql',
    'host' => '127.0.0.1',
    'port' => '3306',
    'database' => 'fmpdo',
    'user' => 'root',
    'password' => 'root'
);

$fmPdo = new FmPdo($dbConfig);

// if you are changing all table to SQL at once, you can do this:
$fm = new FmPdo($db_config);
```

Locate commands that you wish to convert to SQL:

```
$find = $fm->newFindCommand($fmpLayout); // new find command for FileMaker
$find = $fmPdo->newFindCommand($sqlTable) // a new FmpdoCommandFind object

// subsequent method calls to $find, such as $find->setField() and execute() do not require modification
```

## Tricky Stuff
* Server side scripts not supported
* Related sets from web layouts with portals need to be broken out into multiple calls (but the resulting Result object
behaves the same as the FileMaker "relatedSet".
* Repeating fields not supported (yes Virginia, people have used repeating fields in FileMaker Web Publishing)

# Issues
* This is alphaware that is incomplete and has known security issues. See [https://github.com/rjakes/FmPdo/issues]. Use
in production at your own risk.
