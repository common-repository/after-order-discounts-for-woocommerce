<?php

namespace Waod\App\Helpers;

use Exception;

class Template
{
    static $path = __DIR__;
    private $html_content = '';

    /**
     * configure template path
     * Template constructor.
     * @param $path
     */
    public function __construct($path)
    {
        self::$path = rtrim($path, '/') . '/';
    }

    /**
     * Render the template
     * @param $file
     * @param array $variables
     * @return false|string|null
     */
    function render($file, $variables = array())
    {
        $file_path = self::$path . ltrim($file, '/');
        $output = NULL;
        try {
            if (file_exists($file_path)) {
                // Extract the variables to a local namespace
                extract($variables);
                // Start output buffering
                ob_start();
                // Include the template file
                include $file_path;
                // End buffering and return its contents
                $output = ob_get_clean();
            } else {
                $output = 'File not found';
            }
        } catch (Exception $exception) {
            $output = $exception->getMessage();
        }
        $this->html_content = $output;
        return $this;
    }

    /**
     * Display the html content
     */
    function display()
    {
        print $this->html_content;
    }

    /**
     * get the content to render
     * @return string
     */
    function get()
    {
        return $this->html_content;
    }
}