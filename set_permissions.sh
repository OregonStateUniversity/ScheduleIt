# All files and folders
chmod -R 755 *
find . -type f -exec chmod 644 {} \;

# Config files in root
chmod 755 .htaccess
# Add -f to files that aren't necessary to be on server
chmod 600 -f .editorconfig
chmod 600 -f  .gitignore
chmod 600 composer*

# Executable scripts
chmod 700 *.sh
chmod 700 -f .git/hooks/pre-commit
chmod 700 -f .git/hooks/pre-push

# Folders with executables
chmod -R 700 bin
chmod 755 bin
chmod -R 700 vendor/bin
chmod 755 vendor/bin
chmod -R 700 vendor/squizlabs/php_codesniffer/bin
chmod 755 vendor/squizlabs/php_codesniffer/bin
chmod 700 vendor/phpunit/phpunit/phpunit

# Folders with executables
chmod -R 700 bin
chmod 755 bin
chmod -R 700 vendor/bin
chmod 755 vendor/bin
chmod 700 vendor/phpunit/phpunit/phpunit

# Private folders
chmod 700 -f docs
