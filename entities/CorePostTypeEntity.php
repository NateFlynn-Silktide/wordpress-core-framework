<?php

/**
 * CORE POST TYPE CLASS.
 * 
 * Used to modify a core post type from a `config.json` file.
 * 
 * @package     NateFlynn
 * @subpackage  Core
 * 
 * @since       1.0.0
 * @author      Nate Flynn <nate@NateFlynn.co.uk> (https://NateFlynn.co.uk)
 * 
 * @uses        NateFlynn\Singleton_Trait
 */

namespace NateFlynn\Core\Entities;

class CorePostTypeEntity {

    public $baseDIR = null;

    public $config = null;

    public function __construct( $dir ) {
        $this->setup( $dir );
        $this->register_hooks();
    }

    public function setup( $dir ) {
        $this->baseDIR = $dir;
        $config_file = trailingslashit( $this->baseDIR  ) . 'config.json';
        $this->config = file_exists( $config_file )
            ? json_decode( file_get_contents( $config_file ) )
            : (object) [];
    }

    public function register_hooks() {
        add_action( 'admin_init', array( $this, 'block_template' ) );
        add_action( 'admin_init', array( $this, 'custom_supports' ) );
    }

    public function block_template() {
        $template = isset( $this->config->template ) ? $this->config->template : [];

        $object = get_post_type_object( $this->config->name );
        $hookname = "post-type/{$this->config->name}/block-template";

        $object->template = apply_filters( $hookname, $template );
        $object->template_lock = isset( $this->config->templateLock ) 
            ? $this->config->templateLock
            : false;
    }

    public function custom_supports() {
        $supports = isset( $this->config->supports ) ? $this->config->supports : [];

        foreach( $supports as $feature ) 
            add_post_type_support( $this->config->name, $feature );
    }

}