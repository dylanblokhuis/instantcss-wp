<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( 'Access denied.' );
}

class InstantCSS_Ajax
{
	public function __construct()
	{
		// ajax actions related to saving editor CSS
		add_action( 'wp_ajax_icss_save_css', array( $this, 'icss_ajax_save_css' ) );
		add_action( 'wp_ajax_icss_get_css', array( $this, 'icss_ajax_get_css' ) );
		// ajax actions related to saving the editor theme
		add_action( 'wp_ajax_icss_save_theme', array( $this, 'icss_ajax_save_theme' ) );
		add_action( 'wp_ajax_icss_get_theme', array( $this, 'icss_ajax_get_theme' ) );
		//ajax actions related to saving the css preprocessor
		add_action( 'wp_ajax_icss_save_preprocessor', array( $this, 'icss_ajax_save_preprocessor' ) );
		add_action( 'wp_ajax_icss_get_preprocessor', array( $this, 'icss_ajax_get_preprocessor' ) );
		//ajax actions related to the minify option
		add_action( 'wp_ajax_icss_save_minify', array( $this, 'icss_ajax_save_minify' ) );
		add_action( 'wp_ajax_icss_get_minify', array( $this, 'icss_ajax_get_minify' ) );
	}

	/**
	 * Ajax action located in js/editor.js
	 */
	public function icss_ajax_get_css()
	{
		if ( !$this->has_permissions()) {
			echo "You do not have permission to save CSS";
			wp_die();
		}

		$savedCSS = get_option( 'icss_css' );
		$styles = stripslashes($savedCSS);
		if ( isset( $styles ) ) {
			echo $styles;
		} else {
			echo '';
		}

		wp_die();
	}

	/**
	 * Ajax action located in js/editor.js
	 */
	public function icss_ajax_save_css()
	{
		if ( !$this->has_permissions()) {
			echo "You do not have permission to save CSS";
			wp_die();
		}

		$css = $_POST['css'];
		$postcss = $_POST['postcss'];

		if ( isset( $css ) ) {
			update_option( 'icss_css',  $css, true );
			update_option( 'icss_version', time(), true );
		} else {
			update_option( 'icss_css',  '', true );
			update_option( 'icss_version', time(), true );
		}

		if ( isset( $postcss ) ) {
			update_option( 'icss_postcss',  $postcss, true );
			update_option( 'icss_version', time(), true );
		} else {
			update_option( 'icss_postcss',  '', true );
			update_option( 'icss_version', time(), true );
		}

		echo "Saved CSS";

		wp_die();
	}

	/**
	 * Ajax action to change color scheme theme
	 */
	public function icss_ajax_save_theme()
	{
		if ( !$this->has_permissions()) {
			echo "You do not have permission to save theme";
			wp_die();
		}

		$newTheme = $_POST['theme'];

		if ( isset( $newTheme ) ) {
			update_option( 'icss_theme',  $newTheme, true );
		} else {
			update_option( 'icss_theme',  'vs', true );
		}
		echo "Saved theme";
		wp_die();
	}

	/**
	 * Ajax action located in js/editor.js
	 */
	public function icss_ajax_get_theme()
	{
		if ( !$this->has_permissions()) {
			echo "You do not have permission to save theme";
			wp_die();
		}
		
		$theme = get_option( 'icss_theme' );

		if ( isset( $theme ) ) {
			echo $theme;
		} else {
			echo 'vs';
		}

		wp_die();
	}

	/**
	 * Ajax action located in js/editor.js
	 */
	public function icss_ajax_save_preprocessor()
	{
		
		if ( !$this->has_permissions()) {
			echo "You do not have permission to save preprocessor";
			wp_die();
		}

		$newPreprocessor = $_POST['preprocessor'];

		if ( isset( $newPreprocessor ) ) {
			update_option( 'icss_preprocessor',  $newPreprocessor, true );
		} else {
			update_option( 'icss_preprocessor',  'css', true );
		}

		echo "Saved preprocessor";

		wp_die();
	}


	/**
	 * Ajax action located in js/editor.js
	 */
	public function icss_ajax_get_preprocessor()
	{
		if ( !$this->has_permissions()) {
			echo "You do not have permission to get the preprocessor";
			wp_die();
		}

		$preprocessor = get_option( 'icss_preprocessor' );

		if ( isset( $preprocessor ) ) {
			echo $preprocessor;
		} else {
			echo 'css';
		}

		wp_die();
	}

	/**
	 * Ajax action located in js/editor.js
	 */
	public function icss_ajax_save_minify()
	{
		if ( !$this->has_permissions()) {
			echo "You do not get to save the minification option";
			wp_die();
		}

		$minifyOption = $_POST['minify'];

		if ( isset( $minifyOption ) ) {
			update_option( 'icss_minify',  $minifyOption, true );
		} else {
			update_option( 'icss_minify',  'off', true );
		}

		echo "Saved minify option";

		wp_die();
	}

	/**
	 * Ajax action located in js/editor.js
	 */
	public function icss_ajax_get_minify()
	{
		if ( !$this->has_permissions()) {
			echo "You do not have the permission to get the minification option";
			wp_die();
		}

		$minify = get_option( 'icss_minify' );

		if ( isset( $minify ) ) {
			echo $minify;
		} else {
			echo 'off';
		}

		wp_die();
	}

	private function has_permissions() {
		if ( !current_user_can('manage_options') ) {
			return false;
		}
		return true;
	}
}