<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

$this->load->view('templates/header');

// FOR DEBUG UNCOMMENT:
//$arr = get_defined_vars();
//echo '<pre>';
//print_r($_ci_vars);
//var_dump($_SESSION);
//echo '</pre>';

?>
<div id="container" style="text-align:center">
    <br/>
        <h1>My Codeigniter</h1>
        <h1><?php echo lang('login_heading');?></h1>
        <p><?php echo lang('login_subheading');?></p>
        <br/>
        <div id="infoMessage"><?php echo $message;?></div>
        <?php echo form_open("auth/login");?>

        <p><?php echo lang('login_identity_label', 'identity');?></p>
        <p><?php echo form_input($identity);?></p>
        <p><?php echo lang('login_password_label', 'password');?></p>
        <p><?php echo form_input($password);?></p>
        <p><?php echo lang('login_remember_label', 'remember');?>
           <?php echo form_checkbox('remember', '1', FALSE, 'id="remember"');?></p>
        <p><?php echo form_submit('submit', lang('login_submit_btn'));?></p>

        <?php echo form_close();?>

        <p><a href="forgot_password"><?php echo lang('login_forgot_password');?></a></p> 
</div>
        
<?php $this->load->view('templates/footer');?>