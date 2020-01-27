<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');


Class Prova_model extends CI_Model
{
    function __construct()
    {
        parent::__construct();
        $this->db = $this->load->database('default', TRUE);
        //$this->db2 = $this->load->database('adhoc', TRUE);
    }
    function get_all_conti()
    {
        return $this->db2->get_where('vistaagenti',array('AGTIPAGE'=>'C'))->result_array();
    }
    
    function get_all_agentis()
    {
        return $this->db->get_where('agenti',array('obsoleto'=>'0'))->result_array();
    }
    function get_all_colori()
    {
        return $this->db2->get('vistacolori')->result_array();
    }    
    
    function get_all_clientitutti()
    {
        return $this->db2->get('vista_clienti_tutti')->result_array();
    }
    
    function get_all_campi_tabella($tabella)
    {
        return $this->db->field_data($tabella);
    }    

    function get_errore_inserimento($params)
    {            
        $this->db->insert('fasidelcazzo',$params);
        return $this->db->insert_id();
    }
    function get_bolle_manoxxxx()
    {
        return $this->db->get('vistabolleimp')->result_array();
    }     
    }
