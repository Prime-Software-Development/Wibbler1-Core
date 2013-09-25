<?php

$paths = array(
	'Resources' => array(
		'path' => __dir__ . '/../resources/'
	),
	'3rd Party' => array(
		'path' => __dir__ . '/../resources/3rdparty/'
	),
	'Javascript' => array(
		'path' => __dir__ . '/../resources/js/'
	),
	'Css' => array(
		'path' => __dir__ . '/../resources/css/'
	),

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
			'/controllers/BaseController.php',
			'/controllers/welcome.php',
			'/controllers/BaseSearchController.php',
			'/controllers/BaseSearchDataController.php'
		)
	),
	'SampleControllers' => array(
		'path' => __dir__ . '/../application/controllers/user/',
		'files' => array(
			'/controllers/user/data.php',
			'/controllers/user/welcome.php'
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
			'/templates/base.twig',
			'/templates/footer.twig',
			'/templates/navigation.twig',
			'/templates/secured.twig',
			'/templates/welcome.twig',
		)
	),
	'Search Templates' => array(
		'path' => __dir__ . '/../application/templates/_search',
		'files' => array(
			'/templates/_search/base_search.twig',
			'/templates/_search/base_search_body.twig',
			'/templates/_search/base_search_empty.twig',
			'/templates/_search/base_search_results.twig',
			'/templates/_search/manage.twig',
			'/templates/_search/search_bar.twig'
		)
	),
	'Sample Templates' => array(
		'path' => __dir__ . '/../application/templates/user',
		'files' => array(
			'/templates/user/index.twig',
			'/templates/user/search_results.twig'
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