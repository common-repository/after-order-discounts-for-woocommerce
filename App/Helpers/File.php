<?php

namespace Waod\App\Helpers;
class File
{
    /**
     * get all files inside articular directory
     * @param $file_path
     * @param $exclude_files
     * @param bool $need_file_extension
     * @return array
     */
    function getFiles($file_path, $exclude_files = array(), $need_file_extension = true)
    {
        $files = array();
        if ($handle = opendir($file_path)) {
            while (false !== ($entry = readdir($handle))) {
                if ($entry != "." && $entry != "..") {
                    $name = ($need_file_extension) ? $entry : basename($entry, '.php');
                    if (!in_array($name, $exclude_files)) {
                        array_push($files, $name);
                    }
                }
            }
            closedir($handle);
        }
        return $files;
    }
}