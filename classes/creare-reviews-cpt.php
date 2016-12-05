<?php

class Creare_Reviews_CPT
{

    /**
     * Construct the plugin object
     */
    public function __construct()
    {
        // add action for init
        add_action('init', array($this, 'init'), 1);

        // add init for flushing rewrite rules
        add_action('init', array($this, 'flush_rewrites'));
    }

    /**
     * Initialise function
     */
    public function init()
    {

        // post type labels
        $labels = array(
            'name' => _x('Reviews', 'post type general name', 'your-plugin-textdomain'),
            'singular_name' => _x('Review', 'post type singular name', 'your-plugin-textdomain'),
            'menu_name' => _x('Reviews', 'admin menu', 'your-plugin-textdomain'),
            'name_admin_bar' => _x('Review', 'add new on admin bar', 'your-plugin-textdomain'),
            'add_new' => _x('Add New', 'review', 'your-plugin-textdomain'),
            'add_new_item' => __('Add New Review', 'your-plugin-textdomain'),
            'new_item' => __('New Review', 'your-plugin-textdomain'),
            'edit_item' => __('Edit Review', 'your-plugin-textdomain'),
            'view_item' => __('View Review', 'your-plugin-textdomain'),
            'all_items' => __('All Reviews', 'your-plugin-textdomain'),
            'search_items' => __('Search Reviews', 'your-plugin-textdomain'),
            'parent_item_colon' => __('Parent Reviews:', 'your-plugin-textdomain'),
            'not_found' => __('No reviews found.', 'your-plugin-textdomain'),
            'not_found_in_trash' => __('No reviews found in Trash.', 'your-plugin-textdomain')
        );

        // post type args
        $args = array(
            'labels' => $labels,
            'public' => true,
            'publicly_queryable' => true,
            'show_ui' => true,
            'show_in_menu' => true,
            'query_var' => true,
            'rewrite' => array('slug' => 'reviews'),
            'capability_type' => 'post',
            'has_archive' => true,
            'hierarchical' => false,
            'menu_icon' => 'dashicons-star-half',
            'supports' => array('title', 'editor')
        );

        // register post type
        register_post_type('clr_reviews', $args);

    }

    /* 
     * Flush rewrites
     */
    public function flush_rewrites()
    {
        // if option exists, flush rewrites & delete option
        if (get_option('clr_flush_rewrites')) {
            flush_rewrite_rules();
            delete_option('clr_flush_rewrites');
        }
    }

}