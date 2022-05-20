<?php

/**
 * POST TYPE ENTITY
 * 
 * Contains registration methods for loading custom post types into WordPress.
 * 
 * @package NateFlynn
 * @subpackage Core
 * 
 * @since 1.0.0
 * @author Nate Flynn
 */

namespace NateFlynn\Core\Entities;

use \NateFlynn\Core\Entities\BaseEntity;
use \NateFlynn\Core\Interfaces\EntityInterface;

use function \NateFlynn\Core\object_to_array;

class PostTypeEntity extends BaseEntity implements EntityInterface {

    public $singular_name = null;

    public $plural_name = null;

    public $labels = [];

    public $args = [];
 
    public function setup( string $slug, array $args ) : void {
        parent::setup( $slug, $args );

        if( $this->config ) {
            if( isset( $this->config->name ) ) $this->slug = $this->config->name;
            
            $this->plural_name = isset( $this->config->labels->plural_name )
                ? $this->config->labels->plural_name
                : $this->config->label;

            $this->singular_name = isset( $this->config->labels->singular_name )
                ? $this->config->labels->singular_name
                : $this->plural_name;

            $this->labels = $this->labels();
        }
    }

    /**
     * DEFAULT POST TYPE LABELS
     * 
     * Uses the singular and plural versions of the post type label
     * to generate logical default labels for the post type.
     */
    private function default_labels() : array {

        // Lower case variations of labels
        $singular_name_lc = strtolower( $this->singular_name );
        $plural_name_lc = strtolower( $this->plural_name );

        return apply_filters( "post-type/$this->slug/default-labels", [
            'name'                      => _x( $this->plural_name, 'post type name' ),
            'singular_name'             => _x( $this->singular_name, 'post type singular name' ),
            'add_new'                   => _x( 'Add New', $this->singular_name ),
            'add_new_item'              => sprintf( __( 'Add New %s' ), $this->singular_name ),
            'edit_item'                 => sprintf( __( 'Edit %s' ), $this->singular_name ),
            'new_item'                  => sprintf( __( 'New %s' ), $this->singular_name ),
            'view_item'                 => sprintf( __( 'View %s' ), $this->singular_name ),
            'view_items'                => sprintf( __( 'View %s' ), $this->plural_name ),
            'search_items'              => sprintf( __( 'Search %s' ), $this->plural_name ),
            'not_found'                 => sprintf( __( 'No %s found' ), $plural_name_lc ),
            'not_found_in_trash'        => sprintf( __( 'No %s found in Trash' ), $plural_name_lc ),
            'parent_item_colon'         => sprintf( __( 'Parent %s:' ), $this->plural_name ),
            'all_items'                 => sprintf( __( 'All %s' ), $this->plural_name ),
            'archives'                  => sprintf( __( '%s Archives' ), $this->singular_name ),
            'attributes'                => sprintf( __( '%s Attributes' ), $this->singular_name ),
            'insert_into_item'          => sprintf( __( 'Insert into %s' ), $singular_name_lc ),
            'uploaded_to_this_item'     => sprintf( __( 'Uploaded to this %s' ), $singular_name_lc ),
            'featured_image'            => __( 'Featured image' ),
            'set_featured_image'        => __( 'Set featured image' ),
            'remove_featured_image'     => __( 'Remove featured image' ),
            'use_featured_image'        => __( 'Use as featured image' ),
            'menu_name'                 => _x( $this->plural_name, 'post type menu name' ),
            'filter_items_list'         => sprintf( __( 'Filter %s list' ), $plural_name_lc ),
            'filter_by_date'            => sprintf( __( 'Filter %s by date' ), $plural_name_lc ),
            'items_list_navigation'     => sprintf( __( '%s list navigation' ), $this->plural_name ),
            'items_list'                => sprintf( __( '%s list' ), $this->plural_name ),
            'item_published'            => sprintf( __( '%s published' ), $this->singular_name ),
            'item_published_privately'  => sprintf( __( '%s published privately' ), $this->singular_name ),
            'item_reverted_to_draft'    => sprintf( __( '%s reverted to draft' ), $this->singular_name ),
            'item_scheduled'            => sprintf( __( '%s scheduled' ), $this->singular_name ),
            'item_updated'              => sprintf( __( '%s updated' ), $this->singular_name ),
            'item_link'                 => sprintf( __( '%s Link' ), $this->singular_name ),
            'item_link_description'     => sprintf( __( 'A link to a %s' ), $singular_name_lc )
        ], $this->singular_name, $this->plural_name );

    }

    private function labels() : array {
        return apply_filters( "post-type/$this->slug/labels",
            $this->default_labels(),
            $this->singular_name,
            $this->plural_name
        );
    }

    /**
     * DEFAULT POST TYPE ARGS
     * 
     * 
     */
    private function default_args() : array {
        return apply_filters( "post-type/$this->slug/default-args", [
            'label'                 => $this->labels['name'],
            'labels'                => $this->labels,
            'description'           => '',
            'public'                => true,
            'hierarchical'          => false,
            'exclude_from_search'   => false,
            'publicly_queryable'    => true,
            'show_ui'               => true,
            'show_in_menu'          => true,
            'show_in_nav_menus'     => true,
            'show_in_admin_bar'     => true,
            'show_in_rest'          => true,
            'menu_position'         => null,
            'capability_type'       => 'post',
            'supports'              => [ 'title', 'editor' ],
            'has_archive'           => false,
            'rewrite'               => true,
            'can_export'            => true,
            'delete_with_user'      => false
        ], $this->labels );
    }

    private function generated_args() : array {
        $args = [
            'label'         => isset( $this->config->labels->plural_name ) ? $this->config->labels->plural_name : $this->config->label,
            'labels'        => isset( $this->config->labels ) ? object_to_array( $this->config->labels ) : array(),
            'description'   => isset( $this->config->description ) ? $this->config->description : '',
            'rewrite'       => isset( $this->config->rewrite ) ? (array) $this->config->rewrite : true,
            'supports'      => isset( $this->config->supports ) ? (array) $this->config->supports : null,
            'taxonomies'    => isset( $this->config->taxonomies ) ? (array) $this->config->taxonomies : array(),
            'template'      => isset( $this->config->template ) ? $this->config->template : array(),
            'template_lock' => isset( $this->config->template_lock ) ? $this->config->template_lock : null,
            'category'      => isset( $this->config->category ) ? $this->config->category : false,
            'show_in_rest'  => isset( $this->config->show_in_rest ) ? $this->config->show_in_rest : true
        ];

        // Merge post type attributes in separately
        $attributes = object_to_array( $this->config->attributes );
        $args = $args + $attributes;

        return $args;
    }

    private function args() : array {
        return apply_filters( "post-type/$this->slug/args", wp_parse_args(
            $this->generated_args(),
            $this->default_args()
        ));
    }

    public function add_to_category( array $post_types ) : array {
        $post_types[ $this->slug ] = (object) $this->args;
        return $post_types;
    }

    public function register_metadata() : void {
        if( isset( $this->config->meta ) ) {
            foreach( (array) $this->config->meta as $key => $args )
                register_post_meta( $this->slug, $key, object_to_array( $args ) );
        }
    }

    public function register_object_taxonomies() {
        foreach( $this->args['taxonomies'] as $taxonomy )
            register_taxonomy_for_object_type( $taxonomy, $this->slug );
    }

    public function maybe_create_archive_post() : void {
        if( ! isset( $this->config->rewrite ) || ! $this->config->rewrite || $this->config->rewrite->with_front ) return;

        if( ! isset( $this->config->parent ) || ! $this->config->parent )
            $this->config->parent = "page";

            $path_parts = $this->config->rewrite
                ? explode( '/', $this->config->rewrite->slug )
                : [ $this->slug ];

            $path = end( $path_parts );

            // Check if parent post type exists
            if( ! post_type_exists( $this->config->parent ) ) return;

            // Create the archive post if it doesn't exist yet
            if( ! get_page_by_path( $path, 'OBJECT', $this->config->parent ) ) :
                wp_insert_post( array(
                    'post_type' => $this->config->parent,
                    'post_status' => 'publish',
                    'post_title' => $this->plural,
                    'post_name' => $path
                ));
            endif;

            // Add a custom label to the archive post so we can differentiate it from
            // regular posts / pages.
            add_filter( 'display_post_states', array( $this, 'label_archive_post' ), 10, 2 );
    }

    public function label_archive_post( array $post_states, \WP_Post $post ) : array {
        $path_parts = $this->config->rewrite->slug
            ? explode( '/', $this->config->rewrite->slug )
            : [ $this->slug ];

        $path = end( $path_parts );

        if( $post->post_type === $this->config->parent && $post->post_name === $path )
            $post_states[] = sprintf( __( '%s Homepage', $this->plural ) );

        return $post_states;
    }

    public function edit_archive_button() {
        global $post_type;

        if( $post_type == $this->slug ) :
            $parent_post_type = $this->config->parent ?? false;

            if( ! $parent_post_type || ! post_type_exists( $parent_post_type ) ) return;

            // Check if the archive post exists in the parent post type. If not, exit out.
            $archive_slug_parts = explode( '/', $this->config->rewrite->slug );
            $archive_slug = end( $archive_slug_parts );

            $archive_page = get_page_by_path( $archive_slug, 'OBJECT', $parent_post_type );
            if( ! $archive_page || is_wp_error( $archive_page ) ) return false;

            $edit_link = get_edit_post_link( $archive_page->ID );

            $button = sprintf( '<a class="page-title-action" href="%1$s">%2$s</a>',
                $edit_link,
                sprintf( __( 'Edit %s Homepage' ), $this->singular_name )
            );

            printf(
                '<script>
                    jQuery( function() {
                        jQuery( "%1$s" ).insertBefore( "body.post-type-%2$s .wrap .wp-header-end" );
                    });
                </script>',
                $button,
                $this->slug
            );
        endif;
    }

    public function register() : void {
        // Exit early if the post type is disabled
        if( ! $this->config || ! $this->is_enabled() ) return;

        // Register the post type to a post type category (if set)
        if( isset( $this->config->category ) && $this->config->category !== '' ) {
            $this->args['show_in_menu'] = false;
            add_filter( "post-type-category/{$this->config->category}/post-types", array( $this, 'add_to_category' ) );
        }

        // Register the post type with WordPress
        if( ! post_type_exists( $this->slug ) )
            register_post_type( $this->slug, $this->args() );

        // Attach custom taxonomies to the post type
        if( isset( $this->args['taxonomies'] ) && $this->args['taxonomies'] && ! empty( $this->args['taxonomies'] ) )
            add_action( 'init', array( $this, 'register_object_taxonomies' ), 20 );

        // Register any custom metadata with WordPress
        $this->register_metadata();

        // Create an archive post in the correct location if it doesn't exist
        add_action( '_admin_head', array( $this, 'maybe_create_archive_post' ) );
        add_action( 'admin_head', array( $this, 'edit_archive_button' ) );
    }

}