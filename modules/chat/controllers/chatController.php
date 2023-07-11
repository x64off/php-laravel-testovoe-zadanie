<?php

class chatController extends Controller
{
    private $Model;
    public function api($get=[],$post=[]){
        $this->Model = $this->loadModel('chat');
        if(isset($post['get_chats'])){
            $chats = $this->Model->getChats();
            echo json_encode(['status' => 'success', 'chats' => $chats,'token'=>$_SESSION['csrf_token']]);
        }
        else if(isset($post['get_messages'])&&isset($post['chat'])){
            $messages=$this->Model->getMessages($post['chat']);
            echo json_encode(['status' => 'success',"messages"=>$messages,'token'=>$_SESSION['csrf_token']]);
        }
        else if(isset($post['message'])&&isset($post['chat'])&&isset($post['user'])){
            $this->Model->addMessage($post['message'],$post['chat'],$post['user']);
            echo json_encode(['status' => 'success',"post"=>$post,'token'=>$_SESSION['csrf_token']]);
        }else if(isset($post['create_chat'])&&isset($post['name'])&&$post['name']){
            $this->Model->createChat($post['name']);
            echo json_encode(['status' => 'success','token'=>$_SESSION['csrf_token']]);
        }else if(isset($post['search'])&&$post['search']){
            $data = $this->Model->search($post['search']);
            echo json_encode(['status' => 'success','chat'=>$data[0]['chat'],'message'=>$data[0]['id'],'token'=>$_SESSION['csrf_token']]);
        }
        else
            echo json_encode(['status' => 'error',"post"=>$post,'token'=>$_SESSION['csrf_token']]);
        exit;
    }
    public function getPage($get=[],$post=[]){
        if(isset($get['page'])){
            echo $this->loadTemplate("pages/".$get['page'].".html",['test'=>"test-data"]);
        }
    }
}
