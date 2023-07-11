<?php

class pagesController extends Controller
{
    public function init()
    {
    }
    public function index($get=[],$post=[]){
        if(isset($get['page'])){
            $this->render("pages/".$get['page'].".html",['test'=>"test-data"]);
        }
        else
            $this->render("pages/index.html",['test'=>"test-data"]);
    }
    public function getPage($get=[],$post=[]){
        if(isset($get['page'])){
            echo $this->loadTemplate("pages/".$get['page'].".html",['test'=>"test-data"]);
        }
    }
}
