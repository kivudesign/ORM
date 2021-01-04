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
}