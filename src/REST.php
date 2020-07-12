<?php

namespace InstantCSS;

use InstantCSS\Endpoints\File;

class REST
{
    private static $namespace = 'instantcss/v1';

    public function __construct()
    {
        $this->files(new File());
        add_action('rest_api_init', array($this, 'routes'));
    }

    public function files($endpoint)
    {
        register_rest_route(self::$namespace, '/files', array(
            'methods' => 'GET',
            // 'permission_callback' => 'is_admin_callback',
            'callback' => array($endpoint, 'get_all')
        ));

        register_rest_route(self::$namespace, '/files/get', array(
            'methods' => 'POST',
            // 'permission_callback' => 'is_admin_callback',
            'callback' => array($endpoint, 'get')
        ));

        register_rest_route(self::$namespace, '/files', array(
            'methods' => 'POST',
            // 'permission_callback' => 'is_admin_callback',
            'callback' => array($endpoint, 'create')
        ));

        register_rest_route(self::$namespace, '/files', array(
            'methods' => 'PUT',
            // 'permission_callback' => 'is_admin_callback',
            'callback' => array($endpoint, 'update')
        ));

        register_rest_route(self::$namespace, '/files', array(
            'methods' => 'DELETE',
            // 'permission_callback' => 'is_admin_callback',
            'callback' => array($endpoint, 'delete')
        ));
    }

    public function is_admin_callback()
    {
        return current_user_can('editor') || current_user_can('administrator');
    }
}
