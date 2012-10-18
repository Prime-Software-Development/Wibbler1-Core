<?php
namespace Wibbler\Modules;
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
require_once 'Twig/Autoloader.php';

class twig {

	private $_twig;
	private $_template_dir;
	private $_cache_dir;

	function __construct(\Wibbler\WibblerDependencyContainer $dependencies)
	{
		$this->_config = $dependencies->getConfig('twig');

		$this->_template_dir = $this->_config['template_dir'];
		$this->_cache_dir = $this->_config['cache_dir'];

		\Twig_Autoloader::register();

		$loader = new \Twig_Loader_Filesystem($this->_template_dir);
		$this->_twig = new \Twig_Environment($loader, array(
			'cache' => $this->_cache_dir,
			'debug' => true,
		));
		
	}

	public function render($template, $data = array()) {

		$template = $this->_twig->loadTemplate($template);

		return $template->render($data);
	}
	
	public function display($template, $data = array()) {

		$template = $this->_twig->loadTemplate($template);

		$template->display($data);
	}
}