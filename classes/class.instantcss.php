<?php
if (!defined('ABSPATH')) {
    die('Access denied.');
}

class InstantCSS
{
    /**
     * InstantCSS constructor.
     */
    public function __construct()
    {
        // Admin actions
        add_action('admin_menu',  array($this, 'icss_create_admin_menu'));
        add_action('admin_menu', array($this, 'icss_create_admin_sub_menu'));
        new InstantCSS_Ajax();

        // Options
        add_option('icss_css', '', false, true);
        add_option('icss_version', '', false, true);
        add_option('icss_postcss', '', false, true);
        add_option('icss_lang', 'css', false, true);
        add_option('icss_theme', 'vs', false, true);
        add_option('icss_preprocessor', 'css', false, true);
        add_option('icss_minify', 'off', false, true);

        // Add saved option to script tag
        add_action('init', array($this, 'icss_get_css'));
    }

    /**
     * Adds menu page to WordPress admin panel
     */
    public function icss_create_admin_menu()
    {
        global $instantcss_page;
        $instantcss_page = add_menu_page(
            'Instant CSS',
            'Instant CSS',
            'manage_options',
            'instantcss',
            array($this, 'icss_show_admin_menu'),
            'data:image/svg+xml;base64,' . base64_encode('<svg aria-hidden="true" focusable="false" data-prefix="fas" data-icon="code" class="svg-inline--fa fa-code fa-w-20" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 512"><path fill="#89A1A6" d="M278.9 511.5l-61-17.7c-6.4-1.8-10-8.5-8.2-14.9L346.2 8.7c1.8-6.4 8.5-10 14.9-8.2l61 17.7c6.4 1.8 10 8.5 8.2 14.9L293.8 503.3c-1.9 6.4-8.5 10.1-14.9 8.2zm-114-112.2l43.5-46.4c4.6-4.9 4.3-12.7-.8-17.2L117 256l90.6-79.7c5.1-4.5 5.5-12.3.8-17.2l-43.5-46.4c-4.5-4.8-12.1-5.1-17-.5L3.8 247.2c-5.1 4.7-5.1 12.8 0 17.5l144.1 135.1c4.9 4.6 12.5 4.4 17-.5zm327.2.6l144.1-135.1c5.1-4.7 5.1-12.8 0-17.5L492.1 112.1c-4.8-4.5-12.4-4.3-17 .5L431.6 159c-4.6 4.9-4.3 12.7.8 17.2L523 256l-90.6 79.7c-5.1 4.5-5.5 12.3-.8 17.2l43.5 46.4c4.5 4.9 12.1 5.1 17 .6z"></path></svg>')
        );

        add_action('load-' . $instantcss_page, array($this, 'icss_plugin_page'));
    }

    /**
     * Adds submenu pages to the WordPress admin panel
     */
    public function icss_create_admin_sub_menu()
    {
        global $customizer;

        add_submenu_page(
            'instantcss',
            'Instant CSS',
            'Instant CSS',
            'manage_options',
            'instantcss'
        );

        $customizer = add_submenu_page(
            'instantcss',
            'Live editor',
            'Live editor',
            'manage_options',
            'instantcss-customizer',
            array($this, 'icss_show_customizer_page')
        );

        add_action('load-' . $customizer, array($this, 'icss_customizer_page'));
    }

    /**
     * Get the template file for the admin page
     */
    public function icss_show_admin_menu()
    {
        return $this->icss_get_template('editor.php');
    }


    /**
     * Get the template file for the customizer page
     */
    public function icss_show_customizer_page()
    {
        return $this->icss_get_template('customizer.php');
    }

    /**
     * Enqueue all needed dependencies
     */
    public function icss_admin_dependencies()
    {
        wp_enqueue_style('icss-styles', plugins_url('assets/css/style.css', dirname(__FILE__)), array(), ICSS_VERSION);
        wp_enqueue_script('monaco-editor', plugins_url('assets/dist/main.bundle.js', dirname(__FILE__)), array(), ICSS_VERSION, true);
        wp_localize_script('monaco-editor', 'wordpress', array(
            'plugins_url' => plugins_url('/', dirname(__FILE__)),
            'ajax_url' => admin_url('admin-ajax.php'),
            'is_customizer' => 'inactive',
            'nonce' => wp_create_nonce('icss_nonce')
        ));
    }

    public function icss_customizer_dependencies()
    {
        wp_localize_script('monaco-editor', 'wordpress', array(
            'plugins_url' => plugins_url('/', dirname(__FILE__)),
            'ajax_url' => admin_url('admin-ajax.php'),
            'is_customizer' => 'active'
        ));

        wp_enqueue_style('icss-customizer', plugins_url('assets/css/customizer.css', dirname(__FILE__)), array(), ICSS_VERSION);
    }

    /**
     * Get values from the database using the Options API
     */
    public function icss_get_css()
    {
        $savedCSS = get_option('icss_postcss');
        $styles = stripslashes($savedCSS);

        if (isset($styles)) {
            $cssFile = fopen(dirname(__DIR__) . '/public/custom.css', "w");
            if (isset($cssFile)) {
                if (fwrite($cssFile, $styles))
                    add_action('wp_enqueue_scripts', array($this, 'icss_enqueue_css'));
            } else {
                echo '<style type="text/css" id="instant-css">' . $styles . '</style>';
            }
        }
    }

    /*
	 * Enqueues the custom css file created by the user
	 */
    public function icss_enqueue_css()
    {
        $version = get_option('icss_version');
        if (isset($version))
            wp_enqueue_style('icss-custom-styles', plugins_url('public/custom.css', dirname(__FILE__)), array(), $version);
    }

    /**
     * Conditional scripts enqueuing
     */
    public function icss_plugin_page()
    {
        global $instantcss_page;
        $screen = get_current_screen();

        /*
		 * Check if current screen is instantcss page
		 */
        if ($screen->id != $instantcss_page)
            return;

        add_action('admin_enqueue_scripts', array($this, 'icss_admin_dependencies'));
    }

    /**
     * Conditional scripts enqueuing
     */
    public function icss_customizer_page()
    {
        global $customizer;
        $screen = get_current_screen();

        /*
		 * Check if current screen is instantcss page
		 */
        if ($screen->id !== $customizer)
            return;

        add_action('admin_enqueue_scripts', array($this, 'icss_admin_dependencies'));
        add_action('admin_enqueue_scripts', array($this, 'icss_customizer_dependencies'));
    }

    /**
     * @param $template_name
     */
    private function icss_get_template($template_name)
    {
        $template_file = $this->icss_locate_template($template_name);

        if (!file_exists($template_file)) :
            _doing_it_wrong(__FUNCTION__, sprintf('<code>%s</code> does not exist.', $template_file), '1.0.0');
            return;
        endif;

        include $template_file;
    }

    /**
     * @param $template_name
     * @return mixed|void
     * Locate template file in templates folder
     */
    private function icss_locate_template($template_name)
    {
        $default_path = plugin_dir_path(__DIR__) . 'views/'; // Path to the template folder

        // Search template file in theme folder.
        $template = locate_template(array(
            $template_name
        ));

        // Get plugins template file.
        if (!$template) :
            $template = $default_path . $template_name;
        endif;

        return apply_filters('locate_template', $template, $template_name, $default_path);
    }
}
