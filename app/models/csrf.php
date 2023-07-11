<?php
class csrf{
    private static $instance;
    public function __construct() {
        if (!isset($_SESSION['csrf_token'])) $_SESSION['csrf_token'] = self::generateCSRFToken();
    }
    public static function getInstance(){
        if (!self::$instance)
            self::$instance = new csrf();
        return self::$instance;
    }
    public static function update(){
        $_SESSION['csrf_token'] = self::generateCSRFToken();
    }
    public static function generateCSRFToken()
    {
        $token = bin2hex(random_bytes(32));
        return $token;
    }
    public static function verify($token)
    {
        // Проверка CSRF-токена на соответствие сохраненному значению
        if ($token === $_SESSION['csrf_token']) {
            // self::update();
            return true; // CSRF-токен верный
        } else {
            return false; // CSRF-токен неверный
        }
    }
}