<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

$this->load->view('templates/header');

// FOR DEBUG UNCOMMENT:
//$arr = get_defined_vars();
//echo '<pre>';
//print_r($_ci_vars);
//var_dump($_SESSION);
//echo '</pre>';

?>

<h1><?php echo lang('deactivate_heading');?></h1>
<p><?php echo sprintf(lang('deactivate_subheading'), $user->username);?></p>

<?php echo form_open("auth/deactivate/".$user->id);?>

  <p>
  	<?php echo lang('deactivate_confirm_y_label', 'confirm');?>
    <input type="radio" name="confirm" value="yes" checked="checked" />
    <?php echo lang('deactivate_confirm_n_label', 'confirm');?>
    <input type="radio" name="confirm" value="no" />
  </p>

  <?php echo form_hidden($csrf); ?>
  <?php echo form_hidden(array('id'=>$user->id)); ?>

  <p><?php echo form_submit('submit', lang('deactivate_submit_btn'));?></p>

<?php echo form_close();?>        
        
<?php $this->load->view('templates/footer');?>