<?php

class Creare_Reviews_Emails_Dashboard
{

    /**
     * Construct the plugin object
     */
    public function __construct()
    {
        // do something
    }

    public function build_content($data)
    {
        global $wpdb;

        // set table name
        $wpdb->clr_reviews = $wpdb->prefix . 'clr_reviews';

        // get row
        $row = $wpdb->get_row( "SELECT * FROM $wpdb->clr_reviews WHERE id = $data" );

        // define variables
        $row_content = $row->review_content;
        $row_content = json_decode($row_content);

        if( isset( $row_content->clr_author ) ) { 
            $author = $row_content->clr_author;
        } else {
            $author = 'Hello';
        }

        // set url structure
        $url = get_bloginfo('url') . '/leave-a-review/?id=' . $data;

        return '<h3>' . $author . ', would you mind leaving a review on our website?</h3>
<p class="lead">We\'d be very grateful if you could follow the link below and leave a review on our website.</p>
<p>We appreciate honest feedback and are always looking to improve our service level. Leaving a review will take just a couple of minutes, so if you could spare us your time please follow the link below!</p>

<!-- Callout Panel -->

<p class="callout"> <a href="' . $url . '">Click here to leave us a review.</a> </p>
<!-- /Callout Panel --> ';
    }
}