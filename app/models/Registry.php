<?php
class Registry {
    private static $data = [];

    public static function set($key, $value) {
        self::$data[$key] = $value;
    }

    public static function get($key, $default = null) {
        return isset(self::$data[$key]) ? self::$data[$key] : $default;
    }
}
