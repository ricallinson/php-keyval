<?php

error_reporting(E_ALL);
ini_set('display_errors', 'on');

/*
    Create a MockModule to load our module into for testing.
*/

class MockModule {
    public $exports = array();
}
$module = new MockModule();

/*
    Now we "require()" the file to test.
*/

require(__DIR__ . "/../index.php");

/*
    Now we test it.
*/

describe("php-keyval", function () use ($module) {

    it("should call store.put(), store.get(), store.delete() with no cache", function () use ($module) {

        $func = $module->exports;

        $store = $func(__DIR__ . "/fixtures/tmp/");
        $key = "key";
        $val = "val";

        $store->put($key, $val);

        assert($store->get($key) === $val);

        $store->delete($key);

        assert($store->get($key) === null);
    });

    it("should call store.put(), store.get(), store.delete() with a 1 second cache", function () use ($module) {

        $func = $module->exports;

        $store = $func(__DIR__ . "/fixtures/tmp/", 1);
        $key = "key1";
        $val = "val1";

        $store->put($key, $val);

        assert($store->get($key) === $val);

        $store->delete($key);

        assert($store->get($key) === null);
    });
});
