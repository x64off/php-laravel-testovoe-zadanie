<?php

class Model
{
    public function __construct() {
        $this->db = databaseModel::getInstance();
        $this->load();
    }
    public function load(){
    }
}
