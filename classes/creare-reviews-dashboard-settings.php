<?php

require_once('creare-reviews-register-menu-page.php');

class Creare_Reviews_Dashboard_Settings
{
    protected $creare_emails;
    protected $creare_register_menu;

    /**
     * Construct the plugin object
     */
    public function __construct()
    {
        // instantiate menu class
        $this->creare_register_menu = new Creare_Reviews_Register_Menu();

        // register settings
        add_action('admin_init', array($this, 'register_settings'), 10);

        // dashboard hook for js & css
        add_action('admin_enqueue_scripts', array($this, 'admin_enqueue_scripts'), 11);

        // add action to check if yoast installed
        add_action('admin_init', array($this, 'check_yoast'), 20);

        // add admin notice
        add_action('admin_notices', array($this, 'social_notice'));

        // add action for menu bubble
        add_action('admin_menu', array($this, 'add_user_menu_bubble'));

        // add ajax Actions
        add_action('wp_ajax_send_user_guide', array($this, 'send_user_guide'));

        // require email structure class file
        require_once('creare-reviews-emails.php');
        $this->creare_emails = new Creare_Reviews_Emails();
    }

    /*
     * Register settings
     */
    public function register_settings()
    {
        // Register settings
        $settings = get_option("clr_settings");
        if (empty($settings)) {
            $settings = array(
                'clr_facebook' => '',
                'clr_googleplus' => '',
                'clr_yelp' => '',
                'clr_tripadvisor' => '',
                'clr_yell' => '',
                'clr_scoot' => '',
                'clr_enablereviewpages' => '',
                'clr_enablestyling' => '1',
                'clr_pagetemplates' => ''
            );
            add_option(
                "clr_settings",
                $settings,
                '',
                'yes'
            );
        }
    }

    /**
     * Admin Enqueue Scripts
     */
    public function admin_enqueue_scripts($hook)
    {
        // register styles
        wp_register_style(
            'clr-settings-css',
            plugins_url('css/admin/settings.css', dirname(__FILE__)),
            '',
            null
        );

        // register styles
        wp_register_style(
            'clr-about-css',
            plugins_url('css/admin/about.css', dirname(__FILE__)),
            '',
            null
        );

        // register script for send user guide email
        wp_enqueue_script(
            'clr-settings-js',
            plugins_url('js/admin/settings.js', dirname(__FILE__)),
            array('jquery'),
            null
        );

        if ('creare-plugins_page_creare-plugins-clr-settings' == $hook) {
            // enqueue frontend styles & scripts
            wp_enqueue_style('clr-settings-css');
            wp_localize_script(
                'clr-settings-js',
                'ajax_send_user_guide',
                array('ajax_url' => admin_url('admin-ajax.php'))
            );
            wp_enqueue_script('clr-settings-js');
        }

        if('toplevel_page_creare-plugins' == $hook) {
            // enqueue frontend styles & scripts
            wp_enqueue_style('clr-about-css');
        }

    }

    /*
     * Check yoast is installed and copy across social site urls
     */
    public function check_yoast()
    {

        // Is the Yoast WordPress SEO plugin active?
        if (is_plugin_active('wordpress-seo/wp-seo.php') || defined('WPSEO_FILE')) {

            $settings = get_option("clr_settings");

            // get yoast options
            $yoastSocial = get_option('wpseo_social');
            $yoastFacebook = $yoastSocial['facebook_site'];
            $yoastGooglePlus = $yoastSocial['plus-publisher'];

            // update facebook url if added
            if (!empty($yoastFacebook) && $settings['clr_facebook'] == '') {
                $settings['clr_facebook'] = $yoastFacebook;
                update_option('clr_settings', $settings);
            }

            // update google+ url if added
            if (!empty($yoastGooglePlus) && $settings['clr_googleplus'] == '') {
                $settings['clr_googleplus'] = $yoastGooglePlus;
                update_option('clr_settings', $settings);
            }

        }
    }

    /*
     * Get social settings links
     */
    public function get_social_links()
    {
        // set variables
        $array = '';
        $c = '';

        $settings = get_option("clr_settings");
        $c = 0;
        // loop through and check if any are filled out
        foreach ($settings as $set) {

            if ($set != '' || $set != NULL) {
                $c++;
            }
        }

        // return value
        return $c;
    }

    /*
     * Admin notices
     */
    public function social_notice()
    {
        // if 2 or more fields are missing, show admin message
        if ($this->get_social_links() == '1' || $this->get_social_links() == '0') {
            echo '<div class="error"><p>';
            printf(__('Warning: At least 2 social media sites should be added for Creare Local Reviews to be most efficient. | <a href="%1$s">Add them now</a>'), 'admin.php?page=creare-plugins-clr-settings');
            echo "</p></div>";
        }
    }

    /*
     * Add bubble to CPT Menu if posts
     * To do: Use with WP Transient, to cache count instead of running all the time.
     */
    public function add_user_menu_bubble()
    {
        // gain access to menu
        global $menu;

        // set empty variables
        $post_count = '';

        // count all draft posts of reviews post type
        $post_count = wp_count_posts('clr_reviews');
        $post_count = $post_count->draft;

        // if count, loop through menu & add bubble
        if ($post_count) {
            foreach ($menu as $key => $value) {
                if ($menu[$key][2] == 'edit.php?post_type=clr_reviews') {
                    $menu[$key][0] .= ' <span class="update-plugins count-' . $post_count . '" title="' . $post_count . '"><span class="update-count">' . $post_count . '</span></span>';
                    return;
                }
            }
        }
    }

    /*
     * Ajax for send user guide
     */
    public function send_user_guide()
    {
        $to = '';
        $array = '';

        $to = $_POST['recipient'];

        // send email
        $this->creare_emails->build_email('user-guide', $to, $array);

        die();
    }
}