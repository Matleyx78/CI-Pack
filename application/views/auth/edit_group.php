<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

$this->load->view('templates/header');

// FOR DEBUG UNCOMMENT:
//$arr = get_defined_vars();
//echo '<pre>';
//print_r($_ci_vars);
//var_dump($_SESSION);
//echo '</pre>';

?>

<h1><?php echo lang('edit_group_heading');?></h1>
<p><?php echo lang('edit_group_subheading');?></p>

<div id="infoMessage"><?php echo $message;?></div>

<?php echo form_open(current_url());?>

      <p>
            <?php echo lang('edit_group_name_label', 'group_name');?> <br />
            <?php echo form_input($group_name);?>
      </p>

      <p>
            <?php echo lang('edit_group_desc_label', 'description');?> <br />
            <?php echo form_input($group_description);?>
      </p>

      <p><?php echo form_submit('submit', lang('edit_group_submit_btn'));?></p>

<?php echo form_close();?>        
        
<?php $this->load->view('templates/footer');?>