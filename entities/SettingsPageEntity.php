<?php

/**
 * SETTINGS PAGE ENTITY
 * 
 * Contains registration methods for loading custom admin pages into WordPress.
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

class SettingsPageEntity extends BaseEntity implements EntityInterface {

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
            'menu_slug'     => sprintf( "settings_page__%s", isset( $this->config->name ) ? $this->config->name : $this->slug ),
            'function'      => isset( $this->config->function ) ? $this->config->function : array( $this, 'render_default_page' ),
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
        return $this->args = apply_filters( "settings-page/$this->slug/args", 
            wp_parse_args( object_to_array( $this->config ), $this->generated_args() ) 
        );
    }

    public function register_admin_page() : string {
        extract( $this->args(), EXTR_OVERWRITE );

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

    public function register_admin_sub_page() : string {
        extract( $this->args(), EXTR_OVERWRITE );

        return $this->admin_page = add_submenu_page(
            $parent,
            $page_title,
            $menu_title,
            $capability,
            $menu_slug,
            $function,
            $position
        );
    }

    public function custom_body_class( $classes ) {
        global $current_screen;

        if( $current_screen->base == $this->admin_page ) {
            $classes .= " nf-settings-page";
        }

        return $classes;
    }

    public function render_default_page() {
        if( isset( $this->config->route ) ) {
            printf( '<div id="%s"></div>', $this->config->route );
        }
    }

    private function register_settings() {
        $settings = isset( $this->config->settings )
            ? (object) $this->config->settings
            : false;

        if( ! $settings ) return false;

        $namespace = isset( $settings->namespace )
            ? $settings->namespace
            : str_replace( '-', '_', $this->slug );

        foreach( $settings->fields as $field => $options ) {
            $settingName = "{$namespace}_{$field}";
            register_setting( "{$namespace}_settings", $settingName, $options );
        }
    }

    public function register() : void {
        // Exit here if the settings page is disabled via it's config
        if( ! $this->config || ! $this->is_enabled() ) { return; }

        if( ! isset( $this->config->parent ) ) add_action( 'admin_menu', array( $this, 'register_admin_page' ) );
        else add_action( 'admin_menu', array( $this, 'register_admin_sub_page' ) );

        $this->register_settings();

        add_filter( 'admin_body_class', array( $this, 'custom_body_class' ) );
    }

}