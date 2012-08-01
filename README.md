PHP REST SQL
============

A HTTP REST interface to MySQL written in PHP

Description
-----------

PHP REST SQL is a class used to open a HTTP REST interface to a MySQL database using PHP and a HTTP server. Using standard HTTP requests, the data in a database can be created, retrieved, modified and deleted.

Requirements
------------

PHP REST SQL was built and tested using Apache 2.0.45, PHP 4.3.4, and MySQL 3.23, although it should work with any version of PHP4 and MySQL and any HTTP server that will pass requests of all HTTP method types to PHP.

REST Browser
------------

The database can be queried using a regular Web Browser, but to send the appropriate HTTP PUT, POST and DELETE requests you'll need a REST browser.

The Poster Firefox extension allows you to easily craft HTTP requests from within Firefox.

Installation
------------

Place all the files in a directory in your Web servers docroot and edit the config file "phprestsql.ini" with your database information and the URL path to the directory.

To use tidy URLs, an example .htaccess file is included for the Apache Web server and the mod_rewrite module. Without mod_rewrite PHPRestSQL URLs will use the querystring and default document behavour to envoke the PHP script for the variety of URLs required by the RESTful interface.