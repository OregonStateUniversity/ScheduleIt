# All files and folders
chmod -R 755 *
find . -type f -exec chmod 644 {} \;

# Config files in root
chmod 755 .htaccess
chmod 600 .editorconfig
chmod 600 .gitignore
chmod 600 composer*

# Executable scripts
chmod 700 *.sh
chmod 700 .git/hooks/pre-commit

# Folders with executables
chmod -R 700 bin
chmod 755 bin
chmod -R 700 vendor/bin
chmod 755 vendor/bin
chmod -R 700 vendor/squizlabs/php_codesniffer/bin
chmod 755 vendor/squizlabs/php_codesniffer/bin

# Private folders
chmod 700 docs
