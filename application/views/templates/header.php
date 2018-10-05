<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); ?>
<!DOCTYPE html>
<html lang="it">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>My Codeigniter</title>
                
        <link rel="stylesheet" href="<?php echo base_url(); ?>assets/bootstraporiginal/css/bootstrap.min.css">
        <link rel="stylesheet" href="<?php echo base_url(); ?>assets/css-personal/bootstrap-submenu.min.css">
        <link rel="stylesheet" href="<?php echo base_url(); ?>assets/css-personal/docs.css">
        <link rel="stylesheet" href="<?php echo base_url(); ?>assets/css-personal/bootstrap-datetimepicker.css">
        <link rel="stylesheet" href="<?php echo base_url(); ?>assets/css-personal/personal.css">
        
        <script type="text/javascript" src="<?php echo base_url(); ?>assets/jqueryoriginal/jquery.min.js"></script>        
        <script type="text/javascript" src="<?php echo base_url(); ?>assets/js-personal/moment-with-locales.min.js"></script>
        <script type="text/javascript" src="<?php echo base_url(); ?>assets/js-personal/bootstrap-datetimepicker.js"></script>
                
    </head>
    
    
    <body>
    <nav class="navbar navbar-default navbar-fixed-top navbar-inverse">
      <div class="container">
        <div class="navbar-header">
          <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <a class="navbar-brand" href="<?php echo base_url(); ?>">My Codeigniter</a>
        </div>
        <div id="navbar" class="navbar-collapse collapse">
            

          <ul class="nav navbar-nav">
                  <?php 
                  if($this->ion_auth->logged_in())
                      {
                      echo "<p class=\"navbar-text\">".$this->session->email." </p>";
                      echo "<li>".anchor('auth/logout', ' Logout','><span class="glyphicon glyphicon-log-out"></span')."</li>";
                      }else
                          {
                          echo "<li>".anchor('auth/login', ' Login','><span class="glyphicon glyphicon-log-out"></span')."</li>";
                          };
                   ?>
              <?php if($this->ion_auth->is_admin()){?>               
                            <li class="dropdown">
                                <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Admin <span class="caret"></span></a>
                                <ul class="dropdown-menu">
                                    <li><?php echo anchor('example_admin', 'Example Admin');?></li>
                                    <li role="separator" class="divider"></li>
                                    <li><?php echo anchor('crud', 'Crud Generator');?></li>
                                    <li role="separator" class="divider"></li>
                                    <li><?php echo anchor('auth', 'User List');?></li>
                                    <li><?php echo anchor('ControllerAdmin', 'AdminMenu1');?></li>
                                    <li><?php echo anchor('ControllerAdmin', 'AdminMenu1');?></li>
                                    <li class="dropdown-submenu">
                                        <a tabindex="0">SubMenu1</a>
                                        <ul class="dropdown-menu">
                                            <li><?php echo anchor('ControllerAdmin', 'AdminMenu1');?></li>
                                        </ul>
                                    </li>
                                    <li class="dropdown-submenu">
                                        <a tabindex="0">SubMenu1</a>
                                        <ul class="dropdown-menu">
                                            <li><?php echo anchor('ControllerAdmin', 'AdminMenu1');?></li>
                                        </ul>
                                    <li role="separator" class="divider"></li>
                                    <li><?php echo anchor('ControllerAdmin', 'AdminMenu1');?></li>
                                    <li><?php echo anchor('ControllerAdmin', 'AdminMenu1');?></li>
                                    </li>
                                </ul>
                            </li>
                            


              <?php }?>
                                      </ul>
<ul class="nav navbar-nav navbar-right">
              

                            <li class="dropdown">
                                <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">FreeMenu1 <span class="caret"></span></a>
                                <ul class="dropdown-menu">
                                    <li><?php echo anchor('example_guest', 'Example Guest');?></li>                              
                                </ul>
                            </li>
<?php
if($this->ion_auth->in_group(2)){?>
                            <li class="dropdown">
                                <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Only Member menu <span class="caret"></span></a>
                                <ul class="dropdown-menu">
                                    <li><?php echo anchor('example_members', 'Example Member');?></li>
                                    <li role="separator" class="divider"></li>
                                    <li><?php echo anchor('example_members', 'Example Member');?></li>
                                </ul>
                            </li>
<?php
} 
if($this->ion_auth->logged_in()){?>
                            <li class="dropdown">
                                <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">All logged menu <span class="caret"></span></a>
                                <ul class="dropdown-menu">
                                    <li><?php echo anchor('example_logged', 'Example logged');?></li>
                                    <li role="separator" class="divider"></li>
                                    <li><?php echo anchor('example_logged', 'Example logged');?></li>
                                </ul>
                            </li>                                                     
<?php } ?>
          </ul>
        </div><!--/.nav-collapse -->
      </div>
    </nav>

<div class="row"> 
<?php
if($this->session->flashdata('flashSuccess')):?><div class="alert alert-success"><?php echo $this->session->flashdata('flashSuccess'); ?></div>
<?php endif; 
if($this->session->flashdata('flashError')):?><div class="alert alert-danger"><?php echo $this->session->flashdata('flashError'); ?></div>
<?php endif;
if($this->session->flashdata('flashInfo')):?><div class="alert alert-info"><?php echo $this->session->flashdata('flashInfo'); ?></div>
<?php endif;
if($this->session->flashdata('flashWarning')):?><div class="alert alert-warning"><?php echo $this->session->flashdata('flashWarning'); ?></div>
<?php endif ;?>                    
</div>            
<div class="container-fluid">
                
                
            
