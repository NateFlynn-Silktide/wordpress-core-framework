<?php

/**
 * 
 */

namespace NateFlynn\Core;

// EXIT IF THIS FILE IS CALLED DIRECTLY
if( ! defined( 'ABSPATH' ) ) wp_die(
    __( 'You do not have permission to access this file.' ),
    __( 'Permission Denied' )
);


// LOAD CONFIG
$config = (object) json_decode( file_get_contents( trailingslashit( dirname( __FILE__ ) ) . 'config.json' ), true );


// LOAD REQUIRED FILES
require_once( 'Autoload.php' );


/**
 * CORE PLUGIN
 * 
 * Class definition for the core plugin. This will be loaded before any other custom plugins
 * along with any defined libraries.
 * 
 * Everything should be properly namespaced within the core plugin, so using it's libraries and
 * functions in subsequent themes and plugins is as simple as adding a `use` statement for the
 * relevant function or class or, alternatively, namespacing your own theme / plugin within
 * the `NateFlynn` namespace.
 * 
 * @since   1.0.0
 * @author  Nate Flynn
 * 
 * @uses    NateFlynn\Core\Classes\Plugin
 */

namespace NateFlynn\Core;

use \NateFlynn\Core\Classes\Plugin;
use \NateFlynn\Core\Classes\EntityLoader;

use \NateFlynn\Core\Traits\SingletonTrait;

final class Core extends Plugin {

    // Only allow a single instance of this class to be active at any time.
    use SingletonTrait;

    // Class constructor
    public function __construct( object $config, string $basePath ) {
        parent::__construct( $config, $basePath );
        $this->load_entities();
    }

    private function load_entities() {
        new EntityLoader( 'PostType',          'after_setup_theme' );
        new EntityLoader( 'PostTypeCategory',  'after_setup_theme' );
        new EntityLoader( 'SettingsPage',      'after_setup_theme' );
        new EntityLoader( 'Taxonomy',          'init' );
        new EntityLoader( 'ACFBlock',          'after_setup_theme' );
    }

}

// LOAD THE PLUGIN INSTANCE
Core::Instance( $config, dirname( __FILE__ ) );