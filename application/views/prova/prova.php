<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

$this->load->view('templates/header');

$arr = get_defined_vars();
echo '<pre>';
print_r($_ci_vars);
echo '</pre>';

?>

<?php $this->load->view('templates/footer');?>