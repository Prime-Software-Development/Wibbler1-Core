<?php
// Change directory to current location - allows scripts to run via cron properly
chdir( __dir__ );

require '../vendor/autoload.php';

// Name of the class to use as the main index for controllers
$index_class = 'welcome';

/**
 * Name of the method to use as the main index within the controllers
 */
$index_method = 'index';

// Options for environment are development or production
define('ENVIRONMENT', 'development');

// Definition of what this area is used for
define( 'SYSTEMAREA', 'Admin' );

// Switch on the current environment to set error reporting
switch (ENVIRONMENT)
{
	case 'development':
		error_reporting(E_ALL);
		ini_set('display_errors', '1');
		break;
	case 'production':
		error_reporting(0);

		if (extension_loaded('newrelic')) {
			newrelic_set_appname( "App Name" );
		}
		break;
}

/**
 * Path to  /web1
 */
define( 'BASEPATH', str_replace("\\", "/", __dir__) );

/**
 * Path to the common file area
 */
define( 'COMMONPATH', str_replace("\\", "/", __dir__) . '/../common/' );

/**
 * Path to this directory's controllers
 */
define( 'CONTROLLERPATH', str_replace("\\", "/", __dir__ . '/../common/web1/') );

/**
 * File upload root path
 */
define('UPLOADPATH', str_replace("\\", "/", __dir__) . '/../common/uploads/');

/**
 * Temporary file upload path
 */
define('TEMPPATH', UPLOADPATH . 'tmp/');

$load_modules = array('urls', 'input', 'twig');
$load_helpers = array();
$twig_options = array( 'template_dir' => COMMONPATH . "/templates/web1", 'cache_dir' => COMMONPATH . "/cache");


// Initiate the system
new \Trunk\Wibbler\Wibbler();