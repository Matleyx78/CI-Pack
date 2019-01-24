<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); ?>
<!DOCTYPE html>
<html lang="it">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="icon" type="image/png" href="<?php echo base_url(); ?>img/ci-icon.png" />
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

         <?php echo $this->menu->buildMenu();?>       
        
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
                
                
            
