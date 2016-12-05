<?php

require_once('creare-reviews-shortcode.php');

class Creare_Reviews_Metaboxes
{

    // scope variables for use within class
    protected $creare_reviews_shortcode;

    /**
     * Construct the plugin object
     */
    public function __construct()
    {
        // init hook
        add_action('init', array($this, 'init'));

        // admin init hook
        add_action('admin_init', array($this, 'admin_init'));

        // new post & edit post hook for js & css
        add_action('admin_print_scripts-post-new.php', array($this, 'admin_enqueue_scripts'), 11);
        add_action('admin_print_scripts-post.php', array($this, 'admin_enqueue_scripts'), 11);

        // instantiate frontend shortcode
        $this->creare_reviews_shortcode = new Creare_Reviews_Shortcode();

    }

    /**
     * Admin Enqueue Scripts
     */
    public function admin_enqueue_scripts()
    {
        // global post type we're on in admin & only enqueue datepicker for our cpt
        global $post_type;
        if ($post_type == 'clr_reviews') {

            // register & enqueue custom admin css for reviews
            wp_register_style(
                'clr-reviews-styles',
                plugins_url('/css/admin/reviews-metaboxes.css', dirname(__FILE__)),
                '',
                null
            );
            wp_enqueue_style('clr-reviews-styles');

            // register & enqueue star rating css
            wp_register_style(
                'clr-star-rating-styles',
                plugins_url('/css/star-rating.css', dirname(__FILE__)),
                '',
                null
            );
            wp_enqueue_style('clr-star-rating-styles');

            // register & enqueue custom admin js for reviews
            wp_register_script(
                'clr-reviews-script',
                plugins_url('/js/admin/reviews-metaboxes.js', dirname(__FILE__)),
                '',
                '',
                true
            );
            wp_enqueue_script('clr-reviews-script');

            // register & enqueue star rating script
            wp_register_script(
                'clr-star-rating-script',
                plugins_url('/js/star-rating.js', dirname(__FILE__)),
                '',
                '',
                true
            );

            // enqueue star rating js if admin role
            if (current_user_can('activate_plugins')) :
                wp_enqueue_script('clr-star-rating-script');
            endif;

        }
    }

    /**
     * Initialise function
     */
    public function init()
    {
        // save post hook
        add_action('save_post', array($this, 'save_post'));

        // remove post type support for title & editor if editor role
        if (!current_user_can('activate_plugins')) :
            remove_post_type_support('clr_reviews', 'title');
            remove_post_type_support('clr_reviews', 'editor');
        endif;
    }

    /**
     * Save the metaboxes for this custom post type
     */
    public function save_post($post_id)
    {
        // Checks save status
        $is_autosave = wp_is_post_autosave($post_id);
        $is_revision = wp_is_post_revision($post_id);
        $is_valid_nonce = (isset($_POST['clr_nonce']) && wp_verify_nonce($_POST['clr_nonce'], basename(__FILE__))) ? 'true' : 'false';

        // Exits script depending on save status
        if ($is_autosave || $is_revision || !$is_valid_nonce) {
            return;
        }

        // Checks for input and sanitizes/saves if needed
        if (isset($_POST['meta-author'])) {
            update_post_meta($post_id, 'meta-author', sanitize_text_field($_POST['meta-author']));
        }

        // Checks for input and sanitizes/saves if needed
        if (isset($_POST['meta-hyperlocal'])) {
            update_post_meta($post_id, 'meta-hyperlocal', sanitize_text_field($_POST['meta-hyperlocal']));
        }

        // Checks for input and sanitizes/saves if needed
        if (isset($_POST['meta-date'])) {
            update_post_meta($post_id, 'meta-date', sanitize_text_field($_POST['meta-date']));
        }

        // Checks for input and saves if needed
        if (isset($_POST['meta-radio'])) {
            update_post_meta($post_id, 'meta-radio', $_POST['meta-radio']);
        }

    } // END public function save_post($post_id)

    /**
     * hook into WP's admin_init action hook
     */
    public function admin_init()
    {
        add_action('add_meta_boxes', array($this, 'add_meta_boxes'));
    }

    /**
     * Add meta box function
     */
    public function add_meta_boxes($post_type)
    {
        // set our cpt to array
        $post_types = array('clr_reviews');

        // check we're in our cpt before adding meta boxes
        if (in_array($post_type, $post_types)) {
            add_meta_box(
                'clr_metabox',                  // Unique ID
                'Review Information',           // Box title
                array($this, 'metabox_form'),  // Content callback
                $post_type
            );
        }
    }

    /*
     * Get the review content
     */
    public function get_review_content($id)
    {
        $content = '';
        $content = get_post($id);
        return $content->post_content;
    }

    /**
     * Add meta box html
     */
    public function metabox_form($post)
    {
        $checked = '';
        $html = '';
        $disabled = '';
        $hidden = true;

        // Disable fields for editor role
        if (!current_user_can('activate_plugins')) :
            $disabled = 'disabled="disabled"';
            $hidden = false;
        endif;

        // get star rating meta
        $checked = get_post_meta(get_the_ID(), 'meta-radio', true);

        // add our wp nonce for security
        wp_nonce_field(basename(__FILE__), 'clr_nonce');

        // define $clr_stored_meta to access all saved post meta
        $clr_stored_meta = get_post_meta($post->ID);
        ?>

        <?php if ($hidden == false) : ?>
        <div class="field-holder">
            <div class="field-label">
                <label for="meta-title" class="clr-row-title"><?php _e('Review Title', 'clr-textdomain') ?></label>
            </div>
            <div class="field-input">
                <input type="text" disabled="disabled" name="meta-title" id="meta-title" value="<?php the_title(); ?>"/>
            </div>
        </div>

        <div class="field-holder">
            <div class="field-label">
                <label for="meta-content"
                       class="clr-row-content"><?php _e('Review Content', 'clr-textdomain') ?></label>
            </div>
            <div class="field-input">
                <textarea disabled="disabled" name="meta-content"
                          id="meta-content"><?php echo $this->get_review_content(get_the_ID()); ?></textarea>
            </div>
        </div>
    <?php endif; ?>

        <div class="field-holder">
            <div class="field-label">
                <label for="meta-author" class="clr-row-title"><?php _e('Author', 'clr-textdomain')?></label>
            </div>
            <div class="field-input">
                <input type="text" <?php echo $disabled; ?> name="meta-author" id="meta-author"
                       value="<?php if (isset ($clr_stored_meta['meta-author'])) echo $clr_stored_meta['meta-author'][0]; ?>"/>
            </div>
        </div>

        <div class="field-holder">
            <div class="field-label">
                <label for="meta-date" class="clr-row-title"><?php _e('Town/Village', 'clr-textdomain')?></label>
            </div>
            <div class="field-input">
                <input type="text" <?php echo $disabled; ?> name="meta-hyperlocal" id="meta-hyperlocal"
                       value="<?php if (isset ($clr_stored_meta['meta-hyperlocal'])) echo $clr_stored_meta['meta-hyperlocal'][0]; ?>"/>
            </div>
        </div>

        <div class="field-holder">
            <div class="field-label"><strong class="rating-choice"><?php _e('Rating', 'clr-textdomain')?></strong></div>
            <div class="field-input">

                <?php if ($hidden == false) : ?>

                    <?php
                    // get star rating
                    $html = $this->creare_reviews_shortcode->get_star_rating($checked);
                    echo $html;
                    ?>

                <?php else : ?>

                    <ul class="star-rating">
                        <li id="star-1">
                            <input type="radio" name="meta-radio" data-rating="1" id="meta-radio-one"
                                   value="radio-one" <?php if (isset ($clr_stored_meta['meta-radio'])) checked($clr_stored_meta['meta-radio'][0], 'radio-one'); ?> /><i></i>
                        </li>
                        <li id="star-2">
                            <input type="radio" name="meta-radio" data-rating="2" id="meta-radio-two"
                                   value="radio-two" <?php if (isset ($clr_stored_meta['meta-radio'])) checked($clr_stored_meta['meta-radio'][0], 'radio-two'); ?> /><i></i>
                        </li>
                        <li id="star-3">
                            <input type="radio" name="meta-radio" data-rating="3" id="meta-radio-three"
                                   value="radio-three" <?php if (isset ($clr_stored_meta['meta-radio'])) checked($clr_stored_meta['meta-radio'][0], 'radio-three'); ?> /><i></i>
                        </li>
                        <li id="star-4">
                            <input type="radio" name="meta-radio" data-rating="4" id="meta-radio-four"
                                   value="radio-four" <?php if (isset    ($clr_stored_meta['meta-radio'])) checked($clr_stored_meta['meta-radio'][0], 'radio-four'); ?> /><i></i>
                        </li>
                        <li id="star-5">
                            <input type="radio" name="meta-radio" data-rating="5" id="meta-radio-five"
                                   value="radio-five" <?php if (isset    ($clr_stored_meta['meta-radio'])) checked($clr_stored_meta['meta-radio'][0], 'radio-five'); ?> /><i></i>
                        </li>
                    </ul>

                <?php endif; ?>

            </div>
        </div>

    <?php

    }

}