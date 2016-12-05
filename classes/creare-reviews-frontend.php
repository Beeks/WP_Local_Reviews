<?php

class Creare_Reviews_Frontend
{
    protected $creare_emails;
    protected $page_id;

    /**
     * Construct the plugin object
     */
    public function __construct()
    {
        // add shortcode for frontend review form
        add_shortcode('leave-a-review-form', array($this, 'review_form'));

        // dashboard hook for js & css
        add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'), 100);

        // add ajax Actions
        add_action('wp_ajax_post_review', array($this, 'post_review'));
        add_action('wp_ajax_nopriv_post_review', array($this, 'post_review'));

        // redirect single reviews page if accessed
        add_action('template_redirect', array($this, 'redirect_reviews'));

        // add meta robots & noindex to reviews archive page
        add_action('wp_head', array($this, 'noindex_review_archive'));

        // require email structure class file
        require_once('creare-reviews-emails.php');
        $this->creare_emails = new Creare_Reviews_Emails();
    }

    /*
     * Enqueue Scripts
     */
    public function enqueue_scripts()
    {
        // enqueue custom js for posting review
        // change to minified once completed
        wp_register_script(
            'clr-reviews-frontend-script',
            plugins_url('/js/reviews-frontend.js', dirname(__FILE__)),
            array('jquery'),
            null
        );

        // pass through ajax_url as object
        wp_localize_script(
            'clr-reviews-frontend-script',
            'ajax_post_review',
            array('ajax_url' => admin_url('admin-ajax.php'))
        );

        // register frontend styles
        wp_register_style(
            'reviews-frontend-css',
            plugins_url('css/reviews-frontend.css', dirname(__FILE__)),
            '',
            null
        );

        // register styles
        wp_register_style(
            'frontend-preloader-css',
            plugins_url('css/preloader.css', dirname(__FILE__)),
            '',
            null
        );

        // star rating css
        wp_register_style(
            'star-rating-css',
            plugins_url('/css/star-rating.css', dirname(__FILE__)),
            '',
            null
        );

        wp_enqueue_style('star-rating-css');

        // star rating script
        wp_enqueue_script(
            'clr-star-rating-script',
            plugins_url('/js/star-rating.js', dirname(__FILE__)),
            array('jquery'),
            null,
            true
        );

        // only bring through styles on this page
        $page = get_page_by_title('Leave a review');

        // check page exists and then show
        if ($page && is_page($page->ID)) {

            // enqueue frontend styles
            wp_enqueue_style('reviews-frontend-css');
            wp_enqueue_style('frontend-preloader-css');
            wp_enqueue_script('clr-reviews-frontend-script');
        }

    }

    /*
     * Send email to site admin when review is submitted for review
     */
    public function send_email_prompt($post_id)
    {
        $array = array(
            'url' => get_bloginfo('url'),
            'post_id' => $post_id
        );

        $to = get_bloginfo('admin_email');

        // send email
        $this->creare_emails->build_email('frontend-admin-email', $to, $array);

    }

    /*
     * Insert review into DB / Site
     */
    public function post_review()
    {
        global $wpdb;

        // set table name
        $wpdb->clr_reviews = $wpdb->prefix . 'clr_reviews';

        // set empty variables
        $c = '';
        $social_links = '';
        $service_checkboxes = array();
        $row_id = '';

        // check nonce is valid & posted
        $is_valid_nonce = (isset($_POST['clr_nonce']) && wp_verify_nonce($_POST['clr_nonce'], basename(__FILE__))) ? 'true' : 'false';

        // Exits script depending on save status
        if (!$is_valid_nonce) {
            return;
        }

        $post = array(
            'post_content' => $_POST['review_content'],
            'post_title' => $_POST['review_title'],
            'post_status' => 'draft',
            'post_type' => 'clr_reviews',
            'ping_status' => 'closed'
        );

        try {

            if(isset($_POST['review_service'])) {
                // loop through post data and set to array
                foreach ($_POST['review_service'] as $checkbox) {
                    $service_checkboxes[] = $checkbox;
                }
            }

            // insert post into DB & grab post id for later
            $post_id = wp_insert_post($post);

            // update post meta using post id
            if ($_POST['review_author']) {
                update_post_meta($post_id, 'meta-author', $_POST['review_author']);
            }
            if ($_POST['review_radio']) {
                update_post_meta($post_id, 'meta-radio', $_POST['review_radio']);
            }
            if ($_POST['review_hyperlocal']) {
                update_post_meta($post_id, 'meta-hyperlocal', $_POST['review_hyperlocal']);
            }

            // update post terms using post id
            if (isset($_POST['review_area'])) {
                wp_set_object_terms($post_id, $_POST['review_area'], 'area');
            }
            if (isset($_POST['review_service'])) {
                wp_set_object_terms($post_id, $service_checkboxes, 'service');
            }

            // set review_radio post data to variable
            $rating = $_POST['review_radio'];

            // get row id if one is set
            if( isset( $_POST['row_id'] ) ) {
                $row_id = $_POST['row_id'];

                // update database row to complete
                $wpdb->update( 
                    $wpdb->clr_reviews,
                        array(
                            'complete' => '1'
                        ),
                        array(
                            'id' => $row_id
                        ),
                        array(
                            '%d'
                        ),
                        array(
                            '%d'
                        )
                );
            }

            // set iterator to 0
            $c = 0;

            // Get array of options
            $settings = get_option("clr_settings");

            // get social media links
            if ($settings['clr_facebook'] != '') {
                $social_links .= '
				<li>
				<a onclick="ga( \'send\', \'click\', \'CLR Social button - Facebook\' );" target="_blank" class="facebook-icon" href="' . $settings['clr_facebook'] . '">
					<img src="' . plugins_url('images/facebook-icon.svg', dirname(__FILE__)) . '" alt="Facebook Icon" />
					<span>Facebook</span>
				</a>
				</li>';
                $c++;
            }
            if ($settings['clr_googleplus'] != '') {
                $social_links .= '
				<li>
				<a onclick="ga( \'send\', \'click\', \'CLR Social button - Google Plus\' );" target="_blank" class="googleplus-icon" href="' . $settings['clr_googleplus'] . '">
					<img src="' . plugins_url('images/googleplus-icon.svg', dirname(__FILE__)) . '" alt="Google+ Icon" />
					<span>Google+</span>
				</a>
				</li>';
                $c++;
            }
            if ($settings['clr_yelp'] != '') {
                $social_links .= '
				<li>
				<a onclick="ga( \'send\', \'click\', \'CLR Social button - Yelp\' );" target="_blank" class="yelp-icon" href="' . $settings['clr_yelp'] . '">
					<img src="' . plugins_url('images/yelp-icon.svg', dirname(__FILE__)) . '" alt="Yelp Icon" />
					<span>Yelp</span>
				</a>
				</li>';
                $c++;
            }
            if ($settings['clr_tripadvisor'] != '') {
                $social_links .= '
				<li>
				<a onclick="ga( \'send\', \'click\', \'CLR Social button - Tripadvisor\' );" target="_blank" class="tripadvisor-icon" href="' . $settings['clr_tripadvisor'] . '">
					<img src="' . plugins_url('images/tripadvisor-icon.svg', dirname(__FILE__)) . '" alt="Tripadvisor Icon" />
					<span>Tripadvisor</span>
				</a>
				</li>';
                $c++;
            }

            // check to see if review was above 2 stars
            if ($rating == 'radio-one' || $rating == 'radio-two') {
                $review_text = '';
            } else {

                if ($c != 0) {

                    // set review text
                    $review_text = '<p>If you could spare a few minutes to leave a review on the following sites, we\'d be grateful!</p>';
                    $review_text .= '<ul class="social-sites social-4">' . $social_links . '</ul>';

                } else {

                    $review_text = '';
                }
            }

            // set array to value to return to js file
            $array = array(
                'thankyou' => '<p>Thank you for writing a review for us! A member of our team has been notified and will publish it soon.</p>',
                'review_again' => $review_text
            );

            // return as json object
            echo json_encode($array);

            $this->send_email_prompt($post_id);

        } catch (Exception $e) {

            echo $e->getMessage();

        }

        // prevent WordPress returning 1 or 0
        die();

    }

    /*
     * Check if areas exists, then show hyperlocal field
     */
    public function hyperlocal_field($area)
    {
        $html = '';

        // set arguments
        $args = array(
            'hide_empty' => false
        );

        // get terms for $tax
        $terms = get_terms($area, $args);

        // if there are terms
        if (!empty($terms)) {
            $html .= '<div class="input-text-wrap">
	    				<label>Add sub-area you provided service in:</label>
	    				<input required="required" name="clr_local" id="clr-local" type="text" placeholder="" />
	    			</div>';
        }

        return $html;

    }

    /*
     * Dynamically populate taxonomy dropdown
     */
    public function taxonomy_dropdown($tax, $prefill = false, $local = false, $label = false)
    {
        // set variable
        $html = '';
        $tax_title = '';

        // swap tax text for default option
        if ($tax == 'area') {
            $tax_title = 'location';
        }

        // set selected option of dropdown
        if ($prefill) {
            $prefill = '<option data-id="' . $local . '" value="' . $prefill . '">' . ucfirst($prefill) . '</option>';
        } else {
            $prefill = '<option value="">Select your ' . $tax_title . '...</option>';
        }

        // set arguments
        $args = array(
            'hide_empty' => false
        );

        // get terms for $tax
        $terms = get_terms($tax, $args);

        // if label is sent through & there are terms
        if ($label && !empty($terms)) {
            $html .= $label;
        }

        // if not empty, loop through & output dropdown
        if (!empty($terms) && !is_wp_error($terms)) {
            $html .= '<select id="review_' . $tax . '" name="review_' . $tax . '" required="required">';
            $html .= $prefill;
            foreach ($terms as $term) {

                // get taxonomy description
                $description = term_description($term->term_id, 'area');

                // if no description, set as blank
                if (!$description || empty($description)) {
                    $description = '';
                }

                // strip html from description
                $description = strip_tags(trim($description));

                // check to see if prefill is same as an option already in the list & don't show
                if ($term->name == strip_tags(trim($prefill))) {
                    // do nothing
                } else {
                    // add option
                    $html .= '<option data-id="' . $description . '" value="' . $term->slug . '">' . $term->name . '</option>';
                }

            }
            $html .= '</select>';
        }

        return $html;
    }

    /*
     * Get taxonomy terms as checkboxes
     */
    public function taxonomy_checkboxes($tax, $services = false, $label = false)
    {

        // set empty variables
        $html = '';
        $checked = '';
        $c = '';
        $required_check = '';

        // set arguments
        $args = array(
            'hide_empty' => false
        );

        // get terms for $tax
        $terms = get_terms($tax, $args);

        // if label is sent through & there are terms
        if ($label && !empty($terms)) {
            $html .= $label;
        }

        // if not empty, loop through & output dropdown
        if (!empty($terms) && !is_wp_error($terms)) {

            $html .= '<div class="service-checkbox-holder">';

            // set iterator to 1
            $c = 1;

            // loop through terms
            foreach ($terms as $term) {

                // check iterator
                if ($c == 1) {
                    //$required_check = 'required="required"';
                    $required_check = '';
                } else {
                    $required_check = '';
                }

                // check if services is set
                if (!empty($services)) {
                    // shorthand to check what input to prefill
                    $checked = (in_array($term->slug, $services) ? ' checked="checked"' : '');
                }

                $html .= '<div class="service-checkbox">';

                $html .= '<input' . $checked . ' ' . $required_check . ' id="' . $term->slug . '_checkbox" type="checkbox" name="review_service[]" value="' . $term->slug . '" />';

                $html .= '<label for="' . $term->slug . '_checkbox">' . $term->name . '</label>';

                $html .= '</div>';

                $c++;

            }

            $html .= '</div>';

        }

        return $html;

    }

    /*
     * Generate review form
     */
    public function review_form()
    {
        global $wpdb;

        // set blank variables
        $author = '';
        $area = '';
        $local = '';
        $service = '';

        // check to see if we should pre-fill form
        if(isset( $_GET['id'] ) ){ 

                // set id to variable
                $id = $_GET['id'];

                // set table name
                $wpdb->clr_reviews = $wpdb->prefix . 'clr_reviews';

                // get row
                $row = $wpdb->get_row( "SELECT * FROM $wpdb->clr_reviews WHERE id = $id" );

                // define variables
                $row_id = $row->id;
                $row_content = $row->review_content;
                $row_content = json_decode($row_content);
                $row_complete = $row->complete;

                if( $row_complete == 0 ) {

                    // set varaiables for pre-fill
                    if (isset($row_content->clr_author)) {
                        $author = $row_content->clr_author;
                    } else {
                        $author = '';
                    }
                    if (isset($row_content->review_area)) {
                        $area = $row_content->review_area;
                    } else {
                        $area = '';
                    }
                    if (isset($row_content->clr_local)) {
                        $local = $row_content->clr_local;
                    } else {
                        $local = '';
                    }
                    if (isset($row_content->review_service)) {
                        $service = $row_content->review_service;
                    } else {
                        $service = '';
                    }
                }
        }

        // html form
        return '

		' . $this->clr_get_heading() . '

		<form action="/" id="post-review-form" method="POST">
		' . wp_nonce_field(basename(__FILE__), 'clr_nonce') . '

			<p>To help us improve our customer service, please write a review below and tell us about your recent experience with us.</p>

			<div class="post-review-holder">

				<div class="star-rating-container">

	            	<ul class="star-rating">
			    		<li id="star-1">
			    			<input type="radio" required="required" name="review_radio" data-rating="1" id="meta-radio-one" value="radio-one" /><i></i>
			    		</li>
			    		<li id="star-2">
			    			<input type="radio" name="review_radio" data-rating="2" id="meta-radio-two" value="radio-two" /><i></i>
			    		</li>
			    		<li id="star-3">
			    			<input type="radio" name="review_radio" data-rating="3" id="meta-radio-three" value="radio-three" /><i></i>
			    		</li>
			    		<li id="star-4">
			    			<input type="radio" name="review_radio" data-rating="4" id="meta-radio-four" value="radio-four" /><i></i>
			    		</li>
			    		<li id="star-5">
			    			<input type="radio" name="review_radio" data-rating="5" id="meta-radio-five" value="radio-five" /><i></i>
			    		</li>
		    		</ul>
		    		<p>Click to rate</p>

	    		</div>
	    		
				<input type="text" required="required" name="review_title" class="" placeholder="Title of your review" />

				<textarea name="review_content" rows="5" required="required" spellcheck="true" placeholder="Your review"></textarea>

				' . $this->taxonomy_dropdown('area', $area, $local) . '

				<input type="text" id="review_hyperlocal" name="review_hyperlocal" class="' . $class . '" placeholder="Area (e.g Brownsover)" value="' . $local . '" />

				' . $this->taxonomy_checkboxes('service', $service, '<p class="form-helper">Tick the services we provided:</p>') . '

				<input type="text" required="required" name="review_author" class="" placeholder="Leave your name" value="' . $author . '" />

				<input class="no-display" type="text" readonly="readonly" name="action" value="post_review" />

                <input type="hidden" value="'.$id.'" name="row_id" />

				<div class="form-submit">
	    		
	    			<input type="submit" name="review-submit" class="form-submit-button" value="Submit your review" />
	    			<div class="preloader"></div>

	    		</div>
			
			</div>
		</form>
		<div class="post-review-response"></div>';
    }

    /*
     * Create leave a review page
     */
    public function create_page()
    {
        $new_page_template = '';
        $new_page_template = 'templates/review-template.php';

        // Create post object
        $page_defaults = array(
            'post_content' => '[leave-a-review-form]',
            'post_name' => 'leave-a-review',
            'post_title' => 'Leave a review',
            'post_status' => 'publish',
            'post_type' => 'page',
            'ping_status' => 'closed'
        );

        $page_check = get_page_by_title('Leave a review');

        // if page does not exist, add our leave a review page & set template
        if (!isset($page_check->ID)) {
            $page_id = wp_insert_post($page_defaults);
            if (!empty($new_page_template)) {
                update_post_meta($page_id, '_wp_page_template', $new_page_template);
            }
        }
    }

    /*
     * Redirect reviews single back to reviews archive
     */
    public function redirect_reviews()
    {

        // get current post type
        $queried_post_type = get_query_var('post_type');

        // check to see if we're accessing the post type
        if (is_single() && 'clr_reviews' == $queried_post_type || 'clr_reviews' == $queried_post_type) {

            // Get array of options
            $settings = get_option("clr_settings");
            
            // check to see if enabled
            if ($settings['clr_enablereviewpages'] != 1) {

                // redirect with 301 status
                wp_redirect(get_site_url(), 301);

                // exit
                exit;

            }


        }
    }

    /*
     * Noindex, nofollow review archive
     */
    public function noindex_review_archive()
    {

        // get current post type
        $queried_post_type = get_query_var('post_type');

        // check to see if we're accessing post type
        if ('clr_reviews' == $queried_post_type) {

            // add meta tag
            echo "<meta name='robots' content='noindex,nofollow' />";
        }
    }


    /*
     * Get page heading or logo
     */
    public function clr_get_heading()
    {
        $html = '';
        $settings = get_option("clr_settings");
        $logo = $settings["business_logo"];

        if (class_exists('mw_business_details') && $logo != '') :

            $logoID = get_option('business_logo_id');
            $logoSrc = wp_get_attachment_image_src($logoID, 'fullsize');

            $html .= '<div id="clr-logo" class="logo"><img width="150px" src="' . $logoSrc[0] . '" alt="' . get_bloginfo("name") . ' Logo"></div>';

        else:

            $html .= '<h1>' . get_bloginfo("name") . '</h1>';

        endif;
        return $html;
    }

}