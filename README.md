# CI-Pack
CodeIgniter Pack: CRUD-BOOTSTRAP-ION_AUTH

Starter Pack for Codeigniter

CRUD Generator by any table in database (model, controller and 4 views)
  All file generated is ready for ION_AUTH and bootstrap theme
  
Unzip in the Codeigniter directory (please the index.php is in application/web directory)

index.php
  $system_path = '../system';
  $application_folder = '../application';
  
  
config.php
  $config['base_url'] = 'http://www.example.com';
  $config['index_page'] = '';
  
  
database.php
  Config your DB
  
  
sql
table


autoload.php
  $autoload['libraries'] = array('session','database','ion_auth');
  $autoload['helper'] = array('url',);
  
  
route.php
  $route['default_controller'] = 'example_guest';
  
