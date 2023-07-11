<?php
class System 
{

  private static $modules = [];
  private static $configs = [];

  // Запуск 
  public static function run(){
    self::loadAppFiles();
    self::loadModulesConfigs();
    self::loadModules();
    self::route();
  }


  public static function getConfig($config){
    return self::$configs[$config];
  }


  public static function getModule($moduleName){
    return self::$modules[$moduleName];
  }


  // генерация пути
  public static function generatePath($segments){
    return implode(DIRECTORY_SEPARATOR, $segments);
  }


  // Загрузка конфингов модулей
  private static function loadModulesConfigs() {
    $moduleConfigs = glob(self::generatePath(['modules','*','configs','*.php']));
    foreach ($moduleConfigs as $config) {
      $moduleName = basename(dirname($config,2));
      if (!isset(self::$modules[$moduleName])){
        self::$modules[$moduleName] = [];
      }
      if (lcfirst(pathinfo($config)['filename']) == lcfirst($moduleName)){
        self::$modules[$moduleName]['module'] = include_once self::generatePath([ROOT_PATH,$config]);
      }
      else{
        self::$modules[$moduleName][pathinfo($config)['filename']] = include_once self::generatePath([ROOT_PATH,$config]);
      }
    }
  }
  

  // Загрузка модулей
  private static function loadModules()
  {
    foreach (self::$modules as $name => $values) {
      $moduleEnabled = self::checkModuleEnabled($name);
      if ($moduleEnabled) {
        $appPath = self::generatePath([ROOT_PATH,'modules',""]);
        $phpFiles = self::globRecursive($appPath . '*.php');
        foreach ($phpFiles as $file) {
          if (basename(dirname($file)) != 'configs'){
            include_once $file;
          }
        }
        $moduleInitFunction = self::getModuleInitFunction($name);
        if ($moduleInitFunction) {
          self::$modules[$moduleName] = $moduleInitFunction;
        }
      }
    }
  }
  public static function showError($error){
    header("HTTP/1.0 404 Not Found");
    echo $error;
    exit();
  }

  // Загрузка файлов системы
  private static function loadAppFiles()
  {
    $appPath = self::generatePath([ROOT_PATH,'app',""]);
    $phpFiles = self::globRecursive($appPath . '*.php');
    foreach ($phpFiles as $file) {
      if (basename(dirname($file)) == 'configs'){
        self::$configs[pathinfo($file)['filename']] = require_once $file;
      }
      else{
        require_once $file;
      }
    }
  }


  private static function globRecursive($pattern, $flags = 0)
  {
    $files = glob($pattern, $flags);

    foreach (glob(dirname($pattern) . '/*', GLOB_ONLYDIR | GLOB_NOSORT) as $dir) {
      $files = array_merge($files, self::globRecursive($dir . '/' . basename($pattern), $flags));
    }

    return $files;
  }
  

  // Роутинг
  private static function route() {
    Router::getInstance();
  }
  

  // проверка на включение модуля
  private static function checkModuleEnabled($moduleName) {
    return TRUE;
  }


  // запуск функции инициализации
  private static function getModuleInitFunction($moduleName) {

    $controller = lcfirst($moduleName)."Controller";

    if (class_exists($controller)){

      self::$modules[$moduleName]['controller'] = new $controller();

      if (method_exists(self::$modules[$moduleName]['controller'], "init")) {
        self::$modules[$moduleName]['controller']->init();
      }

    }else{
      unset(self::$modules[$moduleName]);
    }
  }


}
