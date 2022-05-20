<?php

/**
 * POST TYPE CATEGORY ENTITY
 * 
 * Contains registration methods for loading custom post type categories into WordPress.
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

class PostTypeCategoryEntity extends BaseEntity implements EntityInterface {

    /**
     * The link to the primary post type.
     * @var string  $primary_link
     */
    public $primary_link = false;

    /**
     * The post types registered to this category.
     * @var array   $post_types
     */
    public $post_types = [];

    /**
     * The admin page for this post type category.
     * @var \WP_Admin_Page  $admin_page
     */
    public $admin_page = null;

    public function setup( string $slug, array $args ) : void {
        parent::setup( $slug, $args );
    }

    private function generated_args() : array {
        $args = [
            'page_title'    => $this->config->title,
            'menu_title'    => isset( $this->config->menu_title ) ? $this->config->menu_title : $this->config->title,
            'capability'    => isset( $this->config->capability ) ? $this->config->capability : 'edit_posts',
            'menu_slug'     => sprintf( "post_type_category__%s", isset( $this->config->name ) ? $this->config->name : $this->slug ),
            'function'      => isset( $this->config->function ) ? $this->config->function : '__return_false',
            "icon_url"      => isset( $this->config->icon ) ? $this->config->icon : null,
            "position"      => isset( $this->config->position ) ? $this->config->position : 20
        ];

        if( strstr( $args['icon_url'], '<svg' ) ) 
            $args['icon_url'] = 'data:image/svg+xml;base64,' . base64_encode( str_replace( '\'', '"', $args['icon_url'] ) );
        
        if( strstr( $args['icon_url'], '.svg' ) )
            $args['icon_url'] = 'data:image/svg+xml;base64,' . base64_encode( str_replace( '\'', '"', file_get_contents( $args['icon_url'] ) ) );

        return $args;
    }

    private function args() : array {
        return $this->args = apply_filters( "post-type-category/$this->slug/args", $this->generated_args() );
    }

    private function register_post_types() : void {
        $this->post_types = apply_filters( "post-type-category/$this->slug/post-types", array() );
    }

    private function register_admin_page() : string {
        extract( $this->args, EXTR_OVERWRITE );

        return $this->admin_page = add_menu_page(
            $page_title,
            $menu_title,
            $capability,
            $menu_slug,
            $function,
            $icon_url,
            $position
        );
    }

    private function register_post_type_page( $post_type, $config ) : void {
        $this->submenu_page = add_submenu_page(
            $this->args['menu_slug'],
            $config->labels['name'],
            $config->labels['menu_name'],
            $this->args['capability'],
            "edit.php?post_type=$post_type",
            "",
            $config->menu_position ?: 10
        );

        $this->register_post_type_parent_file( $post_type );
    }

    private function register_post_type_parent_file( $current_post_type ) : void {
        add_filter( "parent_file", function( $parent_file ) use ( $current_post_type ) : string {
            global $plugin_page, $submenu_file, $post_type;

            if( $post_type === $current_post_type ) {
                $plugin_page = $this->primary_link;
                $submenu_file = "edit.php?post_type=$post_type";
            }

            return $parent_file;
        });
    }

    /**
     * Register the admin page for the post type category.
     * 
     * @return  void
     */
    public function register() : void {
        // Exit here if the category is disabled via it's config
        if( ! $this->config || ! $this->is_enabled() ) { return; }

        $this->register_admin_page();
        
        foreach( $this->post_types as $post_type => $config ) {
            $this->register_post_type_page( $post_type, $config );

            if( ! $this->primary_link )
                $this->primary_link = "edit.php?post_type={$post_type}";
        }

        remove_submenu_page( $this->args['menu_slug'], $this->args['menu_slug'] );
    }

}