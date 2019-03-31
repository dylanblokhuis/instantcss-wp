<?php
if ( ! defined( 'ABSPATH' ) ) {
    die( 'Access denied.' );
}

class InstantCSS
{
    /**
     * InstantCSS constructor.
     */
    public function __construct()
    {
        // Admin actions
        add_action( 'admin_menu',  array( $this, 'icss_create_admin_menu' ) );

        new InstantCSS_Ajax();

        // Options
        add_option( 'icss_css', '', false, true );
        add_option( 'icss_postcss', '', false, true );
        add_option( 'icss_lang', 'css', false, true );
        add_option( 'icss_theme', 'vs', false, true );
		add_option( 'icss_preprocessor', 'css', false, true );
		add_option( 'icss_minify', 'off', false, true );

        // Add saved option to script tag
        add_action( 'wp_head', array( $this, 'icss_get_css' ), 5 );
    }

    /**
     * Adds menu page to WordPress admin panel
     */
    public function icss_create_admin_menu()
    {
        global $instantcss_page;
        $instantcss_page = add_menu_page( 'Instant CSS', 'Instant CSS', 'manage_options', 'instantcss', array( $this, 'icss_show_admin_menu' ) );
        add_action( 'load-'.$instantcss_page, array( $this, 'icss_plugin_page' ) );
    }

    /**
     * Get the template file for the admin page
     */
    public function icss_show_admin_menu()
    {
        return $this->icss_get_template( 'editor.php' );
    }

    /**
     * Enqueue all needed dependencies
     */
    public function icss_admin_dependencies()
    {
        wp_enqueue_style( 'icss-styles', plugins_url('assets/css/style.css', dirname(__FILE__)), array(), '1.0.0' );
        wp_enqueue_script( 'monaco-editor', plugins_url('assets/dist/main.bundle.js', dirname(__FILE__)), array(), '1.0.0', true );
        wp_localize_script( 'monaco-editor', 'wordpress', array(
        	'plugins_url' => plugins_url('/', dirname(__FILE__)),
	        'ajax_url' => admin_url('admin-ajax.php')
        ));
    }

    /**
     * Get values from the database using the Options API
     */
    public function icss_get_css()
    {
        $savedCSS = get_option( 'icss_postcss' );
        $styles = stripslashes($savedCSS);
        if ( isset( $styles ) ) {
            echo '<style type="text/css" id="instant-css">'. $styles .'</style>';
        }
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
		if ( $screen->id != $instantcss_page )
			return;

		add_action( 'admin_enqueue_scripts', array( $this, 'icss_admin_dependencies') );
	}

    /**
     * @param $template_name
     */
    private function icss_get_template( $template_name )
    {
        $template_file = $this->icss_locate_template( $template_name );

        if ( ! file_exists( $template_file ) ) :
            _doing_it_wrong( __FUNCTION__, sprintf( '<code>%s</code> does not exist.', $template_file ), '1.0.0' );
            return;
        endif;

        include $template_file;
    }

    /**
     * @param $template_name
     * @return mixed|void
     * Locate template file in templates folder
     */
    private function icss_locate_template( $template_name )
    {
        $default_path = plugin_dir_path( __DIR__ ) . 'views/'; // Path to the template folder

        // Search template file in theme folder.
        $template = locate_template( array(
            $template_name
        ) );

        // Get plugins template file.
        if ( ! $template ) :
            $template = $default_path . $template_name;
        endif;

        return apply_filters( 'locate_template', $template, $template_name, $default_path );
    }



}