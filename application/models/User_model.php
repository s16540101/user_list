<?php
class User_model extends CI_Model{

    function __construnct(){
        parent::__construct();
    }

    /**
     * 查詢使用者
     *
     * @param string $account
     * 
     */
    function searchUser($account = null){
        $this->db->from('account_info');
        if($account !== null)
            $this->db->where("account", $account);
        return $this->db->get();
    }

    /**
     * insert 使用者
     *
     * @param array $user_info
     * 
     */
    function insertUser($user_info){
        $this->db->insert('account_info', $user_info);
        return $this->db->insert_id();
    }

    /**
     * update 使用者資訊
     *
     * @param string $account 使用者帳號
     * @param array $update_info 要更新的使用者欄位
     * 
     * 
     */
    function userUpdate($account, $update_info){
        $this->db->where("account", $account);
        $this->db->update("account_info", $update_info);
    }

    /**
     * 刪除使用者
     *
     * @param string $account
     * 
     */
    function userDelete($account){
        $this->db->where("account", $account);
        $this->db->delete("account_info");
    }

    /**
     * 批次刪除使用者
     * 
     * @param array $account
     */
    function userBatchDelete($account){
        $this->db->where_in('account', $account);
        $this->db->delete("account_info");
    }
}
