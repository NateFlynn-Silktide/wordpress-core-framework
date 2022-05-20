<?php

/**
 * PLUGIN OBJECT AUTOLOADER
 * 
 * Automatically loads classes, entities, interfaces and traits registered within this
 * plugin's namespace.
 * 
 * @package NateFlynn
 * @subpackage Core
 * 
 * @since 1.0.0
 * @author NateFlynn
 */

namespace NateFlynn\Core;

class Autoload {

    /**
     * REGISTER SPL AUTOLOADERS
     * 
     * Registers the required SPL Autoloaders for classes, entities, interfaces and
     * traits registered in this namespace.
     * 
     * @since 1.0.0
     * @author Nate Flynn
     */
    public static function register() {
        spl_autoload_register( array( 'self', 'class_autoloader' ) );
        spl_autoload_register( array( 'self', 'entity_autoloader' ) );
        spl_autoload_register( array( 'self', 'interface_autoloader' ) );
        spl_autoload_register( array( 'self', 'trait_autoloader' ) );
    }

    /**
     * GENERATE FILENAME
     * 
     * Generates a filename based off a class name and object type. Types should match
     * the pathspec of the object. For example classes use the `Classes` pathspec within
     * their namespace.
     * 
     * @since 1.0.0
     * @author Nate Flynn
     * 
     * @param string $classname The fully qualified classname of the object
     * @param string $type      The type of object being parsed. One of `Classes`, `Entities`, `Interfaces` or `Traits`
     * 
     * @return string
     */
    private static function generate_filename( $classname, $type = 'Classes' ) {
        return str_replace( 
            array( __NAMESPACE__ . '\\' . $type . '\\', '_' ),
            array( '', '-' ), 
            $classname 
        );
    }

    /**
     * CLASS AUTOLOADER
     * 
     * Automatically loads classes within this namespace.
     * 
     * @since 1.0.0
     * @author Nate Flynn
     * 
     * @param string $classname The fully qualified name of the class being loaded
     * 
     * @return bool
     */
    public static function class_autoloader( $classname ) {
        // Only run this autoloader for classes in this namespace
        if( ! strstr( $classname, __NAMESPACE__ ) ) return false;
        if( ! strstr( $classname, 'Classes' ) ) return false;
        
        $filename = self::generate_filename( $classname, 'Classes' );

        $file = trailingslashit( dirname( __FILE__ ) ) . "src/classes/$filename.php";
        if( file_exists( $file ) ) require( $file );
    }

    /**
     * ENTITY AUTOLOADER
     * 
     * Automatically loads entities within this namespace.
     * 
     * @since 1.0.0
     * @author Nate Flynn
     * 
     * @param string $classname The fully qualified name of the entity being loaded
     * 
     * @return bool
     */
    public static function entity_autoloader( $classname ) {
        // Only run this autoloader for classes in this namespace
        if( ! strstr( $classname, __NAMESPACE__ ) && ! strstr( $classname, 'Entities' ) ) return false;
        
        $filename = self::generate_filename( $classname, 'Entities' );

        $file = trailingslashit( dirname( __FILE__ ) ) . "src/entities/$filename.php";
        if( file_exists( $file ) ) require( $file );
    }

    /**
     * INTERFACE AUTOLOADER
     * 
     * Automatically loads interfaces within this namespace.
     * 
     * @since 1.0.0
     * @author Nate Flynn
     * 
     * @param string $classname The fully qualified name of the interface being loaded
     * 
     * @return bool
     */
    public static function interface_autoloader( $classname ) {
        // Only run this autoloader for classes in this namespace
        if( ! strstr( $classname, __NAMESPACE__ ) && ! strstr( $classname, 'Interfaces' ) ) return false;
        
        $filename = self::generate_filename( $classname, 'Interfaces' );

        $file = trailingslashit( dirname( __FILE__ ) ) . "src/interfaces/$filename.php";
        if( file_exists( $file ) ) require( $file );
    }

    /**
     * TRAIT AUTOLOADER
     * 
     * Automatically loads traits within this namespace.
     * 
     * @since 1.0.0
     * @author Nate Flynn
     * 
     * @param string $classname The fully qualified name of the trait being loaded
     * 
     * @return bool
     */
    public static function trait_autoloader( $classname ) {
        // Only run this autoloader for classes in this namespace
        if( ! strstr( $classname, __NAMESPACE__ ) && ! strstr( $classname, 'Traits' ) ) return false;
        
        $filename = self::generate_filename( $classname, 'Traits' );

        $file = trailingslashit( dirname( __FILE__ ) ) . "src/traits/$filename.php";
        if( file_exists( $file ) ) require( $file );
    }

}

Autoload::register();