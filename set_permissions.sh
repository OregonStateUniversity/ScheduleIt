# All files and folders
chmod -R 755 *
find . -type f -exec chmod 644 {} \;

# dotfiles in root
chmod 755 .htaccess
chmod 600 .editorconfig
chmod 600 .gitignore
chmod 600 .prettierrc
chmod 600 composer*

# Scripts
chmod 700 *.sh

# Private folders
chmod 700 bin
chmod 700 docs
