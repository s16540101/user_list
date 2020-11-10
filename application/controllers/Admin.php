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
}
?>