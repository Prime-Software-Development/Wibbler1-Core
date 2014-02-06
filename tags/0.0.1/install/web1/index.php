<?php
namespace Wibbler;

$load_modules = array('urls', 'twig', 'propel');
$load_helpers = array();

// Name of the class to use as the main index for controllers
$index_class = 'welcome';

/**
 * Name of the method to use as the main index within the controllers
 */
$index_method = 'index';

// Path to the system folder
define('BASEPATH', str_replace("\\", "/", __dir__));
define('CONTROLLERPATH', str_replace("\\", "/", __dir__ . '/../common/web1/'));

// Initiate the system
new \Trunk\Wibbler\Wibbler();