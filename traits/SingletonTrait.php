<?php
/**
 * Trait for a singleton pattern.
 * 
 * @package: NateFlynn
 * @subpackage: Core
 * 
 * @since 1.0.0
 * @author Nate Flynn
 */

namespace NateFlynn\Core\Traits;

trait SingletonTrait {
    /**
     * The object instance.
     * @var Object
     */
    private static $instance = null;

    /**
     * Return the plugin instance.
     * 
     * @return Object
     */
    public static function Instance( ...$args ) {
        if( ! self::$instance ) { self::$instance = new self( ...$args ); }
        return self::$instance;
    }

    /**
     * Reset the plugin instance.
     */
    public static function reset() {
        self::$instance = null;
    }

    /**
     * Throw error on object clone.
     * 
     * The whole idea of the singleton design pattern is that there is a single
     * object. Therefore, we don't want the object to be cloned.
     * 
     * @access protected
     * @return void
     */
    public function __clone() {
        // Cloning instances of the class is forbidden.
        _doing_it_wrong( __FUNCTION__, esc_html__( 'Something went wrong', 'nateflynn' ), '1.0' );
    }

    /**
     * Disable unserializing of the class.
     * 
     * @access protected
     * @return void
     */
    public function __wakeup() {
        // Unserializing instances of the class is forbidden.
        _doing_it_wrong( __FUNCTION__, esc_html__( 'Something went wrong', 'nateflynn' ), '1.0' );
    }
}