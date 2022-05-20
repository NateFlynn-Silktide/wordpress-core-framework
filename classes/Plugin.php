<?php

/**
 * BASE PLUGIN CLASS.
 * 
 * Used to instantiate a new plugin with some default properties
 * and methods in place.
 * 
 * @package     NateFlynn
 * @subpackage  Core
 * 
 * @since       1.0.0
 * @author      Nate Flynn
 */

namespace NateFlynn\Core\Classes;

use NateFlynn\Core\Traits\SingletonTrait;

class Plugin {


    // CLASS PROPERTIES
    
    /**
     * The full path to the root directory of the plugin.
     * @var string
     */
    public $basePath = "";

    /**
     * The full URL to the root directory of the plugin.
     * @var string
     */
    public $baseURI = "";

    /**
     * The full path to the scripts directory of the plugin.
     * @var string
     */
    public $scriptsPath = "";

    /**
     * The full URL to the scripts directory of the plugin.
     * @var string
     */
    public $scriptsURI = "";

    /**
     * The full path to the styles directory of the plugin.
     * @var string
     */
    public $stylesPath = "";

    /**
     * The full URL to the styles directory of the plugin.
     * @var string
     */
    public $stylesURI = "";

    /**
     * The plugin's data, taken from the `index.php` file header.
     * @var string
     */
    public $data = [];

    /**
     * A list of directories associated with the plugin.
     * @var array
     */
    public $directories = [];

    /**
     * A list of libraries in the order they should be loaded.
     * @var array
     */
    public $libraries = [];


    // CLASS METHODS

    /**
     * CONSTRUCTOR
     * 
     * Sets up the class properties and loads the required libraries
     * for the plugin.
     * 
     * @since 1.0.0
     * @author Nate Flynn
     * 
     * @param object $config    The plugin configuration object parsed from
     *                          the config.json file.
     * 
     * @return void
     */
    public function __construct( object $config, string $basePath ) {
        $this->setup( $basePath, $config );
        $this->register_hooks();
        $this->loadLibraries();
    }

    /**
     * SETUP THE PLUGIN
     * 
     * Sets up the plugin basePath, directories and libraries.
     * 
     * @since 1.0.0
     * @author Nate Flynn
     */
    public function setup( string $basePath, object $config ) : void {
        $this->config = $config;
        $this->basePath = trailingslashit( $basePath );
        $this->baseURI = trailingslashit( plugins_url( basename( $this->basePath ), $this->basePath ) );

        $this->data = get_file_data( $this->basePath . 'index.php', array( 
            'version' => 'Version',
            'textdomain' => 'Text Domain'
        ));

        $this->directories = $config->directories;
        $this->libraries = $config->libraries;

        $this->config->scriptsPath = trailingslashit( $this->basePath . $this->directories['scripts'] );
        $this->config->scriptsURI = trailingslashit( $this->baseURI . $this->directories['scripts'] );

        $this->config->stylesPath = trailingslashit( $this->basePath . $this->directories['styles'] );
        $this->config->stylesURI = trailingslashit( $this->baseURI . $this->directories['styles'] );
    }

    /**
     * PLUGIN HOOKS
     * 
     * Registers hooked functions with WordPress for this plugin.
     * 
     * @since 1.0.0
     * @author Nate Flynn
     */
    public function register_hooks() {
        add_action( 'wp_enqueue_scripts', array( $this, 'frontend_scripts' ) );
        add_action( 'wp_enqueue_scripts', array( $this, 'frontend_styles' ) );

        add_action( 'admin_enqueue_scripts', array( $this, 'admin_scripts' ) );
        add_action( 'admin_enqueue_scripts', array( $this, 'admin_styles' ) );

        add_action( 'enqueue_block_assets',         array( $this, 'block_assets' ) );
        add_action( 'enqueue_block_editor_assets',  array( $this, 'editor_scripts' ) );
        add_action( 'enqueue_block_editor_assets',  array( $this, 'frontend_scripts' ) );
    }

    /**
     * ENQUEUE SCRIPT
     * 
     * Enqueues a script using an asset file with managed dependencies.
     * 
     * @since 1.0.0
     * @author Nate Flynn
     * 
     * @access private
     * 
     * @param array|object $script The script object to load
     */
    private function enqueue_script( $script ) : void {
        $script = (object) $script;

        $filepath = $this->config->scriptsPath . $script->slug;
        $fileurl = $this->config->scriptsURI . $script->slug . ".js";
        $asset_file = $this->getAssetFile( $filepath );

        $deps = isset( $script->dependencies ) 
            ? $script->dependencies
            : [];

        wp_enqueue_script(
            $script->name,
            $fileurl,
            array_merge( $asset_file['dependencies'], $deps, array( 'wp-api' ) ),
            $asset_file['version'],
            true
        );
    }

    /**
     * ENQUEUE STYLE
     * 
     * Enqueues a stylesheet using an asset file with managed dependencies.
     * 
     * @since 1.0.0
     * @author Nate Flynn
     * 
     * @access private
     * 
     * @param array|object $style The style object to load
     */
    private function enqueue_style( $style ) : void {
        $style = (object) $style;

        $filepath = $this->config->stylesPath . $style->slug;
        $fileurl = $this->config->stylesURI . $style->slug . ".css";
        $asset_file = $this->getAssetFile( $filepath );

        $deps = isset( $style->dependencies ) 
            ? $style->dependencies
            : [];

        wp_enqueue_style(
            $style->name,
            $fileurl,
            array_merge( $asset_file['dependencies'], $deps, array( 'wp-components' ) ),
            false
        );
    }

    /**
     * FRONTEND SCRIPTS
     * 
     * Loads any frontend scripts registered in the plugin's `config.json`
     * 
     * @since 1.0.0
     * @author Nate Flynn
     */
    public function frontend_scripts() : void {
        $scripts = isset( $this->config->scripts['frontend'] )
            ? $this->config->scripts['frontend']
            : [];

        foreach( $scripts as $script ) $this->enqueue_script( $script );
    }

    /**
     * FRONTEND STYLES
     * 
     * Loads any frontend stylesheets registered in the plugin's `config.json`
     * 
     * @since 1.0.0
     * @author Nate Flynn
     */
    public function frontend_styles() : void {
        $styles = isset( $this->config->styles['frontend'] )
            ? $this->config->styles['frontend']
            : [];

        foreach( $styles as $style ) $this->enqueue_style( $style );
    }

    /**
     * ADMIN SCRIPTS
     * 
     * Loads any admin scripts registered in the plugin's `config.json`
     * 
     * @since 1.0.0
     * @author Nate Flynn
     */
    public function admin_scripts() : void {
        $scripts = isset( $this->config->scripts['admin'] )
            ? $this->config->scripts['admin']
            : [];

        foreach( $scripts as $script ) $this->enqueue_script( $script );
    }

    /**
     * ADMIN STYLES
     * 
     * Loads any admin stylesheets registered in the plugin's `config.json`
     * 
     * @since 1.0.0
     * @author Nate Flynn
     */
    public function admin_styles() : void {
        $styles = isset( $this->config->styles['admin'] )
            ? $this->config->styles['admin']
            : [];

        foreach( $styles as $style ) $this->enqueue_style( $style );
    }

    /**
     * EDITOR SCRIPTS
     * 
     * Loads any block editor scripts registered in the plugin's `config.json`
     * 
     * @since 1.0.0
     * @author Nate Flynn
     */
    public function editor_scripts() : void {
        $scripts = isset( $this->config->scripts['editor'] )
            ? $this->config->scripts['editor']
            : [];

        foreach( $scripts as $script ) $this->enqueue_script( $script );
    }

    /**
     * EDITOR STYLES
     * 
     * Loads any block editor stylesheets registered in the plugin's `config.json`
     * 
     * @since 1.0.0
     * @author Nate Flynn
     */
    public function editor_styles() : void {
        $styles = isset( $this->config->styles['editor'] )
            ? $this->config->styles['editor']
            : [];

        foreach( $styles as $style ) $this->enqueue_style( $style );
    }

    /**
     * BLOCK ASSETS
     * 
     * Loads any block stylesheets & scripts registered in the plugin's `config.json`
     * 
     * @since 1.0.0
     * @author Nate Flynn
     */
    public function block_assets() : void {
        $scripts = isset( $this->config->scripts['block'] )
            ? $this->config->scripts['block']
            : [];

        foreach( $scripts as $script ) $this->enqueue_script( $script );

        $styles = isset( $this->config->styles['block'] )
            ? $this->config->styles['block']
            : [];

        foreach( $styles as $style ) $this->enqueue_style( $style );
    }

    /**
     * LOAD PLUGIN LIBRARIES
     * 
     * Loads the plugin libraries as defined in the plugin's `config.json`
     * 
     * @since 1.0.0
     * @author Nate Flynn
     */
    public function loadLibraries() : void {
        foreach( $this->libraries as $library ) {
            $filePath = trailingslashit( $this->basePath . $this->directories['libraries'] ) . "$library.php";
            if( file_exists( $filePath ) ) require_once( $filePath ); 
        }
    }

    /**
     * GET ASSET FILE
     * 
     * Returns asset data for a script or stylesheet.
     * 
     * @since 1.0.0
     * @author Nate Flynn
     */
    public function getAssetFile( string $filepath, string $dir = "" ) : array {
        $asset_path = $dir . $filepath . '.asset.php';

        return file_exists( $asset_path )
            ? include $asset_path
            : array(
                'dependencies'  => array(),
                'version' => $this->data['version']
            );
    }

}