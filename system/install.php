<?php

$paths = array(
	'Application' => array(
		'path' => __dir__ . '/../application/'
	),
	'Config' => array(
		'path' => __dir__ . '/../application/config/',
		'files' => array(
			'/config/autoload.php',
			'/config/propel.php',
			'/config/twig.php'
		)
	),
	'Controllers' => array(
		'path' => __dir__ . '/../application/controllers/',
		'files' => array(
			'/controllers/welcome.php'
		)
	),
	'Helpers' => array(
		'path' => __dir__ . '/../application/helpers/'
	),
	'Modules' => array(
		'path' => __dir__ . '/../application/modules/'
	),
	'Propel' => array(
		'path' => __dir__ . '/../application/propel/'
	),
	'Templates' => array(
		'path' => __dir__ . '/../application/templates/',
		'files' => array(
			'/templates/welcome.twig'
		)
	)
);

foreach ($paths as $index => $path) {
	echo $index;
	if (is_dir($path['path'])) {
		echo ': OK<br/>';
	}
	else {
		echo ': Missing - ';
		if (mkdir($path['path'])) {
			echo 'Created<br/>';
		}
		else {
			echo 'Failed!<br/>';
		}
	}

	if (isset($path['files'])) {
		echo "updating files<br/>";
		foreach ($path['files'] as $file) {
			if (!is_file(__dir__ . '/../application/' . $file)) {
				copy(__dir__ . '/install/' . $file, __dir__ . '/../application/' . $file);
				echo __dir__ . '/install/' . $file . '  ' . __dir__ . '/../application/' . $file . '<br/>';
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