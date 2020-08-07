#!/bin/sh

vendor/bin/phpunit tests --configuration config/phpunit.xml --bootstrap constants.inc.php --testdox
