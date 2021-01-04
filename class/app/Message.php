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
            $req = $this->db->get("message")->fields(['userid','message'])->where(['userid',"=",1]);
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
}