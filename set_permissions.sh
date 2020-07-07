# All files and folders
chmod -R 755 *
find . -type f -exec chmod 644 {} \;

# Config files in root
chmod 755 .htaccess
# Add -f to files that aren't necessary to be on server
chmod -f 600 .editorconfig
chmod -f 600 .gitignore
chmod 600 composer*

# Executable scripts
chmod 700 *.sh
chmod -f 700 .git/hooks/pre-commit
chmod -f 700 .git/hooks/pre-push

# Folders with executables
chmod -R 700 bin
chmod 755 bin
chmod -Rf 700 vendor/bin
chmod -f 755 vendor/bin
chmod -Rf 700 vendor/squizlabs/php_codesniffer/bin
chmod -f 755 vendor/squizlabs/php_codesniffer/bin
chmod -f 700 vendor/phpunit/phpunit/phpunit

# Folders with executables
chmod -R 700 bin
chmod 755 bin
chmod -Rf 700 vendor/bin
chmod -f 755 vendor/bin
chmod -f 700 vendor/phpunit/phpunit/phpunit

# Private folders
chmod -f 700 docs
