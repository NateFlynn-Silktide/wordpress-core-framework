<?php

/**
 * UTILITY FUNCTIONS
 * 
 * Useful utility functions for processing data.
 */

namespace NateFlynn\Core;

/**
 * GET CONFIG
 * 
 * Utility function to return a JSON config file as a
 * configuration object for use within a class declaration.
 * 
 * @since 1.0.0
 * @author Nate Flynn
 */
function get_config( string $path ) : object {
    $filePath = trailingslashit( $path ) . 'config.json';

    $config = file_exists( $filePath )
        ? (object) json_decode( file_get_contents( $filePath, true ) )
        : (object) [];

    return $config;
}

/**
 * OBJECT TO ARRAY
 * 
 * Recursively convert an object to an array.
 * 
 * @since 1.0.0
 * @author Nate Flynn
 * 
 * @param object|array $item The object or array element to traverse.
 */
function object_to_array( $item ) : array {
    $arr = (array) $item;

    // Loop through the array and recursively convert any objects into arrays
    foreach( $arr as &$attribute )
        if( is_object( $attribute ) || is_array( $attribute ) )
            $attribute = object_to_array( $attribute );

    return $arr;
}

