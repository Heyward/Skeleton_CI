<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Home extends MY_Controller 
{
    public function setListParameters(){}
    
	public function index()
	{
		$data = array();
		$data["title"] = "Home";
		
		$this->view('home', $data);
	}

	public function forbbiden()
	{
		$data = array();
		$data["title"] = "Forbbiden";
		$data["menu"]   = "";
		
		$this->view('forbbiden', $data);
	}
}
