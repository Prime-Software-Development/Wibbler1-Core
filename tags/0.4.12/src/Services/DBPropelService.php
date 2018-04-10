<?php
namespace Trunk\Wibbler\Services;
if ( defined( "PROPEL_INC" ) ) {
	\Propel::init( COMMONPATH . 'propel/build/conf/' . PROPEL_INC );
	// Add the generated 'classes' directory to the include path
	set_include_path( COMMONPATH . "propel/build/classes" . PATH_SEPARATOR . get_include_path());
} else {
	require_once COMMONPATH . '/propel/generated-conf/config.php';
}

class DBPropelService {

}