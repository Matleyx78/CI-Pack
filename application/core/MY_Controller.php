<?php if (!defined('BASEPATH')) exit('No direct script access allowed');


 
class MY_Controller extends CI_Controller
{
  function __construct()
  {
    parent::__construct();

  }
}

class Guest_Controller extends MY_Controller        //Controller for Not Logged (free)
{
  function __construct()
  {
    parent::__construct();

  }
}

class Logged_Controller extends MY_Controller        //Controller for ALL Logged
{
  function __construct()
  {
    parent::__construct();
    if (!$this->ion_auth->logged_in())
        {
            
            redirect('auth/login');
        }
  }
}

class Admin_Controller extends MY_Controller        //Controller for Admin
{
  function __construct()
  {
    parent::__construct();
    if (!$this->ion_auth->logged_in())
        {
            
            redirect('auth/login');
        }
        else
            {
            if(!$this->ion_auth->is_admin())
                    {
                        $this->session->set_flashdata('flashError', 'This page is only for ADMIN');
                        redirect('example_logged');
                    }
            }
    }
  
}
 
class Members_Controller extends MY_Controller       //Controller for Group 2
{
  function __construct()
  {
    parent::__construct();
    if(! $this->ion_auth->in_group(2)){
			redirect('auth/login');
    }
  }
}