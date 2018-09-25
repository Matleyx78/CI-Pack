<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Example_logged extends Logged_Controller {

	public function __construct()
	{
		parent::__construct();

	}
	public function index()
	{
		$this->load->view('example/exa_logged');
	}
}
