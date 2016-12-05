<?php

class Creare_Reviews_Dashboard_Widget
{
    protected $creare_emails;
    protected $creare_reviews_frontend;

    /**
     * Construct the plugin object
     */
    public function __construct()
    {
        // add dashboard widget action hook
        add_action('wp_dashboard_setup', array($this, 'add_dashboard_widget'));

        // dashboard hook for js & css
        add_action('admin_enqueue_scripts', array($this, 'admin_enqueue_scripts'), 11);

        // add ajax Actions
        add_action('wp_ajax_send_email_data', array($this, 'send_email_data'));

        // require email structure class file
        require_once('creare-reviews-emails.php');
        $this->creare_emails = new Creare_Reviews_Emails();

        // require frontend form
        require_once('creare-reviews-frontend.php');
        $this->creare_reviews_frontend = new Creare_Reviews_Frontend();

    }

    /**
     * Admin Enqueue Scripts
     */
    public function admin_enqueue_scripts($hook)
    {
        // only enqueue scripts & styles if we're on the dashboard
        if ($hook != 'index.php')
            return;

        // register styles for dashboard widget
        wp_register_style(
            'dashboard-widget-css',
            plugins_url('css/admin/dashboard-widget.css', dirname(__FILE__)),
            '',
            null
        );

        // enqueue frontend styles
        wp_enqueue_style('dashboard-widget-css');

        // register styles for preloader
        wp_register_style(
            'dashboard-preloader-css',
            plugins_url('css/preloader.css', dirname(__FILE__)),
            '',
            null
        );

        // enqueue frontend styles
        wp_enqueue_style('dashboard-preloader-css');

        // enqueue custom js for sending review email
        // change to minified once completed
        wp_register_script(
            'clr-dashboard-widget-script',
            plugins_url('/js/admin/dashboard-widget.js', dirname(__FILE__)),
            '',
            null,
            true
        );

        wp_localize_script(
            'clr-dashboard-widget-script',
            'ajax_send_email_data',
            array('ajax_url' => admin_url('admin-ajax.php'))
        );

        wp_enqueue_script('clr-dashboard-widget-script');
    }

    /*
     * Send email to customer
     */
    public function send_email_data()
    {
        global $wpdb;

        // check nonce is valid & posted
        $is_valid_nonce = (isset($_POST['clr_nonce']) && wp_verify_nonce($_POST['clr_nonce'], basename(__FILE__))) ? 'true' : 'false';

        // Exits script depending on save status
        if (!$is_valid_nonce) {
            return;
        }

        // set email to
        $to = $_POST['clr-emailaddress'];

        // set table name
        $table_name = $wpdb->prefix . 'clr_reviews';

        // insert row
        $wpdb->insert(
                        $table_name,
                        array(
                            'review_content' => json_encode($_POST),
                            'complete' => '0'
                        ),
                        array(
                            '%s',
                            '%d'
                        )
                    );

        // get last id inserted to send through in email
        $id = $wpdb->insert_id;

        // send email
        $this->creare_emails->build_email('dashboard-widget-email', $to, $id);

        die();
    }

    /*
     * Add dashboard widget
     */
    public function add_dashboard_widget()
    {
        // only set up widget for admin or editor users
        if (current_user_can('edit_others_pages')) {

            // globalise $wp_meta_boxes
            global $wp_meta_boxes;

            // register widget
            wp_add_dashboard_widget(
                'creare_review_dashboard_widget',
                'Creare Reviews Dashboard Widget',
                array($this, 'dashboard_widget_function')
            );

        }
    }

    /*
     * Dashboard widget content
     */
    public function dashboard_widget_function()
    {
        $html = '';

        // review form
        $html = '
		<form action="" id="send-review-email-form" method="POST">
		' . wp_nonce_field(basename(__FILE__), 'clr_nonce') . '
			<div class="send-email-holder">

				<p>Fill out the form below to send an email prompt to someone so they can leave you a review.</p>

				<div class="input-text-wrap">

					<label name="clr-emailaddress">Email Recipient</label>

	    			<input name="clr-emailaddress" id="clr-emailaddress" required="required" type="email" placeholder="e.g johnmith@domain.co.uk" />

	    		</div>

	    		<div class="select-text-wrap">

	    			' . $this->creare_reviews_frontend->taxonomy_dropdown('area', '', '', '<label>Select area you provided service in:</label>') . '

	    		</div>

	    		' . $this->creare_reviews_frontend->hyperlocal_field('area') . '

	    		<div class="checkbox-text-wrap">

	    			' . $this->creare_reviews_frontend->taxonomy_checkboxes('service', '', '<label>Select service(s) you provided:</label>') . '

	    		</div>

	    		<div class="input-text-wrap">

	    			<label>Customer Name</label>

	    			<input name="clr_author" required="required" id="clr-author" type="text" placeholder="e.g John smith" />

	    		</div>

	    		<input type="hidden" readonly="readonly" name="action" value="send_email_data" />

	    		<div class="submit-text-wrap">

    			<input type="submit" name="send-email" id="send-review-email" class="button button-primary" value="Send email">
    			<div class="preloader"></div>

    			</div>

			</div>
			<div id="message" class="send-email-response updated">
				<p>Your email to <strong><span class="send-email-to"></span></strong> has been successfully sent.</p>
			</div>
		</form>

		<div id="clr-widget-footer">
			<p>
				<span>
					<svg version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
					viewBox="188.2 227.5 214.8 231.7" enable-background="new 188.2 227.5 214.8 231.7" xml:space="preserve"><g><path fill="#FFFFFF" d="M289.6,401.7c-22,0-38.9-16.9-38.9-38.9s16.9-38.9,38.9-38.9c10.1,0,18.6,3.4,27.1,10.1
					c-10.1-23.7-33.8-40.6-60.9-40.6c-37.2,0-66,30.4-66,66s30.4,66,66,66c23.7,0,44-11.8,55.8-30.4
					C303.1,398.3,296.4,401.7,289.6,401.7z"/>
					<path fill="#FFFFFF" d="M286.2,227.5c-42.3,0-77.8,22-98.1,54.1c16.9-18.6,40.6-28.7,67.6-28.7c52.4,0,93,42.3,93,93
					c0,50.7-42.3,93-93,93c-20.3,0-40.6-6.8-55.8-18.6c20.3,23.7,52.4,38.9,86.2,38.9c64.3,0,116.7-50.7,116.7-115
					S350.5,227.5,286.2,227.5z"/></g></svg>
				</span> 
				Powered by <a target="_blank" href="http://www.creare.co.uk">Creare</a></p>
		</div>

    	';

        echo $html;

    }
}