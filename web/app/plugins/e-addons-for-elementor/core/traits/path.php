<?php

namespace EAddonsForElementor\Core\Traits;

trait Path {

    public static $ignore = ['.', '..', '.git', '.svn', '.DS_Store', '.gitignore', '._.DS_Store', '.htaccess'];

    public function get_path_files($path, $orderby = 'date') {
        $files = array();
        foreach (scandir($path) as $file) {
            if (in_array($file, self::$ignore))
                continue;
            if ($orderby == 'date') {
                $files[$file] = filemtime($path . DIRECTORY_SEPARATOR . $file);
            } else {
                $files[] = $file;
            }
        }
        arsort($files);
        if ($orderby == 'date') {
            $files = array_keys($files);
        }
        return ($files) ? $files : false;
    }

    public static function path_to_array($path, $hidden = false, $files = true) {
        $paths = array();
        $dir = scandir($path);
        foreach ($dir as $key => $file) {
            if (!in_array($value, array(self::$ignore))) {
                if (is_dir($path . DIRECTORY_SEPARATOR . $file)) {
                    $paths[$file] = self::path_to_array($path . DIRECTORY_SEPARATOR . $file, $hidden, $files);
                } else {
                    if ($files && (substr($file, 0, 1) != '.' || $hidden)) {
                        $paths[] = $file;
                    }
                }
            }
        }
        return $paths;
    }

    public static function is_empty_path($path) {
        if (is_dir($path)) {
            $files = self::path_to_array($path);
            return empty($files);
        }
        return false;
    }

    public static function compress_folder($options) {
        $defaults = array(
            'source' => '',
            'filename' => '',
            'folder' => '',
            'zip_temp_directory' => plugin_dir_path(__FILE__),
            'exclude_directories' => self::$ignore,
            'exclude_files' => self::$ignore,
        );
        foreach ($defaults as $key => $value) {
            if (!isset($options[$key])) {
                $options[$key] = $value;
            }
        }
        $zip = new \ZipArchive;
        $res = $zip->open($options['filename'], \ZipArchive::CREATE && \ZipArchive::OVERWRITE);
        $iterator = new \RecursiveDirectoryIterator($options['source']);
        foreach (new \RecursiveIteratorIterator($iterator) as $filename) {
            if (in_array(basename($filename), $options['exclude_files'])) {
                continue;
            }
            foreach ($options['exclude_directories'] as $pathectory) {
                if (strstr($filename, DIRECTORY_SEPARATOR . "{$pathectory}" . DIRECTORY_SEPARATOR)) {
                    continue 2;
                }
            } // continue the parent foreach loop
            $zip_filename = str_replace(trailingslashit($options['source']), '', basename($filename));
            $file_path = $filename->getRealPath();
            $relative_path = substr($file_path, strlen($options['source']));
            $zip->addFile($file_path, $options['folder'] . $relative_path);
        }
        $zip->close();
    }

    static public function url_to_path($url) {
        //var_dump(ABSPATH.'wp-admin'.DIRECTORY_SEPARATOR.'includes'.DIRECTORY_SEPARATOR.'file.php'); die();
        include_once(ABSPATH.'wp-admin'.DIRECTORY_SEPARATOR.'includes'.DIRECTORY_SEPARATOR.'file.php');
        $rel = wp_make_link_relative($url);
        $rel = str_replace('/', DIRECTORY_SEPARATOR, $rel);
        $home_url = get_home_url();
        $tmp = explode('/', $home_url);
        if (count($tmp) > 3) {
            $tmp = array_slice($tmp, 3);
            if (!empty($tmp)) {
                $tmp = DIRECTORY_SEPARATOR.implode(DIRECTORY_SEPARATOR, $tmp);
                if (substr($rel, 0, strlen($tmp)) == $tmp) {
                    $rel = substr($rel, strlen($tmp));
                }
            }
        }
        $tmp = substr(get_home_path(), 0, -1) . $rel;
        $tmp = str_replace('/', DIRECTORY_SEPARATOR, $tmp);
        return $tmp;
    }

    public static function path_to_url($path) {
        $wp_upload_dir = wp_upload_dir();
        $url = str_replace($wp_upload_dir["basedir"], $wp_upload_dir["baseurl"], $path);
        //var_dump(ABSPATH); var_dump(get_home_url(null, '/')); var_dump($url); die();
        $ABSPATH = str_replace('/', DIRECTORY_SEPARATOR, ABSPATH);
        $url = str_replace($ABSPATH, get_home_url(null, '/'), $url);
        $url = str_replace('\\', '/', $url);
        return $url;
    }
    
    static public function get_attached_file($attachment_id, $unfiltered = false) {
        $file_path = get_attached_file($attachment_id, $unfiltered);
        $file_path = str_replace('/', DIRECTORY_SEPARATOR, $file_path);
        return $file_path;
    }

    static public function unlink($path) {
        if (file_exists($path)) {
            unlink($path);
        }
        if (is_dir($path)) {
            $files = glob($path . DIRECTORY_SEPARATOR . '*');
            foreach ($files as $file) {
                is_dir($file) ? self::unlink($file) : unlink($file);
            }
            rmdir($path);
        }
        return true;
    }

    /*
     * License: DWTFYW
     * https://gist.github.com/UziTech/3b65b2543cee57cd6d2ecfcccf846f20
     */

    static public function glob_recursive($path, $pattern, $flags = 0) {
        if (substr($path, -1) !== DIRECTORY_SEPARATOR) {
            $path .= DIRECTORY_SEPARATOR;
        }
        if ($flags == 'GLOB_ONLYFILE') {
            $files = array_filter(glob(DIRECTORY_SEPARATOR . "*"), 'is_file');
            $flags = 0;
        } else {
            $files = glob($path . $pattern, $flags);
        }
        foreach (glob($path . '*', GLOB_ONLYDIR | GLOB_NOSORT | GLOB_MARK) as $sub_path) {
            $path_files = self::glob_recursive($sub_path, $pattern, $flags);
            if ($pathFiles !== false) {
                $files = array_merge($files, $path_files);
            }
        }
        return $files;
    }

    /**
     *
     *
     * @return string
     */
    public static function get_current_url($post = false) {
        
        if ($post) {
            if (!empty($_REQUEST['queried_id'])) {
                return get_permalink($_REQUEST['queried_id']);
            }
            if (!empty(get_queried_object())) {
                $queried_object_url = self::get_link(get_queried_object());
                if ($queried_object_url) {
                    return $queried_object_url;
                }
            }
        }
        
        $url = ( is_ssl() ? 'https' : 'http' ) . "://";
        $host = '';
        if (isset($_SERVER['HTTP_HOST']) && $_SERVER['HTTP_HOST']) {
            $host = $_SERVER['HTTP_HOST'];
        } else if (isset($_SERVER['SERVER_NAME']) && $_SERVER['SERVER_NAME']) {
            $host = $_SERVER['SERVER_NAME'];
        }
        if (isset($_SERVER["SERVER_PORT"]) && $_SERVER["SERVER_PORT"] && $_SERVER["SERVER_PORT"] != "80" && $_SERVER["SERVER_PORT"] != "443") {
            $url .= $host . ":" . $_SERVER["SERVER_PORT"] . $_SERVER["REQUEST_URI"];
        } else {
            $url .= $host . $_SERVER["REQUEST_URI"];
        }
        return $url;
    }
    
    public static function glob($dir, $ext = null) {
        //return glob($dir);
        $files = [];
        $dir = str_replace('/', DIRECTORY_SEPARATOR, $dir);
        $scandir = $dir;
        if (strpos($dir, '*.') !== false) {
            list($scandir, $ext) = explode('*.', $dir, 2);
        }
        
        if ($ext && is_dir($scandir)) {
            foreach (new \DirectoryIterator($scandir) as $fileInfo) {
                //var_dump($fileInfo->getFilename());
                if($fileInfo->isDot()) continue;
                if ($ext && $ext != $fileInfo->getExtension()) continue;
                $files[] = $fileInfo->getPathname();
            }
            //var_dump($files);
        }
        if (empty($files)) {
            $files = glob($dir);
        }
        return $files;
    }

}
