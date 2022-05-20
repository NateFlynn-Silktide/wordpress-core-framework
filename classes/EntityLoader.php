<?php

/**
 * ENTITY LOADER
 * 
 * Reusable class for loading admin content, setting up entities with JSON config
 * files and registering menu and submenu pages.
 * 
 * @package NateFlynn
 * @subpackage Core
 * 
 * @since 1.0.0
 * @author Nate Flynn
 */

namespace NateFlynn\Core\Classes;

class EntityLoader {

    /**
     * ENTITY TYPE
     * This should be the name of an `Entity` class used to
     * register this particular entity. That class should extend the
     * base `Entity` class.
     * @var string
     */
    public $entity_type = null;

    public $slug = null;

    public $register_action = null;

    public $directories_filter = null;

    public $glob_pattern = "*";

    public $directories = [];

    public $entities = [];


    // CLASS METHODS

    /**
     * CLASS CONSTRUCTOR
     */
    public function __construct( string $entity_type = 'PostType', string $register_action = 'after_setup_theme', ?string $glob_pattern = "*" ) {
        $this->setup( $entity_type, $register_action, $glob_pattern );
        $this->register_hooks();
    }

    /**
     * SETUP
     * 
     * Sets up the class properties and assignments.
     */
    private function setup( string $entity_type, string $register_action, string $glob_pattern ) : void {
        $this->entity_type = $entity_type;
        $this->entity_slug = $this->sanitize_entity_type_name( $entity_type );
        $this->glob_pattern = $glob_pattern ?: "*";

        $this->register_action = $register_action ?: "after_setup_theme";
        $this->directories_filter = "$this->entity_slug/directories";
    }

    /**
     * SANITIZE ENTITY TYPE NAME
     * 
     * Generates an entity type name based on the class name of the requested entity.
     */
    private function sanitize_entity_type_name( string $entity_type ) : string {
        // Split the entity type by capital letter
        $entityTypeParts = preg_split( '/(?=[A-Z])/', $entity_type, -1, PREG_SPLIT_NO_EMPTY );
        return implode( '-', $entityTypeParts );
    }

    /**
     * REGISTER HOOKS
     * 
     * Registers any hooked actions or filters for this class.
     */
    private function register_hooks() : void {
        add_action( $this->register_action, array( $this, 'register' ) );
    }

    /**
     * ADD ENTITY
     * 
     * Adds a found entity to the entities array.
     */
    private function add_entity( string $slug, string $file, string $config ) : void {
        $this->entities[ $slug ] = [
            "file" => $file,
            "config" => $config
        ];
    }

    /**
     * LOAD ENTITIES
     * 
     * Loop through directories and add all found post types
     * This also allows for subsequent directories to override post type
     * registration.
     */
    private function load_entities() : void {
        foreach( $this->directories as $dir ) { $this->get_entities( $dir ); }
    }


    /**
     * GET ENTITIES
     * 
     * Retrieve the entity registration files from a specific directory
     * and add it into the loader.
     * 
     * @param string $directory The absolute path to the entity directory
     * 
     * @return bool|array False if the directory is void. Otherwise an array of registered entities.
     */
    public function get_entities( ?string $directory = null ) {
        if( ! $directory ) return false;

        $dir = rtrim( $directory, '/' );
        $pattern = $this->glob_pattern ? "$dir/$this->glob_pattern" : "$dir/*";

        // Loop through all files that match `$pattern`
        foreach( glob( $pattern ) as $entity_dir ) :
            /**
             * @todo: Write a handler for a single file entity
             */
            if( ! is_dir( $entity_dir ) ) continue;

            $slug = basename( $entity_dir );
            $file = "$entity_dir/index.php";
            $config = "$entity_dir/config.json";

            // Load the entity
            if( file_exists( $file ) ) :
                // Add the entity to the entities array
                $this->add_entity( $slug, $file, $config );
                // Require the entity base file
                require_once( $file );
            endif;
        endforeach;

        return $this->entities;
    }

    /**
     * REGISTER ENTITIES
     * 
     * Generates an entity classname and 
     */
    public function register() : void {
        $this->directories = apply_filters( $this->directories_filter, array() );
        $this->load_entities();

        foreach( $this->entities as $slug => $data ) :
            $entity_class = "\\NateFlynn\\Core\\Entities\\$this->entity_type";
            $entity = new $entity_class( $slug, $data );
            $entity->register();
        endforeach;
    }
}