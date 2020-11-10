<?php
namespace MyClass\User;

class User{
    /**
     * @var \CI_Controller
     */
    private $CI;

    public function __construct(){
        $this->CI =& get_instance();
        $this->CI->load->model('user_model');
    }

    /**
     * 寫入database account_info 
     * 
     * @param array $user_info
     */
    public function insertUser($user_info):array{
        $message = array('status' => 1, 'error_message' => '', 'user_id' => null);
        $user_info = array(
            'account' => (isset($user_info['account']) && $user_info['account']) ? mb_strtolower($user_info['account'],'UTF-8') : null,
            'password' => (isset($user_info['password']) && $user_info['password']) ? md5($user_info['password']) : null,
            'birthday' => (isset($user_info['birthday']) && $user_info['birthday']) ? date('Y-m-d', strtotime($user_info['birthday'])) : null,
            'email' => (isset($user_info['email']) && $user_info['email']) ? $user_info['email'] : null,
            'name' => (isset($user_info['name']) && $user_info['name']) ? $user_info['name'] : null,
            'sex' => (isset($user_info['sex']) && $user_info['sex']) ? $user_info['sex'] : null,
            'note' => (isset($user_info['note']) && $user_info['note']) ? $user_info['note'] : null
        );

        if($user_info['account'] === null){
            $message['status'] = 0;
            $message['error_message'] = '未輸入帳號';
            return $message;
        }

        //檢查是否有重複帳號
        $search_user_info = $this->CI->user_model->searchUser($user_info['account']);
        if($search_user_info->num_rows() <= 0){
            $user_id = $this->CI->user_model->insertUser($user_info);
            if(!$user_id){
                $message['status'] = 0;
                $message['error_message'] = '新增使用者發生錯誤';
            }
            else{
                $message['user_id'] = $user_id;
            }
        }
        else{
            $message['status'] = 0;
            $message['error_message'] = "已有新增過該使用者";
        }

        return $message; 
    }

    /**
     * reset user password
     * 
     * @param string $account 要更新的user帳號
     * @param array $update_info 要更新的資料
     * 
     * @return array
     */
    public function userResetPassword($account, $update_info):array{
        $message = array('status' => 1, 'error_message' => '', 'user_id' => null);
        if(!$account){
            $message['status'] = 0;
            $message['error_message'] = '未輸入帳號';
        }
        else{
            $search_user_info = $this->CI->user_model->searchUser($account);
            if($search_user_info->num_rows() <= 0){
                $message['status'] = 0;
                $message['error_message'] = '查無使用者帳號';
            }
            else{
                //設定不可更新欄位
                $check_fields = array('account', 'user_id');
                foreach($update_info as $update_key => $update_value){
                    if(in_array($update_key, $check_fields)){
                        unset($update_info[$update_key]);
                    }
                }

                if(count((array)$update_info) > 0){
                    $this->CI->user_model->userUpdate($account, $update_info);
                }
                else{
                    $message['status'] = 0;
                    $message['error_message'] = '無更新任何資料';
                }
            }
        }
        return $message;
    }

    /**
     * 刪除使用者
     * @param string $account
     * 
     * @return array
     */
    public function userDelete($account):array{
        $message = array('status' => 1, 'error_message' => '', 'user_id' => null);
        if(!$account){
            $message['status'] = 0;
            $message['error_message'] = '未輸入帳號';
        }
        else{
            $search_user_info = $this->CI->user_model->searchUser($account);
            if($search_user_info->num_rows() <= 0){
                $message['status'] = 0;
                $message['error_message'] = '查無使用者帳號';
            }
            else{
                $this->CI->user_model->userDelete($account);
            }
        }

        return $message;
    }

    /**
     * 查詢使用者資訊
     * 
     * @param string $account
     * 
     * @return array
     */
    public function searchUser($account = null):object{
        $user_list = $this->CI->user_model->searchUser($account);
        return $user_list;
    }
}