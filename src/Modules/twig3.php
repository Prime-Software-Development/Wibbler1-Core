<?php
namespace Trunk\Wibbler\Modules;

use Twig\Environment;
use Twig\Extension\AbstractExtension;
use Twig\Extension\CoreExtension;
use Twig\Extension\DebugExtension;
use Twig\Loader\FilesystemLoader;
use Twig\TwigFilter;
use Twig_Loader_Filesystem;
use Twig_Extension_Debug;
use Twig_Environment;
use Twig_SimpleFilter;
use Twig_Filter;
use Twig_FactoryRuntimeLoader;
use Symfony\Bridge\Twig\Form\TwigRendererEngine;
use Symfony\Component\Form\Forms;
use Symfony\Bridge\Twig\Extension\FormExtension;
use Symfony\Bridge\Twig\Form\TwigRenderer;

class twig3 extends base
{

	private $_twig;
	private $_template_dir;
	private $_cache_dir;

	/**
	 * @var \Twig_Loader_Filesystem
	 */
	private $twig_loader;

	/**
	 * Holds a list of the filters already loaded
	 * @var array
	 */
	private $loaded_filters = [ ];
	/**
	 * Holds a list of the extensions already loaded
	 * @var array
	 */
	private $loaded_extensions = [ ];

	/**
	 * @var array|string[]
	 */
	private $_config = [];

	function __construct( array $options = null )
	{
		parent::__construct( $options );

		if ( $options === null ) {
			global $twig_options;
			$this->_config = $twig_options;
		} else {
			$this->_config = $options;
		}

		$this->_template_dir = $this->_config[ 'template_dir' ];
		$this->_cache_dir = $this->_config[ 'cache_dir' ];

		// Create a new loader with the template directories
		$this->twig_loader = new FilesystemLoader( $this->_template_dir );

		// Start the twig environment
		$this->_twig = new Environment( $this->twig_loader, array(
			'cache' => $this->_cache_dir,
			'debug' => true,
		) );

		if ( 'development' === ENVIRONMENT ) {
			$this->_twig->addExtension( new DebugExtension() );

			$this->_twig->addExtension( new Twig2Extensions() );
		}
	}

	public function render( $template, $data = array() )
	{

		$template = $this->_twig->load( $template );

		return $template->render( $data );
	}

	public function display( $template, $data = array() )
	{

		$template = $this->_twig->load( $template );

		$template->display( $data );
	}

	/**
	 * @param string $template_string
	 *
	 * @return \Twig_Template
	 * @throws \Exception
	 * @throws \Throwable
	 */
	public function createTemplate($template_string)
	{
		return $this->_twig->createTemplate($template_string);
	}

	/**
	 * Adds a runtime loader to the twig environment - allowing uses of themes
	 * @param $form_theme string
	 */
	public function add_runtime_loader( $form_theme )
	{
		$twig = $this->_twig;

		$formEngine = new TwigRendererEngine( array( $form_theme ), $twig );
		$twig->addRuntimeLoader( new \Twig_FactoryRuntimeLoader( array(
			TwigRenderer::class => function () use ( $formEngine ) {
				return new TwigRenderer( $formEngine );
			},
		) ) );
	}

	/**
	 * Adds a new extension to the twig environment
	 * @param $extension
	 */
	public function addExtension( $extension )
	{
		// Get the class of the extension
		$extension_class = get_class( $extension );

		// If the class is already loaded
		if ( in_array( $extension_class, $this->loaded_extensions ) ) {
			// Return
			return;
		}
		// Note the extension has been loaded
		$this->loaded_extensions[] = $extension_class;
		// Add the extension
		$this->_twig->addExtension( $extension );
	}

	/**
	 * Adds a new filter function to the twig environment
	 * @param type $name Name of the filter
	 * @param type $filter Function to call
	 */
	public function add_filter( $name, $filter )
	{
		// If the filter is already loaded
		if ( in_array( $name, $this->loaded_filters ) ) {
			// Return
			return;
		}
		// Note the filter has been loaded
		$this->loaded_filters[] = $name;
		// Load the filter
		$this->_twig->addFilter( new TwigFilter( $name, $filter ) );
	}

	/**
	 * Add the path to the list of template paths
	 * @param $path
	 */
	public function add_template_path( $path )
	{
		$this->twig_loader->addPath( $path );
	}

	/**
	 * Sets the default number format for the number_filter extension
	 * @param int $decimal_places
	 * @param string $decimal_point_char
	 * @param string $thousand_seperator
	 */
	public function set_number_format( $decimal_places = 0, $decimal_point_char = ".", $thousand_seperator = "," )
	{
		$this->_twig->getExtension(  CoreExtension::class )->setNumberFormat( $decimal_places, $decimal_point_char, $thousand_seperator );
	}

	/**
	 * Set the default date format
	 * @param $date_format
	 */
	public function set_date_format( $date_format )
	{
		$this->_twig->getExtension( CoreExtension::class )->setDateFormat( $date_format );
	}

	/**
	 * Add a global variable
	 * @param $name
	 * @param $value
	 */
	public function add_global( $name, $value )
	{
		$this->_twig->addGlobal( $name, $value );
	}
}

class Twig2Extensions extends AbstractExtension {
	public function getFilters() {
		return array(
			new TwigFilter('file_exists', function($file){
				return file_exists( $file );
			})
		);
	}

	public function getName() {
		return 'trunk_software_extension';
	}
}