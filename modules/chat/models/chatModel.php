<?php
class chatModel extends Model{
    public function getChats(){
        return $this->db->read('chats');
    }
    public function getMessages($chat){
        $message = $this->db->read('chat_messages',[], ['chat' => $chat], 100, 'id DESC');
        foreach ($message as $key => $value) {
            $message[$key]['username']=$this->db->read('users',['username'], ['id' => $value['user']])[0]['username'];
            $message[$key]['time'] = date('H:i:s', $value['time']);
        }
        return $message;
    }
    public function createChat($name){
        $Data = [
            'name' => $name,
        ];
        $this->db->create('chats', $Data);
        return True;
    }
    public function search($data){
        $pdo=$this->db->getConnection();
        $sql = "SELECT * FROM `chat_messages` WHERE `message` LIKE '%$data%'";
        $statement = $pdo->prepare($sql);
        $statement->execute();
        $results = $statement->fetchAll(PDO::FETCH_ASSOC);
        return $results;
    }
    public function addMessage($message,$chat,$user){
        $timestamp = time();

        // Преобразование метки времени в объект DateTime
        $dateTime = new DateTime();
        $timestamp = $dateTime->getTimestamp();
        $Data = [
            'user' => $user,
            'chat' => $chat,
            'message' => $message,
            'time' => $timestamp
        ];
        $this->db->create('chat_messages', $Data);
        return True;
    }
}