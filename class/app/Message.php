<?php
class Message{
    private $db;

    function __construct()
    {
        $this->db=DB::getInstance();
    }
    function getMessage()
    {
        try {
            $req = $this->db->get("message")->where(["id","=","1"])->fields(['userid','message'])->where(['userid',"=",1]);
            return $req->result();
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
            $this->db->delete("message")->where($data)->result();
            if ($this->db->error()) {
                throw new Exception($this->db->error());
            }
            return true;
        } catch (Exception $ex) {
            echo $ex->getMessage();
        }
    }
}