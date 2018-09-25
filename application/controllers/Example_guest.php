<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Example_guest extends Guest_Controller {

	public function __construct()
	{
		parent::__construct();

	}
	public function index()
	{
		$this->load->view('example/exa_guest');
	}
}
