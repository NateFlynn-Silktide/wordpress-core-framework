<?php

/**
 * BASE ENTITY
 * 
 * An Entity can be one of a number of different loadable objects within WordPress.
 * For example; post types, post type categories, taxonomies, settings pages, adming pages etc.
 * 
 * The base Entity class should be extended to support loading methods for the required Entity type.
 * 
 * @package NateFlynn
 * @subpackage Core
 * 
 * @since 1.0.0
 * @author Nate Flynn
 */

namespace NateFlynn\Core\Entities;

class BaseEntity {

    // CLASS PROPERTIES

    public $slug = null;

    public $config_file = null;

    public $file = null;

    public $path = null;

    public $config = null;


    // CLASS METHODS

    public function __construct( string $slug, array $args ) {
        $this->setup( $slug, $args );
    }

    public function setup( string $slug, array $args ) : void {
        $this->slug = $slug;
        $this->config_file = $args['config'];
        $this->file = $args['file'];
        $this->path = basename( $this->config_file );

        $this->config = file_exists( $this->config_file )
            ? (object) json_decode( file_get_contents( $this->config_file ), true )
            : null;
    }

    public function getConfig() : object {
        return $this->config ?: (object) [];
    }

    public function is_enabled() : bool {
        return isset( $this->config->enabled )
            ? $this->config->enabled
            : true;
    }

}