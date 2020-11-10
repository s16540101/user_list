<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require_once 'User.php';

use MyClass\User\UserLogin;

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

    public function login(){
        $account = $this->input->post('account');
        $password = $this->input->post("password");

        $user_login = new UserLogin();
        $message = $user_login->loginUser($account, $password);
        echo json_encode($message);
    }

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
        
        $message = $this->insertUser($user_info);
        echo json_encode($message);
    }
}