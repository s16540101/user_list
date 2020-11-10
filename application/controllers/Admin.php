<?php
require_once "Base.php";
class Admin extends Base{
    function __construct(){
        parent::__construct();
        if($this->session->userdata('is_login') !== TRUE)
            header("Location:".base_url());
    }

    public function index(){
        $user_list = $this->user_class->searchUser()->result_array();
        $data['user_list'] = $user_list;
        $this->layout->view("user_list", $data);
    }

    public function Logout(){
        $this->session->sess_destroy();
    }

    public function userResetPassword(){
        $request_method = $this->input->server(array('REQUEST_METHOD'));
        $account = null;
        //處理 REQUEST_METHOD 為 put部分
        if($request_method['REQUEST_METHOD'] == 'PUT'){
            $put = array();
            parse_str(file_get_contents("php://input"), $put);
            $account = (isset($put['account'])) ? $put['account'] : null;
        }
        $update_info = array("password" => $account);
        $message = $this->user_class->userResetPassword($account, $update_info);
        echo json_encode($message);
    }

    public function userDelete(){
        $request_method = $this->input->server(array('REQUEST_METHOD'));
        $account = null;
        //處理 REQUEST_METHOD 為 put部分
        if($request_method['REQUEST_METHOD'] == 'DELETE'){
            $delete = array();
            parse_str(file_get_contents("php://input"), $delete);
            $account = (isset($delete['account'])) ? $delete['account'] : null;
        }

        $message = $this->user_class->userDelete($account);

        echo json_encode($message);
    }
}
?>