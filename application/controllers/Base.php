<?php
use MyClass\User\User as NameUser;
use MyClass\User\UserLogin;
class Base extends CI_Controller{
    protected $user_class;
    protected $user_login;

    public function __construct(){
        parent::__construct();
        $this->user_class = new NameUser();
        $this->user_login_class = new UserLogin();
    }
}