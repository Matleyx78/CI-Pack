<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');


class Prova extends Loggeds_Controller 
{
    function __construct()
    {
        parent::__construct();
        $this->load->model('Prova_model');
        //$this->load->model('Adhoc_bolle_model');
        $this->load->helper(
                array('url', 'date','file')
            );
        $this->load->library('Commonfunction');
    } 

    /*
     * Listing of clientiadhocs
     */
    function index()
    {
                    $this->load->model('Clientiadhoc_model');
            $data['cliente'] = $this->Clientiadhoc_model->get_clientiadhoc("04500548");
        $this->load->model('Prezzisconti_model');
        $data['adhoc'] = "Dec 31 2013 12:00:00:000AM";
        $data['nice'] = nice_date($data['adhoc']);
        $data['feries'] = array(    2017 =>  array(  1=>array(2,3,4,5), 
                                    4=>array(14,24),
                                    6=>array(1),
                                    7=>array(31),
                                    8=>array(1,2,3,4,7,8,9,11,14,16,17,18,21,22,23,24,25),
                                    12=>array(27,28,29)                                      
                                ),
                                    2018 =>  array(  1=>array(2,3,4,5), 
                                    4=>array(30),
                                    8=>array(6,7,8,9,13,14,16,17,20,21,22,23,24,27,28,29,30,31),
                                    11=>array(2),
                                    12=>array(24,27,28,31),                                     
                            ),
                    );
//        $data['ad-to-un'] = adhoc_to_unix($data['adhoc']);
//        $data['ad-to-my'] = adhoc_to_mysql($data['adhoc']);
//        $data['mysql'] = $data['ad-to-my'];
//        $data['tounix'] = mysql_to_unix($data['mysql']);
        $data['unixtouman'] = unix_to_human($data['nice'], true, 'eu');
        $data['unixtouman2'] = unix_to_human($data['nice'], false, 'us');
        $data['mysqltouman1'] = mysql_to_human1($data['unixtouman']);
        //$data['listino'] = $this->Prezzisconti_model->articoli_vista_pdf();
        //$data['env'] = getenv('CI_ENV');
        $risultato['txt'] = $this->load->view('prova/prova',$data,TRUE);
        $this->load->view('prova/prova',$risultato);
    }
    
    function add_commessa_fase() 
        {
        $this->load->model('Adhoc_fasi_model');
        $commessa = '0000954204';
        $com = $commessa;
        $fase = '142';
        $data['orario'] = date("20190924 11:36:36");
        $data['sonopassato'] = "no";
        $data['risultato'] = $this->Adhoc_fasi_model->get_singola_fase_daadhoc($commessa,$fase);
        
        if (!$data['risultato'])
            {
                    $lastprow = $this->Adhoc_fasi_model->get_last_cprownum_daadhoc($commessa);
                    
                    if ($lastprow['CPROWNUM'])
                        {
                            $actprow = $lastprow['CPROWNUM'] + 10;
                        }else
                            {
                                $actprow = "10";
                            }
                            
                    $params = array(                
                        'f_commessa' => $com,
                        'CPROWNUM' => $actprow,
                        'f_fase' => $fase,
                        'f_data' => $data['orario'],
                        'f_cesta' => "",
                        'f_ordine' => "",
                        'f_flcanc' => "",
                        'cpccchk' => "zybqkqngfc",                    
                    );
    
        $data['fase'] = $this->Adhoc_fasi_model->add_fase($params);
        $data['sonopassato'] = "si";
            }
              

            $this->load->view('prova/prova',$data);
            
        }     


        function provaordap_anno()
    {

        $this->load->model('Ord_ap_ah_model');
        $this->load->model('Ord_ap_man_model');
        $adesso = now_to_datetime();
        $oggi = ultimo_del_mese_precedente($adesso);
        $anno = substr($oggi, 0, 4);
        $mese = substr($oggi, 5, 2);

        $anni_da_fare = array(
            array('ann'=> $anno,"compl" => $oggi,),
//            array('ann'=> $anno-1,"compl" => date("Y-m-d H:i:s",(mysql_to_unix($oggi) - (365 * 24 * 60 * 60))),),
//            array('ann'=> $anno-2,"compl" => date("Y-m-d H:i:s",(mysql_to_unix($oggi) - (365 * 24 * 60 * 60 * 2))),),
//            array('ann'=> $anno-3,"compl" => date("Y-m-d H:i:s",(mysql_to_unix($oggi) - (365 * 24 * 60 * 60 * 3))),),
            );
        
        foreach ($anni_da_fare as $af){
        
        $data['calcoloordap'] = $this->Ord_ap_ah_model->get_all_op_ah_anno_mese($af['ann'],$af['compl']);
        foreach ($data['calcoloordap'] as $ca)
            {
                if (!isset($data['ris1'][$ca['tipoprof1_oah']][$af['ann']]))
                    {
                        $data['ris1'][$ca['tipoprof1_oah']][$af['ann']] = 0;
                    }
                $data['ris1'][$ca['tipoprof1_oah']][$af['ann']] = $data['ris1'][$ca['tipoprof1_oah']][$af['ann']] + $ca['mvqtamov_oah'] - $ca['kgcarico_oah'];
            }
        
        $data['calcoloordap_man'] = $this->Ord_ap_man_model->get_all_op_man_anno_mese($af['ann'],$af['compl']);
        foreach ($data['calcoloordap_man'] as $cb)
            {
                if (!isset($data['ris1'][$cb['tipoprof1_oam']][$af['ann']]))
                    {
                        $data['ris1'][$cb['tipoprof1_oam']][$af['ann']] = 0;
                    }
                $data['ris1'][$cb['tipoprof1_oam']][$af['ann']] = $data['ris1'][$cb['tipoprof1_oam']][$af['ann']] + $cb['mvqtamov_oam'] - $cb['kgcarico_oam'];
            }            
        }   
            
            
            
        unset($data['calcoloordap']);
                unset($data['calcoloordap_man']);
        $this->load->view('prova/prova',$data);
    }	
    
    
        function send_tlg_msg()
        {
           $nome = 'Matteo';
           
                        $consentiti2 = array(
                array("chat_no" => 571562922, "name" => "Matteo",   "email" => "matteo.lenzi@geal.it",),
                array("chat_no" => 734220594, "name" => "Marcello", "email" => "marcello.corrarati@geal.it",),
                array("chat_no" => 709359261, "name" => "Pietro",   "email" => "pietro.lenzi@geal.it",),
                array("chat_no" => 705998983, "name" => "Vito",   "email" => "vito.chiarle@geal.it",),
                array("chat_no" => 869380550, "name" => "Gianni",   "email" => "gianni.gauttieri@geal.it",),
                array("chat_no" => 828002444, "name" => "Giuliano",   "email" => "giuliano.minetti@geal.it",),
                );
            $data['key'] = array_search($nome, array_column($consentiti2, 'name'));
                        
            if (is_int($data['key']))
                {
                    $data['chatId'] = $consentiti2[$data['key']]['email'];
                }else
                    {
                        $data['chatId'] = 'no';
                    }

        $this->load->view('prova/prova',$data);           
        }
        
        function righecomma()
            {
                    $this->load->model('Adhoc_remote_model');
                    $this->load->model('Adhoc_fasi_model');
                    $commessa = '0000972995';
                    //$this->load->model('Inventario_in_lav_model');
                    //$commessa = str_pad((string)$this->input->post('invilav_aggiungi'), 10, "0", STR_PAD_LEFT);
                    //$data['righe'] = $this->Adhoc_remote_model->search_adhoc_righeord_by_comm($commessa);
                    
                    $data['pistolate'] = $this->Adhoc_fasi_model->get_all_fasi_commessa_daadhoc($commessa);

            $this->load->view('prova/prova',$data);           
            }  
        function sanifica()
            {
                    $stringa = '0061/001  % ?   #  . = ';
                    $data['iniziale'] = 'X'.$stringa.'X';
                    $data['sana'] = mt_str_to_url($stringa);
                    $data['ritorno'] = 'X'.mt_url_to_str($data['sana']).'X';


            $this->load->view('prova/prova',$data);           
            }            
            
            
    }
