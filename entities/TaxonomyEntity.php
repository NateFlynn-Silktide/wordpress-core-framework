<?php

/**
 * TAXONOMY ENTITY
 * 
 * Contains registration methods for loading custom taxonomies into WordPress.
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

class TaxonomyEntity extends BaseEntity implements EntityInterface {

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
     * DEFAULT TRAXONOMY LABELS
     * 
     * Uses the singular and plural versions of the taxonomy label
     * to generate logical default labels for the taxonomy.
     */
    private function default_labels() : array {

        // Lower case variations of labels
        $singular_name_lc = strtolower( $this->singular_name );
        $plural_name_lc = strtolower( $this->plural_name );

        return apply_filters( "taxonomy/$this->slug/default-labels", [
            'name'                          => _x( $this->plural_name, 'post type name' ),
            'singular_name'                 => _x( $this->singular_name, 'post type singular name' ),
            'search_items'                  => sprintf( __( 'Search %s' ), $plural_name_lc ),
            'popular_items'                 => sprintf( __( 'Popular %s' ), $plural_name_lc ),
            'all_items'                     => sprintf( __( 'All %s' ), $plural_name_lc ),
            'parent_item'                   => sprintf( __( 'Parent %s' ), $singular_name_lc ),
            'parent_item_colon'             => sprintf( __( 'Parent %s:' ), $singular_name_lc ),
            'edit_item'                     => sprintf( __( 'Edit %s' ), $singular_name_lc ),
            'view_item'                     => sprintf( __( 'View %s' ), $singular_name_lc ),
            'update_item'                   => sprintf( __( 'Update %s' ), $singular_name_lc ),
            'add_new_item'                  => sprintf( __( 'Add new %s' ), $singular_name_lc ),
            'new_item_name'                 => sprintf( __( 'New %s name' ), $singular_name_lc ),
            'separate_items_with_commas'    => sprintf( __( 'Separate %s with commas' ), $plural_name_lc ),
            'add_or_remove_items'           => sprintf( __( 'Add or remove %s' ), $plural_name_lc ),
            'add_new'                       => _x( 'Add New', $this->singular_name ),
            'view_items'                    => sprintf( __( 'View %s' ), $this->plural_name ),
            'not_found'                     => sprintf( __( 'No %s found' ), $plural_name_lc ),
            'not_found_in_trash'            => sprintf( __( 'No %s found in Trash' ), $plural_name_lc ),
            'parent_item_colon'             => sprintf( __( 'Parent %s:' ), $this->plural_name ),
            'archives'                      => sprintf( __( '%s Archives' ), $this->singular_name ),
            'attributes'                    => sprintf( __( '%s Attributes' ), $this->singular_name ),
            'insert_into_item'              => sprintf( __( 'Insert into %s' ), $singular_name_lc ),
            'uploaded_to_this_item'         => sprintf( __( 'Uploaded to this %s' ), $singular_name_lc ),
            'featured_image'                => __( 'Featured image' ),
            'set_featured_image'            => __( 'Set featured image' ),
            'remove_featured_image'         => __( 'Remove featured image' ),
            'use_featured_image'            => __( 'Use as featured image' ),
            'menu_name'                     => _x( $this->plural_name, 'post type menu name' ),
            'filter_items_list'             => sprintf( __( 'Filter %s list' ), $plural_name_lc ),
            'filter_by_date'                => sprintf( __( 'Filter %s by date' ), $plural_name_lc ),
            'items_list_navigation'         => sprintf( __( '%s list navigation' ), $this->plural_name ),
            'items_list'                    => sprintf( __( '%s list' ), $this->plural_name ),
            'item_published'                => sprintf( __( '%s published' ), $this->singular_name ),
            'item_published_privately'      => sprintf( __( '%s published privately' ), $this->singular_name ),
            'item_reverted_to_draft'        => sprintf( __( '%s reverted to draft' ), $this->singular_name ),
            'item_scheduled'                => sprintf( __( '%s scheduled' ), $this->singular_name ),
            'item_updated'                  => sprintf( __( '%s updated' ), $this->singular_name ),
            'item_link'                     => sprintf( __( '%s Link' ), $this->singular_name ),
            'item_link_description'         => sprintf( __( 'A link to a %s' ), $singular_name_lc )
        ], $this->singular_name, $this->plural_name );

    }

    private function labels() : array {
        return apply_filters( "taxonomy/$this->slug/labels",
            $this->default_labels(),
            $this->singular_name,
            $this->plural_name
        );
    }

    /**
     * DEFAULT TAXONOMY ARGS
     * 
     * 
     */
    private function default_args() : array {
        return apply_filters( "taxonomy/$this->slug/default-args", [
            'label'                 => $this->labels['name'],
            'labels'                => $this->labels,
            'description'           => '',
            'public'                => true,
            'publicly_queryable'    => true,
            'hierarchical'          => false,
            'show_ui'               => true,
            'show_in_menu'          => true,
            'show_in_nav_menus'     => true,
            'show_in_admin_bar'     => true,
            'show_in_rest'          => true,
            'show_in_quick_edit'    => true,
            'show_admin_column'     => true,
            'rewrite'               => true
        ], $this->labels );
    }

    private function generated_args() : array {
        $args = [
            'label'         => isset( $this->config->labels->plural_name ) ? $this->config->labels->plural_name : $this->config->label,
            // 'labels'        => isset( $this->config->labels ) ? object_to_array( $this->config->labels ) : array(),
            'description'   => isset( $this->config->description ) ? $this->config->description : '',
            'rewrite'       => isset( $this->config->rewrite ) ? (array) $this->config->rewrite : true,
            'supports'      => isset( $this->config->supports ) ? (array) $this->config->supports : null,
            'object_type'   => isset( $this->config->object_type ) ? (array) $this->config->object_type : [],
            'category'      => isset( $this->config->category ) ? $this->config->category : false,
            'show_in_rest'  => isset( $this->config->show_in_rest ) ? $this->config->show_in_rest : true
        ];

        // Merge post type attributes in separately
        $attributes = object_to_array( $this->config->attributes );
        $args = $args + $attributes;

        return $args;
    }

    private function args() : array {
        return apply_filters( "taxonomy/$this->slug/args", wp_parse_args(
            $this->generated_args(),
            $this->default_args()
        ));
    }

    public function add_to_category( array $taxonomies ) : array {
        $taxonomies[ $this->slug ] = (object) $this->args();
        return $taxonomies;
    }

    public function register() : void {
        // Exit early if the taxonomy is disabled
        if( ! $this->config || ! $this->is_enabled() ) return;

        // Register taxonomy to a post type category if set
        if( isset( $this->config->category ) && $this->config->category !== '' ) {
            $this->args['show_in_menu'] = false;
            add_filter( "post-type-category/{$this->config->category}/taxonomies", 
                array( $this, 'add_to_category' ) 
            );
        }

        register_taxonomy( $this->slug, $this->config->object_type, (array) $this->args() );
    }

}