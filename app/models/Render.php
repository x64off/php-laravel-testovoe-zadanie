<?php
class Render{
    public static function addData($key,$value){
        if (!Registry::get('Render_data')) Registry::set('Render_data',[]);
        $data = Registry::get('Render_data');
        $data[$key] = $value;
        Registry::set('Render_data',$data);
    }
    public static function getData(){
        return Registry::get('Render_data');
    }
    public static function render($template = null,$layout = 'layout.html')
    {
        if(!isset(self::getData()['system'])){
            $db = databaseModel::getInstance();
            $system_configs=$db->read('system_configs');
            $configs = [];
            foreach ($system_configs as $key => $value) {
                $configs[$value['name']] = $value['value'];
            }
            self::addData('system',$configs);
        }
        if (!isset(self::getData()['theme']))
            self::addData('theme',System::getConfig('system')['theme']);
        $theme = System::generatePath([ROOT_PATH,'themes',self::getData()['theme']]);
        
        $content = self::loadTemplate(System::generatePath([$theme,$template]));
        if ($layout){
            self::addData('content',$content);
            echo self::loadTemplate(System::generatePath([$theme,$layout]));
        }
        else
            echo $content;
    }
    protected static function loadTemplate($templatePath)
    {
        // Проверка наличия файла шаблона
        if (!file_exists($templatePath)) {
            throw new Exception("Template file not found: $templatePath");
        }
        // Загрузка содержимого файла шаблона
        $TemplateContent = file_get_contents($templatePath);
        $TemplateContent = preg_replace('/<\?php.*?\?>/s', '', $TemplateContent);
        $TemplateContent = preg_replace('/<\?=.*?\?>/s', '', $TemplateContent);
        $TemplateContent = str_replace(['{{', '}}'], ['<?= ', ' ?>'], $TemplateContent);
        $TemplateContent = str_replace(['{', '}'], ['<?php ', ' ?>'], $TemplateContent);
        $render_data = Registry::get('Render_data')? Registry::get('Render_data') : [];
        // Временно определяем переменные из массива $data
        foreach ( $render_data as $key => $value) {
            $$key = $value;
        }
    
        // Выполнение кода PHP в контексте шаблона
        ob_start();
        eval('?>' . $TemplateContent);
        $renderedContent = ob_get_clean();
    
        // Возвращение результата
        return $renderedContent;
    }
}