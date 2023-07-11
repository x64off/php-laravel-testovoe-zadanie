<?php
class usersModel extends Model{
    public function load(){
        $this->sessionToken();
        $this->verifySessionToken();
    }
    public function sessionToken(){
        if (!isset($_SESSION['token'])) $_SESSION['token'] = csrf::generateCSRFToken();
    }
    private function getRights($group = 1){
        return json_decode($this->db->read('groups',['rights'],['id'=>$group])[0]['rights'],true);
    } 
    function verifySessionToken()
    {
        $user = $this->db->read('users',null,['token'=>$_SESSION['token']]);
        if ($user) {
            $group = $user[0]['ugroup'];
            $user = $user[0];
            $user['ugroup'] = $this->db->read('groups',['name'],['id'=>$user['ugroup']])[0]['name'];
            unset($user['password']);
            unset($user['user_salt']);
            Registry::set('user-data',$user);
            Render::addData('user',$user);
        }else{
            $group = 2;
        }
        Registry::set('user-rights',$this->getRights($group));
    }
    function hashPassword($password)
    {
        $salt = bin2hex(random_bytes(16));
        $hashedPassword = hash('sha256', $password . $salt);
        
        return array('password' => $hashedPassword, 'salt' => $salt);
    }
    function verifyPassword($password, $hashedPassword, $salt)
    {
        $hashedInputPassword = hash('sha256', $password . $salt);
        if ($hashedInputPassword === $hashedPassword) {
            return true;
        } else {
            return false; 
        }
    }
    public function logOut(){
        $_SESSION['token'] = csrf::generateCSRFToken();
    }
    public function Auth($data) {
        if (!empty($data['email']) && !empty($data['password'])) {
            $user = $this->db->read(
                'users',
                ['id','password','user_salt'],
                [
                    "email"=>mb_strtolower($data['email']),
                ]
            );
            if ($user && $this->verifyPassword($data['password'], $user[0]['password'], $user[0]['user_salt'])){
                $user = $user[0]['id'];
                $data = [
                    'token' => $_SESSION['token']
                ];
                $this->db->update('users', $data,['id'=>$user]);
            }else
            return false;
        }else
            return false;
    }
    public function Register($data) {
        if (!empty($data['username']) && !empty($data['password'])&& !empty($data['email'])) {
            $data['username'] = mb_strtolower($data['username']);
            $hashedData = $this->hashPassword($data['password']);
            $password = $data['password'];
            if (!empty($data['ugroup'])) unset($data['ugroup']);
            $data['password'] = $hashedData['password'];
            $data['user_salt']= $hashedData['salt'];
            $data['token']= csrf::generateCSRFToken();
            $user = $this->db->read(
                'users',
                ['id'],
                [
                    "username"=>$data['username'],
                    "email"=>$data['email'],
                ]
            );
            if(!$user){
                $user = $this->db->create('users', $data);
                $this->Auth(['email' => $data['email'],'password'=>$password]);
            }
                
            else{
                return false;
            }
        }return false;
    }
}