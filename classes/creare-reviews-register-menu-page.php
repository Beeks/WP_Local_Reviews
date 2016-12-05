<?php

class Creare_Reviews_Register_Menu
{
    public function __construct()
    {
        // add action to create menu page
        add_action('admin_menu', array($this, 'register_menu_page'));
    }

    /*
	 * Add menu page
	 */
    public function register_menu_page()
    {
        // admin page
        $page_title = "Creare Local Reviews Settings";
        $menu_title = "Creare Plugins";
        $capability = "manage_options";
        $menu_slug = "creare-plugins";

        if (empty ($GLOBALS['admin_page_hooks']['creare-plugins'])) :

            $page = add_menu_page(
                $page_title,
                $menu_title,
                $capability,
                $menu_slug,
                array($this, 'about_creare_page'),
                'data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0idXRmLTgiPz48c3ZnIHZlcnNpb249IjEuMSIgaWQ9IkxheWVyXzEiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyIgeG1sbnM6eGxpbms9Imh0dHA6Ly93d3cudzMub3JnLzE5OTkveGxpbmsiIHg9IjBweCIgeT0iMHB4IiB2aWV3Qm94PSIwIDAgNTA0LjMgNTQ2LjEiIGVuYWJsZS1iYWNrZ3JvdW5kPSJuZXcgMCAwIDUwNC4zIDU0Ni4xIiB4bWw6c3BhY2U9InByZXNlcnZlIj48Zz48cGF0aCBmaWxsPSIjRkZGRkZGIiBkPSJNMjM3LjcsNDA5LjhjLTUwLjcsMC05MS41LTQwLjgtOTEuNS05MC41YzAtNTAuNyw0MC44LTkxLjUsOTEuNS05MS41YzIzLjksMCw0NS44LDksNjEuNywyNC45Yy0yMi45LTU2LjctNzguNi05Ni41LTE0NC4yLTk2LjVDNjkuNiwxNTYuMiwwLDIyNS44LDAsMzExLjNzNjkuNiwxNTUuMiwxNTUuMiwxNTUuMmM1NC43LDAsMTAyLjUtMjcuOSwxMzAuMy02OS42QzI3MS41LDQwNC44LDI1NS42LDQwOS44LDIzNy43LDQwOS44eiIvPjxwYXRoIGZpbGw9IiNGRkZGRkYiIGQ9Ik0yMzAuOCwwQzEzMy4zLDAsNDguNyw1MC43LDAsMTI3LjNjMzkuOC00MS44LDk2LjUtNjguNiwxNTkuMS02OC42YzEyMS4zLDAsMjE5LjgsOTguNSwyMTkuOCwyMTkuOHMtOTguNSwyMTkuOC0yMTkuOCwyMTkuOGMtNDkuNywwLTk1LjUtMTUuOS0xMzIuMy00My44YzQ5LjcsNTUuNywxMjIuMyw5MS41LDIwMy45LDkxLjVjMTUxLjIsMCwyNzMuNS0xMjEuMywyNzMuNS0yNzIuNVMzODIsMCwyMzAuOCwweiIvPjwvZz48L3N2Zz4=',
                990
            );

            // about creare page
            add_submenu_page(
                $menu_slug,
                $page_title,
                'Creare Plugins',
                $capability,
                $menu_slug,
                array($this, 'about_creare_page')
            );

        endif;

        // main settings page
        $settings_page = add_submenu_page(
            $menu_slug,
            'Creare Local Reviews',
            'Creare Local Reviews',
            $capability,
            $menu_slug . '-clr-settings',
            array($this, 'clr_settings_page')
        );
        add_action("load-{$settings_page}", array($this, 'clr_load_settings_page'));

    }

    /*
     * Load settings page
     */
    function clr_load_settings_page()
    {
        if (isset($_POST["clr-settings-submit"]) == 'Y') {
            check_admin_referer("clr-settings-page");
            $this->clr_save_theme_settings();
            $url_parameters = isset($_GET['tab']) ? 'updated=true&tab=' . $_GET['tab'] : 'updated=true';
            wp_redirect(admin_url('admin.php?page=creare-plugins-clr-settings&' . $url_parameters));
            exit;
        }
    }

    /*
     * Save settings
     */
    function clr_save_theme_settings()
    {
        global $pagenow;

        $settings = get_option("clr_settings");

        if ($pagenow == 'admin.php' && $_GET['page'] == 'creare-plugins-clr-settings') {
            if (isset ($_GET['tab']))
                $tab = $_GET['tab'];
            else
                $tab = 'settings';

            switch ($tab) {
                case 'help' :
                    break;
                case 'settings' :
                    $templates_array = array();
                    $templates = '';

                    foreach( $_POST['clr_pagetemplates'] as $template ) {
                        $templates_array[] = $template;
                    }

                    $settings['clr_facebook'] = $_POST['clr_facebook'];
                    $settings['clr_googleplus'] = $_POST['clr_googleplus'];
                    $settings['clr_yelp'] = $_POST['clr_yelp'];
                    $settings['clr_tripadvisor'] = $_POST['clr_tripadvisor'];
                    $settings['clr_enablereviewpages'] = $_POST['clr_enablereviewpages'];
                    $settings['clr_enablestyling'] = $_POST['clr_enablestyling'];
                    $settings['clr_pagetemplates'] = $_POST['clr_pagetemplates'];
                    $settings['clr_yell'] = $_POST['clr_yell'];
                    $settings['clr_scoot'] = $_POST['clr_scoot'];
                    break;
            }
        }

        $updated = update_option('clr_settings', $settings);
    }

    /*
     * Set up tabs
     */
    function clr_admin_tabs($current = 'settings')
    {
        $tabs = array('settings' => 'Settings', 'help' => 'Help');
        $links = array();
        echo '<div id="icon-themes" class="icon32"><br></div>';
        echo '<h2 class="nav-tab-wrapper">';
        foreach ($tabs as $tab => $name) {
            $class = ($tab == $current) ? ' nav-tab-active' : '';
            echo "<a class='nav-tab$class' href='?page=creare-plugins-clr-settings&tab=$tab'>$name</a>";

        }
        echo '</h2>';
    }

    /*
     * Settings page
     */
    function clr_settings_page()
    {
        global $pagenow;
        $settings = get_option("clr_theme_settings");
        ?>

        <div class="wrap">
            <h2>Creare Local Reviews Settings</h2>

            <?php
            if ('true' == esc_attr(isset($_GET['updated']))) echo '<div class="updated" ><p>Settings updated.</p></div>';

            if (isset ($_GET['tab'])) $this->clr_admin_tabs($_GET['tab']); else $this->clr_admin_tabs('settings');
            ?>

            <div id="poststuff">
                <form method="post" action="<?php admin_url('admin.php?page=creare-plugins-clr-settings'); ?>">
                    <?php
                    wp_nonce_field("clr-settings-page");

                    if ($pagenow == 'admin.php' && $_GET['page'] == 'creare-plugins-clr-settings') {

                        if (isset ($_GET['tab'])) $tab = $_GET['tab'];
                        else $tab = 'settings';

                        echo '<table class="form-table">';
                        switch ($tab) {
                            case 'help' :

                                // page content
                                include(plugin_dir_path(__FILE__) . 'templates/admin/creare-local-reviews-help-tab.php');

                                break;
                            case 'settings' :

                                // page content
                                include(plugin_dir_path(__FILE__) . 'templates/admin/creare-local-reviews-settings-tab.php');

                                break;
                        }
                        echo '</table>';
                    }
                    ?>
                </form>

                <?php include(plugin_dir_path(__FILE__) . '/templates/admin/creare-footer.php'); ?>
            </div>

        </div>
    <?php
    }

    /*
     * Settings page content
     */
    public function settings_menu_page()
    {
        // page content
        include(plugin_dir_path(__FILE__) . '/templates/admin/creare-local-reviews-settings.php');
    }

    /*
     * About Creare page content
     */
    public function about_creare_page()
    {
        // page content
        include(plugin_dir_path(__FILE__) . '/templates/admin/about-creare.php');
    }
}