<?php

/**
 * BASE THEME CLASS.
 * 
 * Used to instantiate a new theme with some default properties
 * and methods in place.
 * 
 * @package     NateFlynn
 * @subpackage  Core
 * 
 * @since       1.0.0
 * @author      Nate Flynn
 */

namespace NateFlynn\Core\Classes;

class Theme {

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
     * for the theme.
     * 
     * @since 1.0.0
     * @author Nate Flynn
     * 
     * @param object $config    The theme configuration object parsed from
     *                          the config.json file.
     * 
     * @return void
     */
    public function __construct( object $config ) {
        $this->setup( $config );
        $this->register_hooks();
        $this->register_theme_support();
        $this->register_nav_menus();
        $this->add_editor_styles();
        $this->loadLibraries();
    }

    /**
     * SETUP THE THEME
     * 
     * Sets up the theme basePath, directories and libraries.
     * 
     * @since 1.0.0
     * @author Nate Flynn
     */
    public function setup( object $config ) : void {
        $this->config = $config;
        $this->basePath = trailingslashit( get_template_directory() );
        $this->baseURI = trailingslashit( get_template_directory_uri() );

        $this->data = get_file_data( $this->basePath . 'style.css', array( 
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
     * THEME HOOKS
     * 
     * Registers hooked functions with WordPress for this theme.
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
        add_action( 'enqueue_block_editor_assets',  array( $this, 'editor_styles' ) );
        add_action( 'enqueue_block_editor_assets',  array( $this, 'frontend_scripts' ) );

        add_action( 'init', array( $this, 'register_block_patterns' ) );
        add_action( 'init', array( $this, 'disable_block_patterns' ) );
        add_action( 'init', array( $this, 'register_block_styles' ) );
        add_action( 'init', array( $this, 'register_shortcodes' ) );
    }

    /**
     * REGISTER THEME SUPPORT
     * 
     * Registers custom theme support for this theme.
     * 
     * @since 1.0.0
     * @author Nate Flynn
     */
    public function register_theme_support() {
        $supports = $this->config->supports;

        foreach( $supports as $feature ) {
            if( is_array( $feature ) ) add_theme_support( $feature['name'], $feature['defaults'] );
            else add_theme_support( $feature );
        }
    }

    /**
     * REGISTER NAV MENUS
     * 
     * Registers custom navigation menu locations for this theme.
     * 
     * @since 1.0.0
     * @author Nate Flynn
     */
    public function register_nav_menus() {
        $menus = $this->config->menus;

        if( ! empty( $menus ) )
            register_nav_menus( $this->config->menus );
    }

    /**
     * REGISTER BLOCK PATTERNS
     * 
     * Registers custom block patterns for this theme.
     * 
     * @since 1.0.0
     * @author Nate Flynn
     */
    public function register_block_patterns() {
        $pattern_prefix = isset( $this->config->patternPrefix ) 
            ? $this->config->patternPrefix
            : basename( $this->basePath );

        $patterns = isset( $this->config->patterns ) 
            ? $this->config->patterns : 
            [];

        foreach( $patterns as $slug => $options ) :
            $pattern_name = "$pattern_prefix/$slug";
            $pattern_file = $this->basePath . "patterns/$slug.html";

            if( file_exists( $pattern_file ) ) {
                $options['content'] = isset( $options['content'] ) 
                    ? $options['content'] 
                    : file_get_contents( $pattern_file );

                register_block_pattern( $pattern_name, $options );
            }

        endforeach;
    }

    /**
     * DISABLE BLOCK PATTERNS
     * 
     * Disables all or a subset of block patterns when this theme is active.
     * 
     * @since 1.0.0
     * @author Nate Flynn
     */
    public function disable_block_patterns() {
        $disable_patterns = isset( $this->config->disablePatterns )
            ? $this->config->disablePatterns
            : false;

        if( is_array( $disable_patterns ) ) {
            foreach( $disable_patterns as $pattern_name )
                unregister_block_pattern( $pattern_name );

            return;
        }

        if( $disable_patterns ) 
            remove_theme_support( 'core-block-patterns' );
    }

    /**
     * REGISTER BLOCK STYLES
     * 
     * Registers custom block styles for this theme.
     * 
     * @since 1.0.0
     * @author Nate Flynn
     */
    public function register_block_styles() {
        $custom_styles = isset( $this->config->blockStyles )
            ? $this->config->blockStyles
            : [];

        foreach( $custom_styles as $blockName => $style ) {
            register_block_style( $blockName, $style );
        }
    }

    /**
     * REGISTER SHORTCODES
     * 
     * Registers custom shortcodes for this theme.
     * 
     * @since 1.0.0
     * @author Nate Flynn
     */
    public function register_shortcodes() {
        $dir = isset( $this->directories['shortcodes'] )
            ? trailingslashit( $this->basePath . $this->directories['shortcodes'] )
            : false;

        if( $dir ) {
            $pattern = "$dir/*.php";
            foreach( glob( $pattern ) as $shortcode ) require_once( $shortcode );
        }
    }

    /**
     * REGISTER CUSTOM EDITOR STYLES
     * 
     * Registers custom wp-editor styles for this theme.
     * 
     * @since 1.0.0
     * @author Nate Flynn
     */
    public function add_editor_styles() {
        $styles = isset( $this->config->styles['editor'] ) 
            ? $this->config->styles['editor']
            : [];

        if( ! empty( $styles ) ) {
            add_theme_support( 'editor-styles' );

            foreach( $this->config->styles['editor'] as $style ) {
                $style = (object) $style;
                $style_path = trailingslashit( $this->directories['styles'] ) . "$style->slug.css";
                add_editor_style( $style_path );
            }
        }
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
        foreach( $this->getScripts( 'frontend' ) as $script ) $this->enqueue_script( $script );
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
        foreach( $this->getStyles( 'frontend' ) as $style ) $this->enqueue_style( $style );
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
        foreach( $this->getScripts( 'admin' ) as $script ) $this->enqueue_script( $script );
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
        foreach( $this->getStyles( 'admin' ) as $style ) $this->enqueue_style( $style );
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
        foreach( $this->getScripts( 'editor' ) as $script ) $this->enqueue_script( $script );
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
        foreach( $this->getStyles( 'editor' ) as $style ) $this->enqueue_style( $style );
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
        foreach( $this->getScripts( 'block' ) as $script ) $this->enqueue_script( $script );
        foreach( $this->getStyles( 'block' ) as $style ) $this->enqueue_style( $style );
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

    private function getScripts( $type ) {
        return isset( $this->config->scripts[ $type ] )
            ? $this->config->scripts[ $type ]
            : [];
    }

    private function getStyles( $type ) {
        return isset( $this->config->styles[ $type ] )
            ? $this->config->styles[ $type ]
            : [];
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