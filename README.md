PHP-Utilities
=============

PHP Utility Classes

Core utilities to get around in PHP

TSDatabase is a database handler that can be instantiated using the following method:

$TSDatabase = new TSDatabase("localhost", "root", "password", "database_name");

TSLogger is a logging class that will record user agent data.  Pass in database credentials to the constructor.

$TSLogger = new TSLogger("localhost", "root", "password", "database_name");

