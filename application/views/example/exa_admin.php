<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

$this->load->view('templates/header');

// FOR DEBUG UNCOMMENT:
//$arr = get_defined_vars();
//echo '<pre>';
//print_r($_ci_vars);
//var_dump($_SESSION);
//echo '</pre>';

?>

	<h1>Welcome to CodeIgniter! - ADMIN</h1>

	<div>
		<p>The page you are looking at is being generated dynamically by CodeIgniter.</p>

		<p>If you would like to edit this page you'll find it located at:</p>
		<code>application/views/welcome_message.php</code>

		<p>The corresponding controller for this page is found at:</p>
		<code>application/controllers/Welcome.php</code>

		<p>If you are exploring CodeIgniter for the very first time, you should start by reading the <a href="user_guide/">User Guide</a>.</p>
	</div>

	<p class="footer">Page rendered in <strong>{elapsed_time}</strong> seconds. <?php echo  (ENVIRONMENT === 'development') ?  'CodeIgniter Version <strong>' . CI_VERSION . '</strong>' : '' ?></p>
        
        
<?php $this->load->view('templates/footer');?>