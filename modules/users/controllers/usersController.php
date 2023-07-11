<?php
class usersController extends Controller{
    private $model;
    public function init(){
        $this->model = $this->loadModel('users');
    }
    public function index($get=[],$post=[]){
        $site_url = System::getConfig('system')['site_url'];
        if ($post){
            
            if (isset($post['register'])){
                unset($post['register']);
                $this->loadModel('users')->Register($post);
                echo '<meta http-equiv="refresh" content="0; url='.$site_url.'" />';
            }elseif (isset($post['login'])) {
                unset($post['login']);
                $this->loadModel('users')->Auth($post);
                echo '<meta http-equiv="refresh" content="0; url='.$site_url.'" />';
            }
            exit;
        }
        if ($get&&$get['id'] == 'logout'){
            $this->loadModel('users')->logout();
            echo '<meta http-equiv="refresh" content="0; url='.$site_url.'" />';
            exit;
        }
        Render::render('index.html');
    }
}