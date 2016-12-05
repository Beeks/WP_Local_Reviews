<?php
/*
Plugin Name: Creare Local Reviews
Plugin URI: http://www.creare.co.uk
Description: Creare Local Reviews give you the power to add rated reviews to a custom post type on your website. Use shortcodes or custom loops to show reviews on the front-end.
Version: 1.0.0
Author: Daniel Long, James Bavington
Author URI: http://www.creare.co.uk
Text Domain: crearelocalreviews
Domain Path: /languages

------------------------------------------------------------------------
*/

require_once('classes/creare-reviews-cpt.php');
require_once('classes/creare-reviews-taxonomies.php');
require_once('classes/creare-reviews-metaboxes.php');
require_once('classes/creare-reviews-dashboard-widget.php');
require_once('classes/creare-reviews-frontend.php');
require_once('classes/creare-reviews-emails.php');
require_once('classes/creare-reviews-shortcode.php');
require_once('classes/creare-reviews-template.php');
require_once('classes/creare-reviews-dashboard-settings.php');
require_once( 'classes/creare-reviews-custom-setup.php' );

// Define plugin constant
define('CLR_PLUGIN_URL', plugin_dir_path(__FILE__));

if (!class_exists('Creare_Local_Reviews')) {
    class Creare_Local_Reviews
    {

        // scope variables for use within class
        protected $creare_reviews_cpt;
        protected $creare_reviews_taxonomies;
        protected $creare_reviews_metaboxes;
        protected $creare_reviews_dashboard_widget;
        protected $creare_reviews_frontend;
        protected $creare_reviews_emails;
        protected $creare_reviews_shortcode;
        protected $creare_reviews_dashboard_settings;
        protected $creare_reviews_custom_setup;

        /**
         * Construct the plugin object
         */
        public function __construct()
        {
            // CLASSES - REVIEWS
            // instantiate reviews cpt
            $this->creare_reviews_cpt = new Creare_Reviews_CPT();

            // instantiate reviews taxonomies
            $this->creare_reviews_taxonomies = new Creare_Reviews_Taxonomies();

            // instantiate metaboxes
            $this->creare_reviews_metaboxes = new Creare_Reviews_Metaboxes();

            // instantiate dashboard widget
            $this->creare_reviews_dashboard_widget = new Creare_Reviews_Dashboard_Widget();

            // instantiate frontend reviews
            $this->creare_reviews_frontend = new Creare_Reviews_Frontend();

            // instantiate frontend emails
            $this->creare_reviews_emails = new Creare_Reviews_Emails();

            // instantiate frontend shortcode
            $this->creare_reviews_shortcode = new Creare_Reviews_Shortcode();

            // instantiate dashboard settings
            $this->creare_reviews_dashboard_settings = new Creare_Reviews_Dashboard_Settings();

            // instantiate custom setup
            $this->creare_reviews_custom_setup = new Creare_Reviews_Custom_Setup();

            // ACTIONS
            // add action to create our leave a review page
            add_action('create_page', array($this->creare_reviews_frontend, 'create_page'));

            // add action to send activation email
            add_action('send_activation_email', array($this->creare_reviews_emails, 'send_activation_email'));

            //create db table on activation
            add_action('create_db_table', array($this, 'create_db_table'));

            // Installation and uninstallation hooks
            register_activation_hook(__FILE__, array('Creare_Local_Reviews', 'activate'));
            register_deactivation_hook(__FILE__, array('Creare_Local_Reviews', 'deactivate'));

            // add action for leave a review template
            add_action('init', array('Creare_Reviews_Template', 'get_instance'), 100);

            // settings page link
            add_filter('plugin_action_links', array($this, 'settings_link'), 10, 2);

        } // END public function __construct

        /**
         * Create db table to store review data
         */
        public function create_db_table()
        {
            global $wpdb;
            $charset_collate = $wpdb->get_charset_collate();
            $table_name = $wpdb->prefix . 'clr_reviews';

            $sql = "CREATE TABLE $table_name (
                `id` mediumint(9) NOT NULL AUTO_INCREMENT,
                `review_content` longtext NOT NULL,
                `complete` mediumint(9) NOT NULL,
                UNIQUE KEY id (id)
            ) $charset_collate;";

            require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
            dbDelta( $sql );
        }

        /**
         * Activate the plugin
         */
        public static function activate()
        {
            // Create page action on activation
            do_action('create_page');

            // add flag for flush rewrites check
            if (!get_option('clr_flush_rewrites')) {
                add_option('clr_flush_rewrites', true);
            }

            // Send email on activation
            do_action('send_activation_email');

            // create db table
            do_action('create_db_table');

        } // END public static function activate

        /**
         * Deactivate the plugin
         */
        public static function deactivate()
        {
            // Do stuff
        } // END public static function deactivate

        /*
         * Add settings link to plugin
         */
        public function settings_link($links, $file)
        {

            $plugin_file = basename(__FILE__);

            if (basename($file) == $plugin_file) {

                $settings_link = '<a href="admin.php?page=creare-local-reviews-settings">Settings</a>';

                array_unshift($links, $settings_link);

            }

            return $links;

        }

    } // END class Creare_Local_Reviews

} // END if(!class_exists('Creare_Local_Reviews'))

// instantiate the plugin class
$creare_local_reviews = new Creare_Local_Reviews();