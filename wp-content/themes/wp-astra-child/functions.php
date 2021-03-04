<?php
/**
 * Astra Child Theme functions and definitions
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package Astra Child
 * @since 1.0.0
 */

/**
 * Define Constants
 */
define( 'CHILD_THEME_ASTRA_CHILD_VERSION', time() );

/**
 * Enqueue styles
 */
function child_enqueue_styles() {
	wp_enqueue_style( 'astra-child-theme-css', get_stylesheet_directory_uri() . '/style.css', array('astra-theme-css'), CHILD_THEME_ASTRA_CHILD_VERSION);
    wp_register_script('typed-js', 'https://cdnjs.cloudflare.com/ajax/libs/typed.js/2.0.5/typed.min.js');
    wp_enqueue_script('astra-child-theme-js', get_stylesheet_directory_uri() . '/script.js', array('typed-js'), CHILD_THEME_ASTRA_CHILD_VERSION);
}

add_action( 'wp_enqueue_scripts', 'child_enqueue_styles');
