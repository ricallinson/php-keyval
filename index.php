<?php
namespace php_require\php_keyval;

class KeyVal {

    private $prefix = "";

    private $ttl = 0;

    public function __construct($prefix, $ttl=0) {

        $this->prefix = $prefix;
        $this->ttl = $ttl;

        if (!function_exists("apc_store")) {
            $this->ttl = 0;
        }
    }

    public function put($key, $val) {

        $fullkey = $this->prefix . $key;

        file_put_contents($fullkey, $val);

        if ($this->ttl) {
            apc_store($fullkey, $val, $this->ttl);
        }
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

        $val = file_get_contents($fullkey);

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

        unlink($fullkey);
    }
}

$module->exports = function ($prefix, $ttl=0) {
    return new KeyVal($prefix, $ttl);
};
