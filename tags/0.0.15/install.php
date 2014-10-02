<?php
namespace Trunk\Wibbler;

class Install {

	/**
	 * Copy a file, or recursively copy a folder and its contents
	 * @param       string   $source    Source path
	 * @param       string   $dest      Destination path
	 * @param       string   $permissions New folder creation permissions
	 * @return      bool     Returns true on success, false on failure
	 */
	private function xcopy($source, $dest, $permissions = 0755)
	{
		// Check for symlinks
		if (is_link($source)) {
			return symlink(readlink($source), $dest);
		}

		// Simple copy for a file
		if (is_file($source)) {
			return copy($source, $dest);
		}

		// Make destination directory
		if (!is_dir($dest)) {
			mkdir($dest, $permissions);
		}

		// Loop through the folder
		$dir = dir($source);
		while (false !== $entry = $dir->read()) {
			// Skip pointers
			if ($entry == '.' || $entry == '..') {
				continue;
			}

			// Deep copy directories
			$this->xcopy("$source/$entry", "$dest/$entry");
		}

		// Clean up
		$dir->close();
		return true;
	}

	function go( $directory ) {

		
		$from_dir = __dir__ . '/install/common';
		$to_dir = $directory . '/common';
		echo "Copying from " . $from_dir . " to " . $to_dir . "\n";
		$this->xcopy( $from_dir, $to_dir );

		$from_dir = __dir__ . '/install/web1';
		$to_dir = $directory . '/web1';
		echo "Copying from " . $from_dir . " to " . $to_dir . "\n";
		$this->xcopy( $from_dir, $to_dir );
	}

}