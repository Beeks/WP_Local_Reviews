<?php

class Creare_Reviews_Emails_Frontend
{

    /**
     * Construct the plugin object
     */
    public function __construct()
    {
        // do something
    }

    public function build_content()
    {
        $current_user = '';
        $current_user = wp_get_current_user();

        return '<h3>ACTIVATION</h3>
<p class="lead">Someone has activated the Local Reviews Plugin.</p>
<p><strong>IP:</strong> ' . $_SERVER['REMOTE_ADDR'] . '<br />
<strong>Installed by:</strong> ' . $current_user->user_login . ', ' . $current_user->user_email . '<br />
<strong>Website:</strong> ' . get_bloginfo('url') . '
</p>

';
    }
}