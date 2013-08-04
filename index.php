<?php
namespace php_require\php_keyval;

class KeyVal {

    private $prefix = "";

    public function __construct($prefix) {

        $this->prefix = $prefix;

        if (substr($this->prefix, -1) !== DIRECTORY_SEPARATOR) {
            $this->prefix = $this->prefix . DIRECTORY_SEPARATOR;
        }
    }

    public function put($key, $val) {

        $fullkey = $this->prefix . $key;

        $bytes = file_put_contents($fullkey, json_encode($val));

        return $bytes > 0 ? true : false;
    }

    public function get($key) {

        $fullkey = $this->prefix . $key;

        if (!is_file($fullkey)) {
            return null;
        }

        $val = json_decode(file_get_contents($fullkey), true);

        return $val;
    }

    public function delete($key) {

        $fullkey = $this->prefix . $key;

        if (!is_file($fullkey)) {
            return true;
        }

        return unlink($fullkey);
    }

    public function getKeys($from=0, $length=null) {

        $keys = array();
        $cur = 0;
        $dh  = @opendir($this->prefix); /* using @ to suppresses error message */

        if (!is_resource($dh)) {
            error_log("php-keyval: Could not read diretory: " . $this->prefix);
            return $keys;
        }

        while (false !== ($filename = readdir($dh))) {

            if (is_file($this->prefix . $filename)) {

                if ($cur >= $from) {
                    array_push($keys, $filename);
                }

                if ($length !== null && $cur === $from + $length - 1) { 
                    return $keys;
                }

                $cur++;
            }
        }

        return $keys;
    }
}

$module->exports = function ($prefix, $ttl=0) {
    return new KeyVal($prefix, $ttl);
};
