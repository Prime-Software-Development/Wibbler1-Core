<?php

$paths = array(
	'Resources' => array(
		'path' => '/resources/'
	),
	'3rd Party' => array(
		'path' => '/resources/3rdparty/'
	),
	'Javascript' => array(
		'path' => '/resources/js/',
		'files' => array(
			'manage.js',
			'script.js',
			'search.js'
		)
	),
	'Css' => array(
		'path' => '/resources/css/',
		'files' => array(
			'style.css'
		)
	),

	'Application' => array(
		'path' => '/application/'
	),
	'Config' => array(
		'path' => '/application/config/',
		'files' => array(
			'autoload.php',
			'propel.php',
			'twig.php'
		)
	),
	'Controllers' => array(
		'path' => '/application/controllers/',
		'files' => array(
			'BaseController.php',
			'welcome.php',
			'BaseSearchController.php',
			'BaseSearchDataController.php'
		)
	),
	'SampleControllers' => array(
		'path' => '/application/controllers/user/',
		'files' => array(
			'data.php',
			'welcome.php'
		)
	),
	'Helpers' => array(
		'path' => '/application/helpers/'
	),
	'Modules' => array(
		'path' => '/application/modules/'
	),
	'Propel' => array(
		'path' => '/application/propel/'
	),
	'Templates' => array(
		'path' => '/application/templates/',
		'files' => array(
			'base.twig',
			'footer.twig',
			'navigation.twig',
			'secured.twig',
			'welcome.twig',
		)
	),
	'Search Templates' => array(
		'path' => '/application/templates/_search/',
		'files' => array(
			'base_search.twig',
			'base_search_body.twig',
			'base_search_empty.twig',
			'base_search_results.twig',
			'manage.twig',
			'search_bar.twig',
			'settings.twig'
		)
	),
	'Sample Templates' => array(
		'path' => '/application/templates/user/',
		'files' => array(
			'index.twig',
			'search_results.twig'
		)
	)

);

foreach ($paths as $index => $path) {
	echo $index;

	$dest_dir = __dir__ . '/../' . $path['path'];

	if (is_dir($dest_dir)) {
		echo ': OK<br/>';
	}
	else {
		echo ': Missing - ';
		if (mkdir($dest_dir)) {
			echo 'Created<br/>';
		}
		else {
			echo 'Failed!<br/>';
		}
	}

	if (isset($path['files'])) {
		echo "updating files<br/>";
		foreach ($path['files'] as $file) {
			$to_file_name = __dir__ . '/..' . $path['path'] . $file;
			$from_file_name = __dir__ . '/install' . $path['path'] . $file;

			if (!is_file($to_file_name)) {
				copy($from_file_name, $to_file_name);
				echo 'Copied from: ' . $from_file_name . '  to:' . $to_file_name . '<br/>';
			}
		}
	}
}

if (!is_file(__dir__ . '/../index.php')) {
	copy(__dir__ . '/install/index.php', __dir__ . '/../index.php');
}
if (!is_file(__dir__ . '/../.htaccess')) {
	copy(__dir__ . '/install/.htaccess', __dir__ . '/../.htaccess');
}