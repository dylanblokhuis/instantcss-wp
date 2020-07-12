<?php

namespace InstantCSS;

use InstantCSS\Helpers\Template;

if (! defined('ABSPATH')) {
    die('Access denied.');
}

class Plugin
{
    /**
     * InstantCSS constructor.
     */
    public function __construct()
    {
        // Admin actions
        add_action('admin_menu', array( $this, 'icss_create_admin_menu' ));
        add_action('admin_menu', array( $this, 'icss_create_admin_sub_menu' ));

        add_action('admin_enqueue_scripts', array( $this, 'icss_admin_dependencies' ));

        new REST();
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
            array( $this, 'icss_show_admin_menu' ),
            'data:image/svg+xml;base64,' . base64_encode('<svg aria-hidden="true" focusable="false" data-prefix="fas" data-icon="code" class="svg-inline--fa fa-code fa-w-20" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 512"><path fill="#89A1A6" d="M278.9 511.5l-61-17.7c-6.4-1.8-10-8.5-8.2-14.9L346.2 8.7c1.8-6.4 8.5-10 14.9-8.2l61 17.7c6.4 1.8 10 8.5 8.2 14.9L293.8 503.3c-1.9 6.4-8.5 10.1-14.9 8.2zm-114-112.2l43.5-46.4c4.6-4.9 4.3-12.7-.8-17.2L117 256l90.6-79.7c5.1-4.5 5.5-12.3.8-17.2l-43.5-46.4c-4.5-4.8-12.1-5.1-17-.5L3.8 247.2c-5.1 4.7-5.1 12.8 0 17.5l144.1 135.1c4.9 4.6 12.5 4.4 17-.5zm327.2.6l144.1-135.1c5.1-4.7 5.1-12.8 0-17.5L492.1 112.1c-4.8-4.5-12.4-4.3-17 .5L431.6 159c-4.6 4.9-4.3 12.7.8 17.2L523 256l-90.6 79.7c-5.1 4.5-5.5 12.3-.8 17.2l43.5 46.4c4.5 4.9 12.1 5.1 17 .6z"></path></svg>')
        );

        // add_action( 'load-'.$instantcss_page, array( $this, 'icss_plugin_page' ) );
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
            array( $this, 'icss_show_customizer_page' )
        );

        add_action('load-'.$customizer, array( $this, 'icss_customizer_page' ));
    }

    /**
     * Get the template file for the admin page
     */
    public function icss_show_admin_menu()
    {
        return Template::icss_get_template('editor.php');
    }


    /**
     * Get the template file for the customizer page
     */
    public function icss_show_customizer_page()
    {
        return Template::icss_get_template('customizer.php');
    }

    /**
     * Enqueue all needed dependencies
     */
    public function icss_admin_dependencies()
    {
        // wp_enqueue_style( 'icss-styles', plugins_url('assets/css/style.css', dirname(__FILE__)), array(), ICSS_VERSION );
        wp_enqueue_script('icss-js', plugins_url('frontend/dist/main.bundle.js', dirname(__FILE__)), array(), ICSS_VERSION, true);
        wp_localize_script('icss-js', 'wordpress', array(
            'plugins_url' => plugins_url('/', dirname(__FILE__)),
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('wp_rest')
        ));
    }
}
