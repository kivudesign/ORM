<?php
class Message{
    private $db;

    function __construct()
    {
        $this->db=DB::getInstance();
    }
    function getMessage(array $where)
    {
        try {
            // count number of result from table with conditions
            $count =$this->db->count("message")->where($where)->result();
            // get selected field from selected table with condition
            $req = $this->db->get("message")->fields(['userid','message'])->where($where)->result();
            return [
                "total"=>$count,
                "result"=>$req,
            ];
        } catch (Exception $ex) {
            echo $ex->getMessage();
        }
    }
    function sendMessage(array $data)
    {
        try {
            $this->db->insert("message")->fields($data)->result();
            if ($this->db->error()) {
                throw new Exception("impossible d'enregister ce message");
            }
            return $this->db->lastId();
        } catch (Exception $ex) {
            echo $ex->getMessage();
        }
    }
    function deleteMessage(array $data)
    {
        try {
            $req=$this->db->delete("message")->where($data)->result();
            if ($this->db->error()) {
                throw new Exception($this->db->error());
            }
            return ["row deleted"=>$this->db->countAll()];
        } catch (Exception $ex) {
            echo $ex->getMessage();
        }
    }
    function SelectOneMessage(){
        try{
            $req = $this->db->query("select * from message join users on users.id=message.userid");
            if($req->error()){
                throw new Exception($req->error());
            }
            return $req->result();
        }catch(Exception $ex){
            echo $ex->getMessage();
        }
    }
    function updateMessage(array $data,array $where){
        try{
            $req = $this->db->update("message")->fields($data)->where($where)->result();
            if($this->db->error()){
                throw new Exception($req->error());
            }
            return ["row updated"=>$this->db->count()];
        }catch(Exception $ex){
            echo $ex->getMessage();
        }
    }
}