<?php

namespace InstantCSS\Endpoints;

use Symfony\Component\Filesystem\Exception\IOException;
use Symfony\Component\Filesystem\Filesystem;
use WP_REST_Request;

class File
{
    /**
     * @var string
     */
    private $scss_folder;

    public function __construct()
    {
        $this->scss_folder = plugin_dir_path( dirname(__DIR__)) . "public/scss/";
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

        if (!$content) {
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
        $name = $request->get_param('name');

        if ($this->createScssFile($name)) {
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
        $name = $request->get_param('name');
        $content = $request->get_param('content');

        if ($this->updateFile($name, $content)) {
            return rest_ensure_response('File updated successfully');
        } else {
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
     * @param $name
     * @param string $content
     * @return bool
     */
    private function updateFile($name, $content)
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
