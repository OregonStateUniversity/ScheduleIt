<?php

require_once dirname(__DIR__) . '/config.inc.php';
// Load the CAS library
require_once ABSPATH . '/vendor/jasig/phpcas/source/CAS.php';

// Based on examples provided for phpCAS
// https://github.com/apereo/phpCAS/blob/master/docs/examples/example_simple.php
// https://github.com/apereo/phpCAS/blob/master/docs/examples/example_custom_urls.php

// Example for a simple cas 2.0 client

// PHP Version 5

// @file     example_simple.php
// @category Authentication
// @package  PhpCAS
// @author   Joachim Fritschi <jfritschi@freenet.de>
// @author   Adam Franco <afranco@middlebury.edu>
// @license  http://www.apache.org/licenses/LICENSE-2.0  Apache License 2.0
// @link     https://wiki.jasig.org/display/CASC/phpCAS

// Enable debugging
if (ENVIRONMENT == 'test') {
    phpCAS::setDebug();
}

// Enable verbose error messages. Disable in production!
phpCAS::setVerbose(ENVIRONMENT == 'test');

// Initialize phpCAS
phpCAS::client(CAS_VERSION_2_0, $cas_host, $cas_port, $cas_context);

// For production use set the CA certificate that is the issuer of the cert
// on the CAS server and uncomment the line below
// phpCAS::setCasServerCACert($cas_server_ca_cert_path);

// For quick testing you can disable SSL validation of the CAS server.
// THIS SETTING IS NOT RECOMMENDED FOR PRODUCTION.
// VALIDATING THE CAS SERVER IS CRUCIAL TO THE SECURITY OF THE CAS PROTOCOL!
phpCAS::setNoCasServerValidation();

// Override the validation url for any CAS 1.0, 2.0 and 3.0 validation
// Example of the URL for the version of CAS 2.0 validation
phpCAS::setServerServiceValidateURL($cas_validate_url);

// Force CAS authentication
phpCAS::forceAuthentication();
