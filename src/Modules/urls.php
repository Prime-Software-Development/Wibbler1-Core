<?php
namespace Trunk\Wibbler\Modules;

class urls extends base
{

	/**
	 * @var string Request type (http or https)
	 */
	var $http = '';
	/**
	 * @var string URL used to access the root of the system
	 */
	var $root_url = '';
	/**
	 * @var string URL to the controller
	 */
	var $controller_path = '';
	/**
	 * @var string Name of the server
	 */
	var $server_name = '';
	/**
	 * @var bool Whether we are being access from the command line
	 */
	var $is_cli = false;

	/**
	 * @var \Trunk\Wibbler\Services\CoreRouter
	 */
	private $wibbler_loader = null;

	// Constructor of the core urls module
	function __construct( $additional_config = null )
	{

		// If PHP_SAPI == 'cli' we have been called from the command line
		if ( PHP_SAPI == 'cli' ) {
			$this->is_cli = true;
		} else {
			$this->http = isset( $_SERVER[ 'REQUEST_SCHEME' ] ) ? $_SERVER[ 'REQUEST_SCHEME' ] : ( isset( $_SERVER[ 'HTTPS' ] ) ? 'https' : 'http' );
			$this->server_name = $_SERVER[ 'HTTP_HOST' ];
			$this->root_url = $this->_get_current_root_url();
		}

		parent::__construct( $additional_config );
		$this->wibbler_loader = $this->load_service( 'wibbler.loader' );
	}

	/**
	 * Gets the current url for the root of the system
	 * @return string
	 */
	private function _get_current_root_url()
	{
		$requested = $_SERVER[ 'REQUEST_URI' ];
		$script = $_SERVER[ 'SCRIPT_NAME' ];
		$result = '';

		// If index.php is in the request - then we aren't using htaccess rewrites
		// Therefore find the path quickly from the request
		if ( strpos( $requested ?? "", "index.php" ) > 0 ) {
			$result = substr( $requested, 0, strpos( $requested, "index.php" ) );
			$this->controller_path = substr( $requested, 0, strpos( $requested, "index.php" ) + 9 ) . '/';
			return $result;
		}

		// No index.php found in request - therefore we are using htaccess rewrites
		// Therefore find the path by comparing request and current script until they no longer match

		// Find the maximum number of characters to compare
		$requested_len = strlen( $requested );
		$max_chars = strlen( $script ) < $requested_len ? strlen( $script ) : $requested_len;

		// Iterate over both strings
		for ( $i = 0; $i < $max_chars; $i++ ) {
			if ( $requested[ $i ] == $script[ $i ] ) {
				$result .= $requested[ $i ];
			} else {
				break;
			}
		}

		// If there isn't a / on the end
		if ( strrpos( $result, "/" ) != strlen( $result ) ) {
			$result = substr( $result, 0, strrpos( $result, "/" ) + 1 );
		}

		$this->controller_path = $result;
		return $result;
	}

	public function get_full_url()
	{
		return $this->http . '://' . $this->server_name . $this->root_url;
	}

	public function get_requested_url()
	{
		$requested_uri = $_SERVER[ 'REQUEST_URI' ] ?? "";
		if ( strpos( $requested_uri ?? "", '/' ) === 0 ) {
			$requested_uri = substr( $requested_uri ?? "", 1 );
		}
		return $requested_uri;
	}

	public function redirect( $url = "" )
	{
		header( 'Location: ' . $this->get_full_url() . $url );
	}

	public function get_controller_path()
	{
		return $this->wibbler_loader->controller_path;
	}
}
