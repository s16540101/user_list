<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Home extends CI_Controller {
    
	public function index(){
		$this->layout->view("home_index");
    }
    
    public function register(){
        $this->layout->view("register");
    }
}