<?php

namespace InstantCSS;

use InstantCSS\Endpoints\File;

class REST
{
    private static $namespace = 'instantcss/v1';

    public function __construct()
    {
        add_action('rest_api_init', array($this, 'files'));
    }

    /**
     * Files endpoint
     * Used for creating scss files and updating them
     */
    public function files()
    {
        $endpoint = new File();

        register_rest_route(self::$namespace, '/files', array(
            'methods' => 'GET',
            // 'permission_callback' => 'is_admin_callback',
            'callback' => array($endpoint, 'get_all'),
        ));

        register_rest_route(self::$namespace, '/files/get', array(
            'methods' => 'POST',
            // 'permission_callback' => 'is_admin_callback',
            'callback' => array($endpoint, 'get'),
            'args' => array(
                'path' => array(
                    'validate_callback' => array( $this, 'is_path_callback' ),
                ),
            ),
        ));

        register_rest_route(self::$namespace, '/files', array(
            'methods' => 'POST',
            // 'permission_callback' => 'is_admin_callback',
            'callback' => array($endpoint, 'create'),
            'args' => array(
                'path' => array(
                    'validate_callback' => array( $this, 'is_path_callback' ),
                ),
            ),
        ));

        register_rest_route(self::$namespace, '/files', array(
            'methods' => 'PUT',
            // 'permission_callback' => 'is_admin_callback',
            'callback' => array($endpoint, 'update'),
            'args' => array(
                'path' => array(
                    'validate_callback' => array( $this, 'is_path_callback' ),
                    'required' => true
                ),
                'content' => array(
                    'validate_callback' => array( $this, 'is_content_callback' ),
                    'required' => true
                )
            ),
        ));

        register_rest_route(self::$namespace, '/files', array(
            'methods' => 'DELETE',
            // 'permission_callback' => 'is_admin_callback',
            'callback' => array($endpoint, 'delete'),
            'args' => array(
                'path' => array(
                    'validate_callback' => array( $this, 'is_path_callback' ),
                    'required' => true
                ),
            ),
        ));
    }

    public function is_path_callback($param, $request, $key)
    {
        return is_string( $param );
    }

    public function is_content_callback($param, $request, $key)
    {
        return is_string( $param );
    }

    public function is_admin_callback()
    {
        return current_user_can('editor') || current_user_can('administrator');
    }
}
