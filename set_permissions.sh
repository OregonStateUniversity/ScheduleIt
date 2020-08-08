#!/bin/sh

# All files and folders
chmod -R 755 config
chmod -R 755 db
chmod -R 755 lib
chmod -R 755 resources
chmod -R 755 static
chmod -R 755 tests
chmod -R 755 views
find config -type f -exec chmod 644 {} \;
find db -type f -exec chmod 644 {} \;
find lib -type f -exec chmod 644 {} \;
find resources -type f -exec chmod 644 {} \;
find static -type f -exec chmod 644 {} \;
find tests -type f -exec chmod 644 {} \;
find views -type f -exec chmod 644 {} \;

# Config files in root
chmod 755 .htaccess

# Executable scripts
chmod 700 *.sh

# Folders with executables
chmod -R 700 bin
chmod 755 bin
chmod -Rf 700 vendor/bin
chmod 755 vendor/bin
chmod -Rf 700 vendor/squizlabs/php_codesniffer/bin
chmod 755 vendor/squizlabs/php_codesniffer/bin
chmod -Rf 700 vendor/robmorgan/phinx/bin
chmod 755 vendor/robmorgan/phinx/bin
chmod 700 vendor/phpunit/phpunit/phpunit

# Just top level folder
chmod 755 vendor

# Private folders
chmod 700 docs
