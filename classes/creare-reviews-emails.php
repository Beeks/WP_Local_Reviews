<?php

// require email templates
require_once('email-templates/email-footer.php');
require_once('email-templates/email-header.php');
require_once('email-templates/dashboard-widget-email.php');
require_once('email-templates/frontend-admin-email.php');
require_once('email-templates/activation-email.php');
require_once('email-templates/user-guide.php');

class Creare_Reviews_Emails
{

    // set $variables
    protected $header;
    protected $footer;
    protected $dashboard_widget_email;
    protected $frontend_admin_email;
    protected $frontend_email;
    protected $user_guide_email;

    /**
     * Construct the plugin object
     */
    public function __construct()
    {

        // change wp_mail format
        add_filter('wp_mail_content_type', array($this, 'set_content_type'));

        // change wp_mail from email address
        add_filter('wp_mail_from', array($this, 'wp_mail_from'));

        // change wp_mail name
        add_filter('wp_mail_from_name', array($this, 'wp_mail_from_name'));

        // set variables to classes
        $this->header = new Creare_Reviews_Emails_Header();
        $this->footer = new Creare_Reviews_Emails_Footer();

        $this->dashboard_widget_email = new Creare_Reviews_Emails_Dashboard();
        $this->frontend_admin_email = new Creare_Reviews_Emails_Frontend_Admin();
        $this->frontend_email = new Creare_Reviews_Emails_Frontend();
        $this->user_guide_email = new Creare_Reviews_Emails_User_Guide();
    }

    /**
     * Change wp_mail name
     */
    public function wp_mail_from($email)
    {
        $url = get_bloginfo('url');
        $url = str_replace(array('http://', 'https://', 'www.'), '', $url);
        return 'noreply@' . $url;
    }

    /**
     * Change wp_mail from email address
     */
    public function wp_mail_from_name($from_name)
    {
        // get blog name
        $company_name = get_bloginfo('name');

        // return in email as name
        return $company_name;
    }

    /**
     * Change content type to html
     */
    public function set_content_type($content_type)
    {
        return 'text/html';
    }

    /**
     * Build email header
     */
    public function email_header()
    {
        // Build email and return
        return $this->header->build_header();
    }

    /**
     * Build email content
     */
    public function email_content($template, $data)
    {
        // content
        switch ($template) :
            case "dashboard-widget-email":
                $content = $this->dashboard_widget_email->build_content($data);
                break;
            case "frontend-admin-email":
                $content = $this->frontend_admin_email->build_content($data);
                break;
            case "activation-email":
                $content = $this->frontend_email->build_content();
                break;
            case "user-guide":
                $content = $this->user_guide_email->build_content();
                break;
        endswitch;

        return $content;
    }

    /**
     * Get email footer
     */
    public function email_footer()
    {
        // Build email and return
        return $this->footer->build_footer();
    }

    /**
     * Set email subject
     */
    public function set_subject($template)
    {
        switch ($template) :
            case "dashboard-widget-email":
                $subject = 'Would you mind leaving us a review?';
                break;
            case "frontend-admin-email":
                $subject = 'You have a new review pending.';
                break;
            case "activation-email":
                $subject = 'Thank you for installing the Creare Local Reviews plugin';
                break;
            case "user-guide":
                $subject = 'Creare Local Reviews plugin - User Guide';
                break;
        endswitch;

        return $subject;
    }

    /**
     * Send email
     */
    public function send_email($to, $subject, $message, $attachments)
    {
        $headers = '';

        // send html email
        wp_mail($to, $subject, $message, $headers, $attachments);

        // remove html email filter to prevent plugin conflicts
        remove_filter('wp_mail_content_type', array($this, 'set_content_type'));
    }

    /**
     * Build email & send
     */
    public function build_email($template, $to, $data)
    {
        $message = '';
        $attachments = '';

        // set subject based on $template
        $subject = $this->set_subject($template);

        // build header
        $message .= $this->email_header();

        // build content
        $message .= $this->email_content($template, $data);

        // build footer
        $message .= $this->email_footer();

        if ($template == 'user-guide') {
            $attachments = CLR_PLUGIN_URL . 'user-guide/user-guide.pdf';
        } else {
            $attachments = '';
        }

        // send html email
        $this->send_email($to, $subject, $message, $attachments);

    }

    /*
     * Send activation email
     */
    public function send_activation_email()
    {

        // set to
        //$to = get_bloginfo( 'admin_email' );
        $to = 'dan@creare.co.uk';

        // send email
        $this->build_email('activation-email', $to);

    }

}