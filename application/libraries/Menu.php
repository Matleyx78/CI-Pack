<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Menu {
    private $ci;                // for CodeIgniter Super Global Reference.
//    private $id_menu        = 'id="menu"';
//    private $class_menu        = 'class="menu"';
//    private $class_parent    = 'class="parent"';
//    private $class_last        = 'class="last"';
    // --------------------------------------------------------------------
    /**
     * PHP5        Constructor
     *
     */
    function __construct()
    {
        $this->ci =& get_instance();    // get a reference to CodeIgniter.
    }                
    function buildMenu()
        {
            $htmlmenu = "";
                    
            $htmlmenu .= $this->part1();
            $htmlmenu .= $this->part2();
            $htmlmenu .= $this->part3();
            $htmlmenu .= "                                      </ul>";
            $htmlmenu .= $this->part4();
            $htmlmenu .= $this->part5();
            $htmlmenu .= $this->part6();
            $htmlmenu .= $this->part7();
            
            return $htmlmenu;
        }
    
    function part1()
        {
                        $part1 = "
    <nav class=\"navbar navbar-default navbar-fixed-top navbar-inverse\">
      <div class=\"container\">
        <div class=\"navbar-header\">
          <button type=\"button\" class=\"navbar-toggle collapsed\" data-toggle=\"collapse\" data-target=\"#navbar\" aria-expanded=\"false\" aria-controls=\"navbar\">
            <span class=\"sr-only\">Toggle navigation</span>
            <span class=\"icon-bar\"></span>
            <span class=\"icon-bar\"></span>
            <span class=\"icon-bar\"></span>
          </button>
          <a class=\"navbar-brand\" href=".base_url().">My Codeigniter</a>
        </div>
        <div id=\"navbar\" class=\"navbar-collapse collapse\">
            

          <ul class=\"nav navbar-nav\">
";
                        
            return $part1;            
        }

    function part2()
        {
            $part2 ="";
            if($this->ci->ion_auth->logged_in())
                {
$part2 = "                          <p class=\"navbar-text\">".$this->ci->session->email." </p>
                                <li>".anchor('auth/logout', ' Logout','><span class="glyphicon glyphicon-log-out"></span')."</li>
";
                }else
                    {
$part2 = "                          <li>".anchor('auth/login', ' Login','><span class="glyphicon glyphicon-log-out"></span')."</li>
";                    
                    };        
                        
            return $part2;            
        }

    function part3()
        {
            $part3 ="";
            if($this->ci->ion_auth->is_admin())
                {
$part3 = "                          <li class=\"dropdown\">
                                <a href=\"#\" class=\"dropdown-toggle\" data-toggle=\"dropdown\" role=\"button\" aria-haspopup=\"true\" aria-expanded=\"false\">Admin <span class=\"caret\"></span></a>
                                <ul class=\"dropdown-menu\">
                                    <li>".anchor('example_admin', 'Example Admin')."</li>
                                    <li role=\"separator\" class=\"divider\"></li>
                                    <li>".anchor('crud', 'Crud Generator')."</li>
                                    <li role=\"separator\" class=\"divider\"></li>
                                    <li>".anchor('auth', 'User List')."</li>
                                    <li>".anchor('ControllerAdmin', 'AdminMenu1')."</li>
                                    <li>".anchor('ControllerAdmin', 'AdminMenu1')."</li>
                                    <li class=\"dropdown-submenu\">
                                        <a tabindex=\"0\">SubMenu1</a>
                                        <ul class=\"dropdown-menu\">
                                            <li>".anchor('ControllerAdmin', 'AdminMenu1')."</li>
                                        </ul>
                                    </li>
                                    <li class=\"dropdown-submenu\">
                                        <a tabindex=\"0\">SubMenu1</a>
                                        <ul class=\"dropdown-menu\">
                                            <li>".anchor('ControllerAdmin', 'AdminMenu1')."</li>
                                        </ul>
                                    <li role=\"separator\" class=\"divider\"></li>
                                    <li>".anchor('ControllerAdmin', 'AdminMenu1')."</li>
                                    <li>".anchor('ControllerAdmin', 'AdminMenu1')."</li>
                                    </li>
                                </ul>
                            </li>
";
                }        
                        
            return $part3;            
        }

    function part4()
        {
            $part4 ="";
$part4 = "                          <ul class=\"nav navbar-nav navbar-right\">
                <li class=\"dropdown\">
                    <a href=\"#\" class=\"dropdown-toggle\" data-toggle=\"dropdown\" role=\"button\" aria-haspopup=\"true\" aria-expanded=\"false\">FreeMenu1 <span class=\"caret\"></span></a>
                    <ul class=\"dropdown-menu\">
                        <li>".anchor('example_guest', 'Example Guest')."</li>                              
                    </ul>
                </li>
";
        
                        
            return $part4;            
        }

    function part5()
        {
            $part5 ="";
            if($this->ci->ion_auth->in_group(2))
                {
$part5 = "                  <li class=\"dropdown\">
                                <a href=\"#\" class=\"dropdown-toggle\" data-toggle=\"dropdown\" role=\"button\" aria-haspopup=\"true\" aria-expanded=\"false\">Only Member menu <span class=\"caret\"></span></a>
                                <ul class=\"dropdown-menu\">
                                    <li>".anchor('example_members', 'Example Member')."</li>
                                    <li role=\"separator\" class=\"divider\"></li>
                                    <li>".anchor('example_members', 'Example Member')."</li>
                                </ul>
                            </li>
";
                }        
                        
            return $part5;            
        }

    function part6()
        {
            $part6 ="";
            if($this->ci->ion_auth->logged_in())
                {
$part6 = "              <li class=\"dropdown\">
                                <a href=\"#\" class=\"dropdown-toggle\" data-toggle=\"dropdown\" role=\"button\" aria-haspopup=\"true\" aria-expanded=\"false\">All logged menu <span class=\"caret\"></span></a>
                                <ul class=\"dropdown-menu\">
                                    <li>".anchor('example_logged', 'Example logged')."</li>
                                    <li role=\"separator\" class=\"divider\"></li>
                                    <li>".anchor('example_logged', 'Example logged')."</li>
                                </ul>
                            </li>
";
                }        
                        
            return $part6;            
        }

    function part7()
        {
            $part7 ="";
$part7 = "                                    </ul>
        </div><!--/.nav-collapse -->
      </div>
    </nav>
";
        
                        
            return $part7;            
        }

        
}
