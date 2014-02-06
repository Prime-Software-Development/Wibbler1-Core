<?php
namespace Trunk\Wibbler;

class Install {

	function go( $directory ) {
		foreach ($this->paths as $index => $path) {
			echo $index;

			$dest_dir = $directory . $path['path'];

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
					$to_file_name = $directory . $path['path'] . $file;
					$from_file_name = __dir__ . '/install' . $path['path'] . $file;

					if (!is_file($to_file_name)) {
						copy($from_file_name, $to_file_name);
						echo 'Copied from: ' . $from_file_name . '  to:' . $to_file_name . '<br/>';
					}
				}
			}
		}
	}


	var $paths = array(
		'Common' => array(
			'path' => '/common/'
		),
		'Propel' => array(
			'path' => '/common/propel/'
		),
		'Generated Classes' => array(
			'path' => '/common/propel/generated-classes/'
		),
		'Helpers' => array(
			'path' => '/common/helpers/'
		),
		'Modules' => array(
			'path' => '/common/modules/'
		),
		'Templates' => array(
			'path' => '/common/templates/'
		),
		'Template Web1' => array(
			'path' => '/common/templates/web1/',
			'files' => array(
				'base.twig',
				'footer.twig',
				'navigation.twig',
				'secured.twig',
				'welcome.twig',
			)
		),
		'Search Templates' => array(
			'path' => '/common/templates/web1/_search/',
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
			'path' => '/common/templates/web1/user/',
			'files' => array(
				'index.twig',
				'search_results.twig'
			)
		),

		'Controllers' => array(
			'path' => '/common/web1/',
			'files' => array(
				'BaseController.php',
				'welcome.php',
				'BaseSearchController.php',
				'BaseSearchDataController.php'
			)
		),
		'SampleControllers' => array(
			'path' => '/common/web1/user/',
			'files' => array(
				'data.php',
				'welcome.php'
			)
		),

		'Web1' => array(
			'path' => '/web1/',
			'files' => array(
				'index.php',
				'.htaccess'
			)
		),
		'3rd Party' => array(
			'path' => '/web1/3rdparty/'
		),
		'Javascript' => array(
			'path' => '/web1/js/',
			'files' => array(
				'manage.js',
				'script.js',
				'search.js'
			)
		),
		'Css' => array(
			'path' => '/web1/css/',
			'files' => array(
				'style.css'
			)
		)
	);
}