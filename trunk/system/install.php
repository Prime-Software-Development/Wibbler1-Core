<?php

$paths = array(
	'Application' => array(
		'path' => __dir__ . '/../application/'
	),
	'Config' => array(
		'path' => __dir__ . '/../application/config/'
	),
	'Controllers' => array(
		'path' => __dir__ . '/../application/controllers/'
	),
	'Helpers' => array(
		'path' => __dir__ . '/../application/helpers/'
	),
	'Modules' => array(
		'path' => __dir__ . '/../application/modules/'
	),
	'Templates' => array(
		'path' => __dir__ . '/../application/templates/'
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
}
