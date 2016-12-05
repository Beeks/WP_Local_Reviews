<?php

class Creare_Reviews_Taxonomies
{

    /**
     * Construct the plugin object
     */
    public function __construct()
    {
        add_action('init', array($this, 'init'), 1);
    }

    /**
     * Initialise function
     */
    public function init()
    {
        // Add new taxonomy, make it hierarchical (like categories)
        $labels = array(
            'name' => _x('Areas', 'taxonomy general name'),
            'singular_name' => _x('Area', 'taxonomy singular name'),
            'search_items' => __('Search Areas'),
            'all_items' => __('All Areas'),
            'parent_item' => __('Parent Area'),
            'parent_item_colon' => __('Parent Area:'),
            'edit_item' => __('Edit Area'),
            'update_item' => __('Update Area'),
            'add_new_item' => __('Add New Area'),
            'new_item_name' => __('New Area Name'),
            'menu_name' => __('Area'),
        );

        $args = array(
            'hierarchical' => true,
            'labels' => $labels,
            'show_ui' => true,
            'show_admin_column' => true,
            'query_var' => true,
            'rewrite' => array('slug' => 'area'),
        );

        register_taxonomy('area', array('clr_reviews', 'clr_areas'), $args);

        // Add new taxonomy, make it hierarchical (like categories)
        $labels = array(
            'name' => _x('Services', 'taxonomy general name'),
            'singular_name' => _x('Service', 'taxonomy singular name'),
            'search_items' => __('Search Services'),
            'all_items' => __('All Services'),
            'parent_item' => __('Parent Service'),
            'parent_item_colon' => __('Parent Service:'),
            'edit_item' => __('Edit Service'),
            'update_item' => __('Update Service'),
            'add_new_item' => __('Add New Service'),
            'new_item_name' => __('New Service Name'),
            'menu_name' => __('Service'),
        );

        $args = array(
            'hierarchical' => true,
            'labels' => $labels,
            'show_ui' => true,
            'show_admin_column' => true,
            'query_var' => true,
            'rewrite' => array('slug' => 'service'),
        );

        register_taxonomy('service', array('clr_reviews', 'clr_areas'), $args);
    }

}