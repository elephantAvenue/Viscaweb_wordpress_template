<?php

require_once __DIR__ . '/vendor/autoload.php';

use Viscaweb\Theme;


function enqueue_parent_styles()
{
    $parent_style = 'parent-style'; // Estos son los estilos del tema padre recogidos por el tema hijo.
    wp_enqueue_style($parent_style, get_template_directory_uri() . '/style.css');
    wp_enqueue_style('child-style',
        get_stylesheet_directory_uri() . '/style.css',
        array($parent_style),
        wp_get_theme()->get('Version')
    );
}

add_action('wp_enqueue_scripts', 'enqueue_parent_styles');

$app = new Theme();
$app->init();