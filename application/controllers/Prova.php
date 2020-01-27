<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Prova extends Loggeds_Controller 
{
    function __construct()
        {
            parent::__construct();
            $this->load->model('Prova_model');
        } 

    function index()
        {
            $data['test'] = array();
            $this->load->view('prova/prova',$data);
        }
}
