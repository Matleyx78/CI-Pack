<?php if (!defined('BASEPATH')) exit('No direct script access allowed');


class Commonfunction
    {
        protected $CI;
        public function __construct()
                {
                    $this->CI = & get_instance();
                    //$this->CI->load->model('ANY_model');                   
                }

    public function showHelloWorld()
        {
            return "Hello World";
        }
     
    public function cf_test($entry)
    {
        $this->CI->load->model('ANY_model');
        $data['test']['test2'] = $this->CI->ANY_model->ANY_FUNCTION();
        return $data;

    }
