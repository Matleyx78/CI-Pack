<?php if (!defined('BASEPATH')) exit('No direct script access allowed');


Class Prova_model extends CI_Model
{
    function __construct()
        {
            parent::__construct();
            $this->db = $this->load->database('default', TRUE);
        }
        
    function test_prova()
        {
            return $this->db->get_where('TABELLA',array('CAMPO'=>'xxx'))->result_array();
        }

}
