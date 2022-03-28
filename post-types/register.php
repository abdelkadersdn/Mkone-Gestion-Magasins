<?php

function mkone_register_magasin_type() {
    $labels = array (

        'name' => __('Magasins', MKONE_DOMAIN), //utilisation de la "internalization" et "domain" pour aider à la traduction si plusieurs langues sont disponibles
        'singular_name' => __('Magasin', MKONE_DOMAIN),
        'featured_image' => __('Image du Magasin', MKONE_DOMAIN),
        'set_featured_image' => __('Définir Image', MKONE_DOMAIN),
        'remove_featured_image' => __('Supprimer Image', MKONE_DOMAIN),
        'use_featured_image' => __('Utiliser Image', MKONE_DOMAIN),
        'archive' => __('Liste des Magasins', MKONE_DOMAIN),
        'add_new' => __('Ajouter Nv. Magasin', MKONE_DOMAIN),
        'add_new_item' => __('Ajouter Nv. Magasin', MKONE_DOMAIN)
    );

    $args = array(
        'labels' => $labels,
        'public' => true, //exposer the custom post type pour front-end et le wp admin
        'has_archive' => 'magasins',
        'rewrite' => array('has_front' => true),
        'menu_icon' => 'dashicons-building',
        'supports' => array('title', 'editor', 'thumbnail'),
        'show_in_rest' => false
    );


    register_post_type('magasin', $args);
}