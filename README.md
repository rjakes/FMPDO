# FMPDO

FMPDO is a php class that is designed to replace FileMaker.php when converting legacy FileMaker web applications to use a SQL database. The tables in the legacy solution can be converted to SQL all at once, or individually, as required.


## Features
### Minimal changes to legacy apps
* FMPDO methods calls and responses are the same format as FileMaker.php

### Easy to Integrate
* Convert all or some of your tables to SQL

### Flexible
* Choose any major SQL database, easily change databases at a later date (thanks to PDO)


 
## System Requirements

### PHP 5.3
### PDO Driver for desired database
* MySQL and SQLite are already in most PHP builds




## License

FMPDO is free for commercial and non-commercial use, licensed under the business-friendly standard MIT license.


# FMPDO Documentation

* index.php contains sample calls to supported methods, more formal docs are on the way

## Example Quickstart
* Put the code in a place where your browser can load it (existing web root or create a host)
* Import sample_data/fmpdo.sql into your favorite SQL database
* Edit config.php with your connection settings
* Load index.php with your browser


## Conversion Quickstart
* Create SQL tables that mirror existing FMP tables
* If you intend to use FileMaker External SQL Sources, ensure that the SQL columns are in the same position as the FileMaker fields
* Add or change the database initialization call


```
$fm = new FileMaker(); // this is the FileMaker.php object

$fmpdo = new FMPDO($sql_connect_parameters); // this is your SQL database connection

// if you are changing all table to SQL at once, you can do this:
$fm = new FMPDO($sql_connect_parameters);
```

Locate commands that you wish to convert to SQL:


```
$find = $fm->newFindCommand($fmpLayout); // new find command for FileMaker
$find = $fmpdo->newFindCommand($sqlTable) // a new FMPDO_Command_Find object

// subsequent method calls to $find, such as $find->setField() and execute() do not require modification
```


## Tricky Stuff
* Server side scripts not supported
* Relate sets from web layouts with portals need to be broken out into multiple calls (but the resulting FMPDO_Result object behaves the same as the FileMaker "relatedSet".
* Repeating fields not supported (yes Virginia, people have used repeating fields in FileMaker Web Publishing)

# Issues
* This is alphaware that is incomplete and has known security issues, see https://github.com/rjakes/FMPDO/issues before using in production.


