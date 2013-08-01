<?php
namespace php_require\php_keyval;

class KeyVal {

    private $prefix = "";

    private $ttl = 0;

    public function __construct($prefix, $ttl=0) {

        $this->prefix = $prefix;
        $this->ttl = $ttl;

        if (substr($this->prefix, -1) !== DIRECTORY_SEPARATOR) {
            $this->prefix = $this->prefix . DIRECTORY_SEPARATOR;
        }

        if ($ttl === 0 || !function_exists("apc_store")) {
            $this->ttl = 0;
        }
    }

    public function put($key, $val) {

        $fullkey = $this->prefix . $key;

        $bytes = file_put_contents($fullkey, json_encode($val));

        if ($bytes > 0 && $this->ttl) {
            apc_store($fullkey, $val, $this->ttl);
        }

        return $bytes > 0 ? true : false;
    }

    public function get($key) {

        $fullkey = $this->prefix . $key;

        if ($this->ttl) {
            $success;
            $val = apc_fetch($fullkey, $success);
            if ($success) {
                return $val;
            }
        }

        if (!is_file($fullkey)) {
            return null;
        }

        $val = json_decode(file_get_contents($fullkey), true);

        if ($this->ttl) {
            $this->put($key, $val);
        }

        return $val;
    }

    public function delete($key) {

        $fullkey = $this->prefix . $key;

        if ($this->ttl) {
            apc_delete($fullkey);
        }

        if (!is_file($fullkey)) {
            return true;
        }

        return unlink($fullkey);
    }

    public function getKeys($from=0, $length=null) {

        $keys = array();
        $cur = 0;
        $dh  = opendir($this->prefix);

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
