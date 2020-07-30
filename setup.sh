#!/bin/sh

COMPOSER=bin/composer

if [ ! -f "$COMPOSER" ]; then
  echo "* Installing Composer..."
  EXPECTED_SIGNATURE="$(wget -q -O - http://composer.github.io/installer.sig)"
  php -r "copy('http://getcomposer.org/installer', 'composer-setup.php');"
  ACTUAL_SIGNATURE="$(php -r "echo hash_file('sha384', 'composer-setup.php');")"

  if [ "$EXPECTED_SIGNATURE" != "$ACTUAL_SIGNATURE" ]
  then
      >&2 echo 'ERROR: Invalid installer signature'
      rm composer-setup.php
      exit 1
  fi

  php composer-setup.php --quiet
  RESULT=$?
  rm composer-setup.php
  mv composer.phar bin/composer
fi

echo ""
echo "* Installing packages..."
bin/composer install
echo "* Running database migrations..."
vendor/bin/phinx migrate
echo ""
echo "* Setting file and folder permissions..."
sh set_permissions.sh

exit $RESULT
