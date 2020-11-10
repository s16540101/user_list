<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require_once 'User.php';
class Home extends User {
    
    function __construct(){
        parent::__construct();
        $this->load->model('user_model', 'user');
    }

	public function index(){
		$this->layout->view("home_index");
    }
    
    public function register(){
        $this->layout->view("register");
    }

    /**
     * 登入
     */
    public function login(){
        $account = $this->input->post('account');
        $password = $this->input->post("password");

        $message = $this->user_login_class->loginUser($account, $password);
        echo json_encode($message);
    }

    /**
     * register user
     */
    public function userInsert(){
        $user_info = array(
                        "account" => $this->input->post("account"),
                        "password" => $this->input->post("password"),
                        "name" => $this->input->post("user_name"),
                        "sex" => $this->input->post("user_sex"),
                        "birthday" => $this->input->post("user_birthday"),
                        "email" => $this->input->post("user_email"),
                        "note" => $this->input->post("user_note")
                    );
        
        $message = $this->user_class->insertUser($user_info);
        echo json_encode($message);
    }
}