#!/bin/sh

# All files and folders
chmod -R 755 *
find . -type f -exec chmod 644 {} \;

# Config files in root
chmod 755 .htaccess
chmod 755 scheduleit/.htaccess
# Add -f to files that aren't necessary to be on server
chmod -f 600 .editorconfig
chmod -f 600 .gitignore
chmod 600 composer*

# Executable scripts
chmod 700 *.sh

# Folders with executables
chmod -R 700 bin
chmod 755 bin
chmod -Rf 700 vendor/bin
chmod -f 755 vendor/bin
chmod -Rf 700 vendor/squizlabs/php_codesniffer/bin
chmod -f 755 vendor/squizlabs/php_codesniffer/bin
chmod -Rf 700 vendor/robmorgan/phinx/bin
chmod -f 755 vendor/robmorgan/phinx/bin
chmod -f 700 vendor/phpunit/phpunit/phpunit

# Private folders
chmod -f 700 docs
