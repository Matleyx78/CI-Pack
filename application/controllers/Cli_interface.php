<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Cli_interface extends CI_Controller 
{
    
    function __construct()
        {
        parent::__construct();
        
//  THIS CONTROLLER WORKS WITHOUT ION_AUTH  
        
        $this->load->library(array(
            'email',
        ));
        
        $this->load->helper(array(
            'date',
            )); 
        
        } 
    
    function index()
        {
        echo "Hello, World" . PHP_EOL;
        }       

    function testmail()
        {
            $message = "TEST MESSAGE";
            $this->email->from('you@example.com', 'Your Name', 'returned_emails@example.com');
            $this->email->to('TOMAIL@EXAMPLE.COM'); 
            $this->email->subject('Test mail from CLI');
            $this->email->message($message);
            $this->email->send();
        }
        
    }
