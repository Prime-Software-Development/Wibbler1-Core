<?php
require '../vendor/autoload.php';

// Name of the class to use as the main index for controllers
$index_class = 'welcome';

/**
 * Name of the method to use as the main index within the controllers
 */
$index_method = 'index';

// Path to the system folder
define('BASEPATH', str_replace("\\", "/", __dir__));
define('COMMONPATH', str_replace("\\", "/", __dir__) . '/../common/');
define('CONTROLLERPATH', str_replace("\\", "/", __dir__ . '/../common/web1/'));


$load_modules = array('urls', 'input', 'twig');
$load_helpers = array();
$twig_options = array( 'template_dir' => COMMONPATH . "/templates/web1", 'cache_dir' => COMMONPATH . "/cache");


// Initiate the system
new \Trunk\Wibbler\Wibbler();