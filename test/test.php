<?php

error_reporting(E_ALL);
ini_set('display_errors', 'on');

/*
    Now we "require()" the file to test.
*/

$module = new stdClass();
require(__DIR__ . "/../index.php");

/*
    Now we test it.
*/

describe("php-keyval", function () use ($module) {

    it("should call store.put(), store.get(), store.delete()", function () use ($module) {

        $func = $module->exports;

        $store = $func(__DIR__ . "/fixtures/tmp");
        $key = "key";
        $val = "val";

        $store->put($key, $val);

        assert($store->get($key) === $val);

        $store->delete($key);

        assert($store->get($key) === null);
    });

    it("should return [10]", function () use ($module) {

        $func = $module->exports;
        $store = $func(__DIR__ . "/fixtures/range");

        $keys = $store->getKeys();

        assert(count($keys) === 10);
    });

    it("should return [5]", function () use ($module) {

        $func = $module->exports;
        $store = $func(__DIR__ . "/fixtures/range");

        $keys = $store->getKeys(5);

        assert(count($keys) === 5);
    });

    it("should return [1]", function () use ($module) {

        $func = $module->exports;
        $store = $func(__DIR__ . "/fixtures/range");

        $keys = $store->getKeys(0, 1);

        assert(count($keys) === 1);
    });

    it("should return [2]", function () use ($module) {

        $func = $module->exports;
        $store = $func(__DIR__ . "/fixtures/range");

        $keys = $store->getKeys(8, 2);

        assert(count($keys) === 2);
    });

    it("should return [3]", function () use ($module) {

        $func = $module->exports;
        $store = $func(__DIR__ . "/fixtures/range");

        $keys = $store->getKeys(5, 3);

        assert(count($keys) === 3);
    });

    it("should return an [empty array]", function () use ($module) {

        $func = $module->exports;
        $store = $func(__DIR__ . "/fixtures/fake");

        $keys = $store->getKeys(5, 3);

        assert(count($keys) === 0);
    });

    it("should return [3] from the filter [key2 === a]", function () use ($module) {

        $func = $module->exports;
        $store = $func(__DIR__ . "/fixtures/filtering");
        $filter = array("key2" => "a");

        $keys = $store->getKeys(0, null, $filter);

        assert(count($keys) === 3);
    });

    it("should return [2] from the filter [key2 === b]", function () use ($module) {

        $func = $module->exports;
        $store = $func(__DIR__ . "/fixtures/filtering");
        $filter = array("key2" => "b");

        $keys = $store->getKeys(0, null, $filter);

        assert(count($keys) === 2);
    });

    it("should return [2] from the filter [key2 === a, key3 === z]", function () use ($module) {

        $func = $module->exports;
        $store = $func(__DIR__ . "/fixtures/filtering");
        $filter = array("key2" => "a", "key3" => "z");

        $keys = $store->getKeys(0, null, $filter);

        assert(count($keys) === 2);
    });

    it("should return [0] from the filter [key2 === b, key3 === z]", function () use ($module) {

        $func = $module->exports;
        $store = $func(__DIR__ . "/fixtures/filtering");
        $filter = array("key2" => "b", "key3" => "z");

        $keys = $store->getKeys(0, null, $filter);

        assert(count($keys) === 0);
    });

    it("should return [2] from the filter [key2 === b, key3 === y]", function () use ($module) {

        $func = $module->exports;
        $store = $func(__DIR__ . "/fixtures/filtering");
        $filter = array("key2" => "b", "key3" => "y");

        $keys = $store->getKeys(0, null, $filter);

        assert(count($keys) === 2);
    });

    it("should return [null] when trying to get() nothing", function () use ($module) {

        $func = $module->exports;
        $store = $func(__DIR__ . "/fixtures/null");

        $result = $store->get("null");

        assert($result === null);
    });

    it("should return [true] when trying to delete() nothing", function () use ($module) {

        $func = $module->exports;
        $store = $func(__DIR__ . "/fixtures/null");

        $result = $store->delete("null");

        assert($result === true);
    });
});
