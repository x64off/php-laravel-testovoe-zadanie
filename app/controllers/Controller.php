<?php

class Controller
{
    public function __construct() {
    }

    public function index($get=[],$post=[]){}
    public function getConfig(){
        return $this->config;
    }
    public function loadModel($Name){
        $Name = $Name.'Model';
        return new $Name();
    }

    private function getControllerName() {
        $reflectionClass = new ReflectionClass($this);
        $className = $reflectionClass->getShortName();
        $controllerName = str_replace('Controller', '', $className);
        return $controllerName;
    }


    public function init()
    {
    }

    protected function loadTemplate($view, $data = [])
    {
        Render::addData($this->getControllerName(),$data);
        Render::render($view,false);
    }
    protected function render($view, $data = [],$layout = 'layout.html')
    {
        Render::addData($this->getControllerName(),$data);
        Render::render($view,$layout);
    }
}
