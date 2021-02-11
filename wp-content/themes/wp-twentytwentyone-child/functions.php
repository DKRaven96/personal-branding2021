<?php

function enqueue_twentytwentyone_child(){
	wp_enqueue_style('twentytwentyone-child-css', get_stylesheet_directory_uri() . '/style.css', array( 'twenty-twenty-one-style' ), time());
    wp_enqueue_script('twentytwentyone-child-js', get_stylesheet_directory_uri() . '/script.js', array(), time());
}
add_action('wp_enqueue_scripts', 'enqueue_twentytwentyone_child');