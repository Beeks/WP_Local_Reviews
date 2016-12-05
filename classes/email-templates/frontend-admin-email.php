<?php

class Creare_Reviews_Emails_Frontend_Admin
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
        $url = $data['url'];
        $post_id = $data['post_id'];

        return '<h3>You have a new review pending!</h3>
<p>Hello,<br/> A customer has recently left a review on your website which needs your attention. If you are logged in to the website already, <a href="' . $url . '/wp-admin/post.php?post=' . $post_id . '&action=edit">click on this link to view the review.</a></p>';
    }
}