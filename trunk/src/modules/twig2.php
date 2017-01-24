<?php
namespace Trunk\Wibbler\Modules;

use Twig_Loader_Filesystem;
use Twig_Environment;
use Twig_Extension;
use Twig_SimpleFilter;
use Twig_Filter;

class twig extends base {

	private $_twig;
	private $_template_dir;
	private $_cache_dir;
	private $loaded_filters = [];

	function __construct( )
	{
		global $twig_options;
		$this->_config = $twig_options;

		$this->_template_dir = $this->_config['template_dir'];
		$this->_cache_dir = $this->_config['cache_dir'];

		#\Twig_Autoloader::register();

		$loader = new Twig_Loader_Filesystem($this->_template_dir);
		$this->_twig = new Twig_Environment($loader, array(
			'cache' => $this->_cache_dir,
			'debug' => true,
		));

		if( 'development' === ENVIRONMENT ) {
			$this->_twig->addExtension(new \Twig_Extension_Debug());

			$this->_twig->addExtension( new TwigExtensions() );
		}
	}

	public function addExtension( $extension ) {
		$this->_twig->addExtension( $extension );
	}

	public function render($template, $data = array()) {

		$template = $this->_twig->loadTemplate($template);

		return $template->render($data);
	}
	
	public function display($template, $data = array()) {

		$template = $this->_twig->loadTemplate($template);

		$template->display($data);
	}
	/**
	 * Adds a new filter function to the twig environment
	 * @param type $name Name of the filter
	 * @param type $filter Function to call
	 */
	public function add_filter($name, $filter) {
		// If the filter is already loaded
		if ( in_array( $name, $this->loaded_filters ) ) {
			// Return
			return;
		}
		// Note the filter has been loaded
		$this->loaded_filters[] = $name;
		// Load the filter
		$this->_twig->addFilter(new Twig_Filter($name, $filter));
	}

	public function set_number_format($decimal_places = 0, $decimal_point_char = ".", $thousand_seperator = ",") {
		$this->_twig->getExtension('Twig_Extension_Core')->setNumberFormat($decimal_places, $decimal_point_char, $thousand_seperator);
	}

    public function add_global( $name, $value ) {
        $this->_twig->addGlobal( $name, $value );
    }
}

class TwigExtensions extends Twig_Extension {
	public function getFilters() {
		return array(
			new Twig_SimpleFilter('file_exists', function($file){
				return file_exists( $file );
			})
		);
	}

	public function getName() {
		return 'trunk_software_extension';
	}
}