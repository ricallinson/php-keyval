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

        if (!function_exists("apc_store")) {
            $this->ttl = 0;
        }
    }

    public function genUuid() {

        return sprintf( '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            // 32 bits for "time_low"
            mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ),

            // 16 bits for "time_mid"
            mt_rand( 0, 0xffff ),

            // 16 bits for "time_hi_and_version",
            // four most significant bits holds version number 4
            mt_rand( 0, 0x0fff ) | 0x4000,

            // 16 bits, 8 bits for "clk_seq_hi_res",
            // 8 bits for "clk_seq_low",
            // two most significant bits holds zero and one for variant DCE1.1
            mt_rand( 0, 0x3fff ) | 0x8000,

            // 48 bits for "node"
            mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff )
        );
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

    public function getKeys($from=0, $to=null) {

        $keys = array();
        $dh  = opendir($this->prefix);

        while (false !== ($filename = readdir($dh))) {
            if (is_file($this->prefix . $filename)) {
                $keys[] = $filename;
            }
            if ($to && --$to < $from) {
                return $keys;
            }
        }

        return $keys;
    }
}

$module->exports = function ($prefix, $ttl=0) {
    return new KeyVal($prefix, $ttl);
};
