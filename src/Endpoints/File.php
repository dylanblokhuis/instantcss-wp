<?php

namespace InstantCSS\Endpoints;

use ScssPhp\ScssPhp\Compiler;
use Symfony\Component\Filesystem\Exception\IOException;
use Symfony\Component\Filesystem\Filesystem;
use WP_REST_Request;

class File
{
    /**
     * @var string
     */
    private $scss_folder;
    private $css_file;

    public function __construct()
    {
        $this->scss_folder = plugin_dir_path( dirname(__DIR__)) . "public/scss/";
        $this->css_file = plugin_dir_path( dirname(__DIR__)) . "public/css/custom.css";
    }

    /**
     * @param $request
     * @return mixed|\WP_Error|\WP_HTTP_Response|\WP_REST_Response
     */
    public function get_all($request)
    {
        $tree = $this->get_relative_file_tree($this->scss_folder);

        return rest_ensure_response($tree);
    }

    /**
     * @param WP_REST_Request $request
     * @return mixed|\WP_Error|\WP_HTTP_Response|\WP_REST_Response
     */
	public function get($request)
	{
        $path = $this->scss_folder . $request->get_param('path');
        $content = file_get_contents($path);

        if ($content === false) {
            return new \WP_REST_Response("No file found", 400);
        }

        return rest_ensure_response([
            "content" => $content
        ]);
	}

    /**
     * @param $request
     * @return mixed|\WP_Error|\WP_HTTP_Response|\WP_REST_Response
     */
	public function create($request)
    {
        $path = $request->get_param('path');

        if ($this->createScssFile($path)) {
            return rest_ensure_response('File created successfully');
        } else {
            return new \WP_REST_Response("Failed to create file", 500);
        }
    }

    /**
     * @param $request
     * @return mixed|\WP_Error|\WP_HTTP_Response|\WP_REST_Response
     */
    public function update($request)
    {
        $path = $request->get_param('path');
        $content = $request->get_param('content');

        try {
            $filesystem = new Filesystem();
            $filesystem->dumpFile($this->scss_folder . $path, $content);

            $this->compile();

            return rest_ensure_response('File updated successfully');
        } catch (IOException $e) {
            return new \WP_REST_Response("Failed to update file", 500);
        }
    }

    /**
     * @param $request
     * @return mixed|\WP_Error|\WP_HTTP_Response|\WP_REST_Response
     */
    public function delete($request)
    {
        try {
            $filesystem = new Filesystem();
            $filesystem->remove($this->scss_folder . $request->get_param('path'));

            return rest_ensure_response("Deleted successfully");
        } catch (IOException $e) {
            return new \WP_REST_Response("Failed to delete", 500);
        }
    }

    /**
     * @return bool
     */
    private function compile()
    {
        $scss = file_get_contents($this->scss_folder . "main.scss");

        if ($scss === false) {
            return false;
        }

        try {
            $compiler = new Compiler();
            $compiler->setImportPaths($this->scss_folder);
            $css = $compiler->compile($scss);

            $filesystem = new Filesystem();
            $filesystem->dumpFile($this->css_file, $css);
            return true;
        } catch (\Exception $e) {
            dd($e->getTraceAsString());
        }
    }

    /**
     * @param $name
     * @param string $content
     * @return bool
     */
    private function createScssFile($name, $content = "")
    {
        try {
            $filesystem = new Filesystem();
            $filesystem->dumpFile($this->scss_folder . $name, $content);

            return true;
        } catch (IOException $e) {
            return false;
        }
    }


    /**
     * @param $folder_path
     * @return array
     */
    private function get_relative_file_tree($folder_path) {
        $iterator = new \IteratorIterator(new \DirectoryIterator($folder_path));

        $tree = array();
        foreach ($iterator as $file) {
            if ($file->isDot()) {
                continue;
            }

            // files like .gitkeep, .DS_Store
            if ($file->getFilename()[0] === '.') {
                continue;
            }

            $transform_relative = str_replace($this->scss_folder, '', $file->getPathName());

            $meta = array(
                'name' => $file->getFilename(),
                'path' => $transform_relative,
                "is_dir" => $file->isDir()
            );

            if ($file->isDir()) {
                $nodes = $this->get_relative_file_tree($file->getPathname());

                // only add the nodes if we have some
                if (!empty($nodes)) {
                    $meta['children'] = $nodes;
                }
            }

            $tree[] = $meta;
        }

        return $tree;
    }
}
