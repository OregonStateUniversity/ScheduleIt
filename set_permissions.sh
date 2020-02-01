# All files and folders
chmod -R 755 *
find . -type f -exec chmod 644 {} \;

# Private files and folders
chmod 600 .gitignore
chmod 600 *.md
chmod 700 doc

# Scripts
chmod 700 *.sh
