<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Example_admin extends Admin_Controller {

	public function __construct()
	{
		parent::__construct();

	}
	public function index()
	{
		$this->load->view('example/exa_admin');
	}
}
