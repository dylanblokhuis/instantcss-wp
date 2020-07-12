<?php

namespace InstantCSS\Helpers;

class Template
{
    /**
    * @param $template_name
    */
    public static function icss_get_template($template_name)
    {
        $template_file = self::icss_locate_template($template_name);

        if (! file_exists($template_file)) :
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
    public static function icss_locate_template($template_name)
    {
        $default_path = plugin_dir_path(__DIR__) . 'templates/'; // Path to the template folder
        $template = $default_path . $template_name;

        return apply_filters('locate_template', $template, $template_name, $default_path);
    }
}
