<?php
/**
 *
 * SCSS compiler.
 *
 * @since   2.0.0
 * @package Custom_Codes
 */

defined( 'ABSPATH' ) || die( 'No script kiddies please!' );

/**
 *
 * Main compiler function.
 *
 * @param string $compileable_content Directory path to delete.
 */
function codes_compile_scss( $compileable_content ) {

	$compilation = array(
		'status'   => 'initiated',
		'compiled' => null,
	);

	// Call Composer.
	require_once __DIR__ . '/vendor/autoload.php';

	try {

		// Compiler.
		$scss = new ScssPhp\ScssPhp\Compiler();
		$scss->setImportPaths( CODES_FOLDER_DIR );
		$scss->setOutputStyle( \ScssPhp\ScssPhp\OutputStyle::EXPANDED );

		$scss->setSourceMap( ScssPhp\ScssPhp\Compiler::SOURCE_MAP_INLINE );
		$scss->setSourceMapOptions(
			array(
				'sourceMapBasepath' => ABSPATH, // Partial path (server root) removed (normalized) to create a relative url.
				'sourceRoot'        => '/',     // Prepended to 'source' field entries for relocating source files.
			)
		);

		$compiled    = $scss->compile( $compileable_content );
		$compilation = array(
			'status'   => 'success',
			'compiled' => $compiled,
		);

	} catch ( \Exception $e ) {

		$compilation = array(
			'status'   => 'error',
			'compiled' => null,
			'message'  => $e->getMessage(),
		);

	}

	return $compilation;

}
