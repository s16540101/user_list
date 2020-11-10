<?php

namespace MyClass\User;

class UserLogin{
    /**
     * @var \CI_Controller
     */
    private $CI;

    function __construct(){
        $this->CI =& get_instance();
        $this->CI->load->model('user_model');
    }

    /**
     * 檢查使用者帳號密碼
     * 
     * @param string $input_account 輸入的帳號
     * @param string $input_password 輸入的密碼
     * 
     * @return array
     */
    public function loginUser($input_account, $input_password):array{
        $message = array('status' => 1, 'error_message' => '');
        $user_info = $this->CI->user_model->searchUser($input_account)->row_array();
        if(count((array) $user_info) > 0){
            if(md5($input_password) == $user_info['password']){
                $this->_initSession($user_info);    
            }
            else{
                $message['status'] = 0;
                $message['error_message'] = '帳號或密碼輸入錯誤';
            }
        }
        else{
            $message['status'] = 0;
            $message['error_message'] = '查無此帳號';

        }
        return $message;
    }

    /**
     * 登入成功後寫入SESSION
     * 
     * @param array $user_info
     */
    private function _initSession($user_info):void{
        $this->CI->session->set_userdata('account', $user_info['account']);
        $this->CI->session->set_userdata('user_id', $user_info['user_id']);
        $this->CI->session->set_userdata('is_login', TRUE);
    }
}