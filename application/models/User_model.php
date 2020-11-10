<?php
class User_model extends CI_Model{

    function __construnct(){
        parent::__construct();
    }

    function searchUser($account = null){
        $this->db->from('account_info');
        if($account !== null)
            $this->db->where("account", $account);
        return $this->db->get();
    }

    function insertUser($user_info){
        $this->db->insert('account_info', $user_info);
        return $this->db->insert_id();
    }
}
