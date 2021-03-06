<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Crud extends Loggeds_Controller
{

    private $tname; //table name
    private $controllername; // controller name
    private $ind_htm_name;
    private $fname; // form name
    private $sname; // singol name   
    private $modelname; // model tite
    private $obsolfield; // model tite
    private $listviewname;
    private $list_viewpagname;
    private $create_viewname;
    private $edit_viewname;
    private $viewdetail_viewname;
    private $tbl_pk;
    private $auther          = "Chiuna";
    private $auther_mail     = "info@chiuna.it";
    private $created_date;
    private $header          = 'header';
    private $footer          = 'footer';
    private $header_data     = '';
    private $footer_data     = 'footer';
    private $controller_data = '';
    private $ind_htm_data    = '';
    private $model_data      = '';
    private $create_data     = '';
    private $edit_data       = '';
    private $viewdetail_data = '';
    private $list_data       = '';
    private $listp_data      = '';
    private $library_list    = array("form_validation", "session");
    private $helper_list     = array("url");

    public function __construct() {
        parent::__construct();


        $this->load->library('form_validation');
        $this->dbcrud       = $this->load->database('default', TRUE);
        $this->load->library('zip');
        $this->load->helper('url');
        $this->load->helper('file');
        $this->load->helper('download');
        $this->created_date = date('Y-m-d ');
    }

    public function index() {
        $db_name        = $this->dbcrud->database;
        $result         = $this->dbcrud->query('SELECT TABLE_NAME AS TABLES FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA="' . $db_name . '";');
        $result         = $result->result_array();
        $data['name1']  = $db_name;
        $data['result'] = $result;

        $this->load->view('crud/index', $data);
    }

    public function generator() {
        $db_name = $this->dbcrud->database;
        $data    = '';
        $this->form_validation->set_rules('tname', 'Table Name', 'required');
        $this->form_validation->set_rules('cname', 'Controller Name', 'required');
        $this->form_validation->set_rules('fname', 'Title Name', 'required');
        if ($this->form_validation->run() === FALSE)
        {
            redirect($this->index());
        }
        else
        {
            if ($this->input->post("obsofield") != '' OR $this->input->post("obsofield") != NULL)
            {
                $campoobsolescenza = $this->input->post("obsofield");
            }
            else
            {
                $campoobsolescenza = 'CAMPOOBSOLESCENZA';
            }
            $tblowercase               = strtolower($this->input->post("tname"));
            $this->tname               = $tblowercase;
            $cname                     = $this->input->post("cname");
            $this->obsolfield          = $campoobsolescenza;
            $this->controllername      = str_replace(' ', '_', $cname);
            $this->fname               = 'pl_'.$this->input->post("fname");
            $this->sname               = 'si_'.$this->input->post("fname");
            $this->modelname           = $this->tname . '_model';
            $this->ind_htm_name        = 'index';
            $this->listviewname        = 'listall_' . $this->controllername;
            $this->list_viewpagname    = 'list_' . $this->controllername;
            $this->create_viewname     = 'create_' . $this->sname;
            $this->edit_viewname       = 'edit_' . $this->sname;
            $this->viewdetail_viewname = 'view_' . $this->sname;
            $fields                    = $this->dbcrud->field_data($this->tname);
            if (empty($fields))
            {
                die("Table not existing");
            }
            foreach ($fields as $field)
            {
                $field_name = $field->name;
                if ($field->primary_key == 1)
                {
                    $this->tbl_pk = $field_name;
                }
            }

            // Foreign Keys
            $query2      = $this->dbcrud->query('SELECT * FROM information_schema.TABLE_CONSTRAINTS WHERE information_schema.TABLE_CONSTRAINTS.CONSTRAINT_TYPE = \'FOREIGN KEY\' AND information_schema.TABLE_CONSTRAINTS.TABLE_SCHEMA = \'' . $db_name . '\' AND information_schema.TABLE_CONSTRAINTS.TABLE_NAME = \'' . $this->tname . '\'');
            $foreignkeys = $query2->result_array();
            if (isset($foreignkeys[0]))
            {
                foreach ($foreignkeys as $f)
                {
                    $query3    = $this->dbcrud->query('SELECT COLUMN_NAME,CONSTRAINT_NAME,REFERENCED_TABLE_SCHEMA,REFERENCED_TABLE_NAME,REFERENCED_COLUMN_NAME FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE WHERE TABLE_SCHEMA = \'' . $db_name . '\' AND CONSTRAINT_NAME = \'' . $f['CONSTRAINT_NAME'] . '\'');
                    $fkfound[] = $query3->result_array();
                }
            }
            // End foreignKeys
            if (isset($fkfound[0]))
            {
                $this->controller_data = $controller            = $this->build_controller($fields, $fkfound);
                $this->create_data     = $view_create           = $this->build_view_create($fields, $fkfound);
                $this->edit_data       = $view_edit             = $this->build_view_edit($fields, $fkfound);
                $this->viewdetail_data = $view_viewdetail       = $this->build_view_view($fields, $fkfound);
                $this->model_data      = $model                 = $this->build_model($fields, $fkfound);
            }
            else
            {
                $this->controller_data = $controller            = $this->build_controller($fields);
                $this->create_data     = $view_create           = $this->build_view_create($fields);
                $this->edit_data       = $view_edit             = $this->build_view_edit($fields);
                $this->viewdetail_data = $view_viewdetail       = $this->build_view_view($fields);
                $this->model_data      = $model                 = $this->build_model($fields);
            }

            $this->list_data             = $view_list                   = $this->build_view_listing($fields);
            $this->listp_data            = $view_listp                  = $this->build_view_listp($fields);
            $this->ind_htm_data          = $view_htm                    = $this->build_htm();
            $data['model']               = $model;
            $data['controller']          = $controller;
            $data['view_create']         = $view_create;
            $data['view_edit']           = $view_edit;
            $data['view_viewdetail']     = $view_viewdetail;
            $data['view_list']           = $view_list;
            $data['view_listp']          = $view_listp;
            $data['view_header']         = $view_header;
            $data['view_footer']         = $view_footer;
            $data['view_htm']            = $view_htm;
            //name for each file
            $data['controllername']      = $this->controllername;
            $data['modelname']           = $this->modelname;
            $data['listviewname']        = $this->listviewname;
            $data['listviewp']           = $this->list_viewpagname;
            $data['create_viewname']     = $this->create_viewname;
            $data['edit_viewname']       = $this->edit_viewname;
            $data['viewdetail_viewname'] = $this->viewdetail_viewname;
            $data['htm_name']            = $this->ind_htm_name;
            $data['header']              = $this->header;
            $data['footer']              = $this->footer;
            $data['tname']               = $this->tname;
            $data['fname']               = $this->fname;
            $data['sname']               = $this->sname;
            $data['cname']               = $cname;
            
            if (isset($_POST['download']))
            {
                $this->download();
            }
        }
        $db_name        = $this->dbcrud->database;
        $result         = $this->dbcrud->query('SELECT TABLE_NAME AS TABLES FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA="' . $db_name . '";');
        $result         = $result->result_array();
        $data['result'] = $result;
        $this->load->view('crud/index', $data);
    }

    /**
     * Functon buld controller
     * 
     * it will bult structure of controller
     * 
     * @auther shabeeb <me@shabeebk.com>
     * @createdon  17-06-2014
     * @
     * 
     * @param type 
     * @return type 
     * exceptions controller name empty
     * 
     */
    function build_controller($fields = NULL, $fktrovate = NULL) {
        if ($fields == NULL)
        {
            return FALSE;
        }
        $library_list = $this->library_list;
        $helper_list  = $this->helper_list;
        $controller   = '<?php if (!defined(\'BASEPATH\')) exit(\'No direct script access allowed\');



class ' . ucfirst($this->controllername) . ' extends Logged_Controller
    {
        public function __construct() {
                parent::__construct();
                    
            ';
        if (!empty($library_list))
        {
            foreach ($library_list as $lib)
            {
                $controller .= ' $this->load->library("' . $lib . '" );             
            ';
            }
        }
        if (!empty($helper_list))
        {
            foreach ($helper_list as $help)
            {
                $controller .= ' $this->load->helper("' . $help . '" );             
            ';
            }
        }
        //$this->load->helper('url');
        $controller .= ' $this->load->model(\'' . ucfirst($this->modelname) . '\');
            ';
        if ($fktrovate != NULL)
        {
            foreach ($fktrovate as $fkt)
            {
                $fktkname   = $fkt[0]['CONSTRAINT_NAME'];
                $fktcname   = $fkt[0]['COLUMN_NAME'];
                $fkttschema = $fkt[0]['REFERENCED_TABLE_SCHEMA'];
                $fkttname   = $fkt[0]['REFERENCED_TABLE_NAME'];
                $fktrcname  = $fkt[0]['REFERENCED_COLUMN_NAME'];

                $controller .= ' $this->load->model(\'' . ucfirst($fkttname) . '_model\');
            ';
            }
        }

        $controller .= '
    
        }
     /**
     * Functon index
     * 
     * list all the values in grid
     * 
     * @auther ' . $this->auther . ' <' . $this->auther_mail . '>
     * @createdon   : ' . $this->created_date . '
     * 
     */
     
    function index()
        {
            $data[\'' . $this->fname . '\'] = $this->' . ucfirst($this->modelname) . '->get_all_' . $this->fname . '();
            $this->load->view(\'' . $this->tname . '/' . $this->listviewname . '\',$data);
        }
     
    function index_active()
        {
            $data[\'' . $this->fname . '\'] = $this->' . ucfirst($this->modelname) . '->get_all_' . $this->fname . '_active();
            $this->load->view(\'' . $this->tname . '/' . $this->listviewname . '\',$data);
        }';

        
        
        $controller .= '

    function report_index()
        {
            $data[\'' . $this->fname . '\'] = $this->' . ucfirst($this->modelname) . '->get_all_' . $this->fname . '();
            
                $this->load->library(\'pdf\');
                $this->pdf = new Pdf();
                $this->pdf->AddPage();
                $this->pdf->AliasNbPages();
                $this->pdf->SetLeftMargin(15);
                $this->pdf->SetRightMargin(15);
                $this->pdf->SetFillColor(200,200,200);
                $this->pdf->SetFont(\'Arial\',\'\', 9);
                $this->pdf->Cell(180,15,\'Report index of ' . $this->fname . '\',0,0,\'C\');
                $this->pdf->Ln();';
        foreach ($fields as $field)
        {
            $field_name = $field->name;
            $pk         = $field->primary_key;
            $label      = str_replace('_', ' ', $field_name);

            $controller .= ' 
                $this->pdf->Cell(10,7,"' . $field_name . '",1,0,\'C\');';
        }
        $controller .= '
                $this->pdf->Ln(10);
            
                foreach($data[\'' . $this->fname . '\'] as $k)
                    {';
        foreach ($fields as $field)
        {
            $field_name = $field->name;
            $pk         = $field->primary_key;
            $label      = str_replace('_', ' ', $field_name);

            $controller .= ' 
                        $this->pdf->Cell(10,7,$k[\'' . $field_name . '\'],1,0,\'C\');';
        }

        $controller .= '
                        $this->pdf->Ln();
                    }
                ob_end_clean();
            $this->pdf->Output("' . $this->fname . '.pdf", \'I\');
        }
';
        $controller .= ' 
       
     /**
     * Functon list pagination
     */

    function ' . $this->fname . '()
        {
            $this->load->library(\'pagination\');
            $config = array();
            $config[\'base_url\'] = base_url().\'index.php/' . $this->controllername . '/' . $this->fname . '\';
            $config[\'total_rows\'] = $this->' . ucfirst($this->modelname) . '->count_all_' . $this->fname . '();
            $config[\'per_page\'] = 10;
            $config[\'num_links\'] = 5;
            //$config[\'uri_segment\'] = 4;

            $config[\'full_tag_open\'] = "<ul class=\'pagination\'>";
            $config[\'full_tag_close\'] = "</ul>";
            $config[\'num_tag_open\'] = "<li>";
            $config[\'num_tag_close\'] = "</li>";
            $config[\'cur_tag_open\'] = "<li class=\'disabled\'><li class=\'active\'><a href=\'#\'>";
            $config[\'cur_tag_close\'] = "<span class=\'sr-only\'></span></a></li>";
            $config[\'next_tag_open\'] = "<li>";
            $config[\'next_tagl_close\'] = "</li>";
            $config[\'prev_tag_open\'] = "<li>";
            $config[\'prev_tagl_close\'] = "</li>";
            $config[\'first_tag_open\'] = "<li>";
            $config[\'first_tagl_close\'] = "</li>";
            $config[\'last_tag_open\'] = "<li>";
            $config[\'last_tagl_close\'] = "</li>";

            $this->pagination->initialize($config);
            $page = $this->uri->segment(3);
            $data[\'' . $this->fname . '\'] = $this->' . ucfirst($this->modelname) . '->fetch_' . $this->fname . '($config[\'per_page\'],$page);
            $data[\'links\'] = $this->pagination->create_links();
            $data[\'message\'] = \'\';        

            $this->load->view(\'' . $this->tname . '/' . $this->list_viewpagname . '\',$data);   
        }

    function ' . $this->fname . '_active()
        {
            $this->load->library(\'pagination\');
            $config = array();
            $config[\'base_url\'] = base_url().\'/' . $this->controllername . '/' . $this->fname . '_active\';
            $config[\'total_rows\'] = $this->' . ucfirst($this->modelname) . '->count_all_' . $this->fname . '_active();
            $config[\'per_page\'] = 10;
            $config[\'num_links\'] = 5;
            //$config[\'uri_segment\'] = 4;

            $config[\'full_tag_open\'] = "<ul class=\'pagination\'>";
            $config[\'full_tag_close\'] = "</ul>";
            $config[\'num_tag_open\'] = "<li>";
            $config[\'num_tag_close\'] = "</li>";
            $config[\'cur_tag_open\'] = "<li class=\'disabled\'><li class=\'active\'><a href=\'#\'>";
            $config[\'cur_tag_close\'] = "<span class=\'sr-only\'></span></a></li>";
            $config[\'next_tag_open\'] = "<li>";
            $config[\'next_tagl_close\'] = "</li>";
            $config[\'prev_tag_open\'] = "<li>";
            $config[\'prev_tagl_close\'] = "</li>";
            $config[\'first_tag_open\'] = "<li>";
            $config[\'first_tagl_close\'] = "</li>";
            $config[\'last_tag_open\'] = "<li>";
            $config[\'last_tagl_close\'] = "</li>";

            $this->pagination->initialize($config);
            $page = $this->uri->segment(3);
            $data[\'' . $this->fname . '\'] = $this->' . ucfirst($this->modelname) . '->fetch_' . $this->fname . '_active($config[\'per_page\'],$page);
            $data[\'links\'] = $this->pagination->create_links();
            $data[\'message\'] = \'\';        

            $this->load->view(\'' . $this->tname . '/' . $this->list_viewpagname . '\',$data);

        }

    /**
     * Functon search
     */

    function ' . $this->fname . '_search_id()
        {
            $search = $this->input->post(\'search_id\');

            if(isset($search) and !empty($search))
                {
                    $data[\'' . $this->fname . '\'] = $this->' . ucfirst($this->modelname) . '->search_' . $this->fname . '_by_id($search);
                    $data[\'links\'] = \'\';
                    $data[\'message\'] = \'Svuota il campo ricerca e premi Cerca per ricaricare i dati\';
                    $this->load->view(\'' . $this->tname . '/' . $this->list_viewpagname . '\',$data);
                }
                else
                    {
                        redirect(\'' . $this->controllername . '/' . $this->fname . '\');
                    }
        }

    /**
     * Functon create
     */

    function create_' . $this->sname . '()
        {';
        if ($fktrovate != NULL)
        {
            foreach ($fktrovate as $fkt)
            {
                $fktkname   = $fkt[0]['CONSTRAINT_NAME'];
                $fktcname   = $fkt[0]['COLUMN_NAME'];
                $fkttschema = $fkt[0]['REFERENCED_TABLE_SCHEMA'];
                $fkttname   = $fkt[0]['REFERENCED_TABLE_NAME'];
                $fktrcname  = $fkt[0]['REFERENCED_COLUMN_NAME'];

                $controller .= '
            $data[\'' . $fkttname . '\'] = $this->' . ucfirst($fkttname) . '_model->get_all_' . $fkttname . '();';
            }
        }

        $controller .= '
            $this->load->library(\'form_validation\');
            $this->form_validation->set_error_delimiters(\'<div class="alert alert-danger">\', \'</div>\');
';

        foreach ($fields as $field)
        {
            $field_name = $field->name;
            $pk         = $field->primary_key;
            $label      = str_replace('_', ' ', $field_name);
            if ($pk != 1)
            {
                $controller .= ' 
            $this->form_validation->set_rules("' . $field_name . '", "' . $label . '", "required");';
            }
        }

        $controller .= '

            if($this->form_validation->run())     
                {                
                    $params = array(';
        foreach ($fields as $field)
        {
            $field_name = $field->name;
            $pk         = $field->primary_key;
            if ($pk != 1)
            {
                $controller .= '
                        \'' . $field_name . '\' => $this->input->post(\'' . $field_name . '\'),';
            }
        }
        $controller .= '
                        );
    
                $' . $this->sname . '_id = $this->' . ucfirst($this->modelname) . '->add_' . $this->sname . '($params);
                redirect(\'' . $this->controllername . '/' . $this->fname . '\');
        }
        else
            {';
        if ($fktrovate != NULL)
        {
            $controller .= '        
                $this->load->view(\'' . $this->tname . '/' . $this->create_viewname . '\',$data);
';
        }
        else
        {
            $controller .= '        
                $this->load->view(\'' . $this->tname . '/' . $this->create_viewname . '\');';
        }

        $controller .= '
            }
        } 

/**
     * Functon view
     * edit form
     * 
     * @auther ' . $this->auther . ' <' . $this->auther_mail . '>
     * @createdon   : ' . $this->created_date . '
     * 
     */
    function view_' . $this->sname . '($id) 
        {';
        if ($fktrovate != NULL)
        {
            foreach ($fktrovate as $fkt)
            {
                $fktkname   = $fkt[0]['CONSTRAINT_NAME'];
                $fktcname   = $fkt[0]['COLUMN_NAME'];
                $fkttschema = $fkt[0]['REFERENCED_TABLE_SCHEMA'];
                $fkttname   = $fkt[0]['REFERENCED_TABLE_NAME'];
                $fktrcname  = $fkt[0]['REFERENCED_COLUMN_NAME'];

                $controller .= '
            $data[\'' . $fkttname . '\'] = $this->' . ucfirst($fkttname) . '_model->get_all_' . $fkttname . '();';
            }
        }

        $controller .= '

            // check if the ' . $this->sname . ' exists before trying to edit it
            $data[\'id_' . $this->sname . '\'] = $this->' . ucfirst($this->modelname) . '->get_' . $this->sname . '_by_id($id);        
            if(isset($data[\'id_' . $this->sname . '\'][\'' . $this->tbl_pk . '\']))
                {
                    $this->load->view(\'' . $this->tname . '/' . $this->viewdetail_viewname . '\',$data);
                }
                else
                    {
                        $this->session->set_flashdata(\'flashError\', \'The ' . $this->sname . ' you are trying to view does not exist.\');                            
                        redirect(\'' . $this->controllername . '/' . $this->fname . '\');
                    }            
        } 

/**
     * Functon edit
     * edit form
     * 
     * @auther ' . $this->auther . ' <' . $this->auther_mail . '>
     * @createdon   : ' . $this->created_date . '
     * 
     */
    function edit_' . $this->sname . '($id)
        {';
        if ($fktrovate != NULL)
        {
            foreach ($fktrovate as $fkt)
            {
                $fktkname   = $fkt[0]['CONSTRAINT_NAME'];
                $fktcname   = $fkt[0]['COLUMN_NAME'];
                $fkttschema = $fkt[0]['REFERENCED_TABLE_SCHEMA'];
                $fkttname   = $fkt[0]['REFERENCED_TABLE_NAME'];
                $fktrcname  = $fkt[0]['REFERENCED_COLUMN_NAME'];

                $controller .= '
            $data[\'' . $fkttname . '\'] = $this->' . ucfirst($fkttname) . '_model->get_all_' . $fkttname . '();';
            }
        }

        $controller .= '

            // check if the ' . $this->sname . ' exists before trying to edit it
            $data[\'id_' . $this->sname . '\'] = $this->' . ucfirst($this->modelname) . '->get_' . $this->sname . '_by_id($id);
        
            if(isset($data[\'id_' . $this->sname . '\'][\'' . $this->tbl_pk . '\']))
                {';

        foreach ($fields as $field)
        {
            $field_name = $field->name;
            $pk         = $field->primary_key;
            $label      = str_replace('_', ' ', $field_name);
            if ($pk != 1)
            {
                $controller .= ' 
                    $this->form_validation->set_rules("' . $field_name . '", "' . $label . '", "required");';
            }
        }

        $controller .= '

                    if($this->form_validation->run())     
                        {
                            $params = array(';
        foreach ($fields as $field)
        {
            $field_name = $field->name;
            $pk         = $field->primary_key;
            if ($pk != 1)
            {
                $controller .= '
                                \'' . $field_name . '\' => $this->input->post(\'' . $field_name . '\'),';
            }
        }
        $controller .= '
                                );
                
                            $this->' . ucfirst($this->modelname) . '->update_' . $this->sname . '($id,$params);            
                            redirect(\'' . $this->controllername . '/view_' . $this->sname . '/\'.$data[\'id_' . $this->sname . '\'][\'' . $this->tbl_pk . '\']);
                        }
                        else
                            {
                                $this->load->view(\'' . $this->tname . '/' . $this->edit_viewname . '\',$data);
                            }
                }
                else
                    {
                        $this->session->set_flashdata(\'flashError\', \'The ' . $this->sname . ' you are trying to view does not exist.\');                            
                        redirect(\'' . $this->controllername . '/' . $this->fname . '\');
                    }   
        } 
    ';
        $controller .= '              

/**
    * Functon remove
    * 
    * process grid data 
    * 
    * @auther ' . $this->auther . ' <' . $this->auther_mail . '>
    * @createdon   : ' . $this->created_date . '
    */

    function remove_' . $this->sname . '($id)
        {
            $data[\'id_' . $this->sname . '\'] = $this->' . ucfirst($this->modelname) . '->get_' . $this->sname . '_by_id($id);
            if(isset($data[\'id_' . $this->sname . '\'][\'' . $this->tbl_pk . '\']))
                {
                    $params = array(
                        \'' . $this->obsolfield . '\' => 1,
                        );
                    $this->' . ucfirst($this->modelname) . '->update_' . $this->sname . '($data[\'id_' . $this->sname . '\'][\'' . $this->tbl_pk . '\'],$params);            
                    //$this->' . ucfirst($this->modelname) . '->delete_' . $this->sname . '_by_id($id);
                    redirect(\'' . $this->controllername . '/' . $this->fname . '\');
                }
                else
                    {
                        $this->session->set_flashdata(\'flashError\', \'The ' . $this->sname . ' you are trying to delete does not exist.\');                            
                        redirect(\'' . $this->controllername . '/' . $this->fname . '\');
                    }     
	}';
        $controller .= ' 
//  POST CRUD


}';
        return $controller;
    }

    function build_view_create($fields = NULL, $fktrovate = NULL) {
        if ($fields == NULL)
        {
            return FALSE;
        }
        $view = '<?php if (!defined(\'BASEPATH\')) exit(\'No direct script access allowed\');

$this->load->view(\'templates/header\');

//$arr = get_defined_vars();
//echo \'<pre>\';
//print_r($_ci_vars);
//echo \'</pre>\';

?>
<!--
<div class="row">
    <div class="col-lg-12">
        <h1 class="page-header">' . $this->controllername . ' - Aggiungi ' . $this->sname . '</h1>
    </div>
</div>-->
<div class="row">
    <div class="col-md-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                Aggiungi ' . $this->sname . '
            </div>
            <div class="panel-body">
                <div class="row">
                    <div class="col-md-12">
                        
                        <?php echo form_open(\'' . $this->controllername . '/create_' . $this->sname . '\'); ?>
    ';



        foreach ($fields as $field)
        {
            $havefk     = 2000;
            $field_name = $field->name;
            $field_type = substr($field->type, 0, 4);
            $pk         = $field->primary_key;
            if ($pk != 1)
            {             //se non è una chiave primaria
                $label = str_replace('_', ' ', $field_name);

                if ($fktrovate != NULL)     //se sono state trovate delle fk
                {
                    foreach ($fktrovate as $key => $value)        //per tutte le chiavi trovate
                    {
                        $fktcname = $value[0]['COLUMN_NAME'];
                        if ($fktcname == $field_name)       //se il campo ha una fk, segna la chiave
                        {
                            $havefk = $key;
                        }
                    }
                    if ($havefk != 2000)                       // se ha la fk
                    {

                        $fktkname   = $fktrovate[$havefk][0]['CONSTRAINT_NAME'];
                        $fktcname   = $fktrovate[$havefk][0]['COLUMN_NAME'];
                        $fkttschema = $fktrovate[$havefk][0]['REFERENCED_TABLE_SCHEMA'];
                        $fkttname   = $fktrovate[$havefk][0]['REFERENCED_TABLE_NAME'];
                        $fktrcname  = $fktrovate[$havefk][0]['REFERENCED_COLUMN_NAME'];
                        $fktlabel   = str_replace('_', ' ', $fktcname);
                        $fktletter  = substr($fktrcname, 0, 2);

                        $view .= '
                    <div class="row clearfix">
                        <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                            <label for="' . $fktcname . '" class="control-label">' . $fktlabel . '</label>
                            <div class="form-group">
                                <?php echo form_error(\'' . $fktcname . '\'); ?>
                                <select name="' . $fktcname . '" class="form-control">
                                    <?php foreach($' . $fkttname . ' as $' . $fktletter . '): ?>
                                        <option value="<?php echo $' . $fktletter . '[\'' . $fktrcname . '\']; ?>" class="form-control" id="' . $fktcname . '"><?php echo $' . $fktletter . '[\'' . $fktrcname . '\']; ?></option>
                                    <?php endforeach;?>
                                </select>
                            </div>    
                        </div>                            
                    </div>
';
                    }
                    else       // se non ha la fk
                    {
                        if ($field_type == "date")
                        {
                            $view .= '
                    <div class="row clearfix">
                        <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                            <label for="' . $field_name . '" class="control-label">' . $label . '</label>
                            <div class="form-group">
                                <?php echo form_error(\'' . $field_name . '\'); ?>
                                <div class="input-group date" id="' . $field_name . '">
                                    <input type="text" name="' . $field_name . '" value="<?php echo $this->input->post(\'' . $field_name . '\'); ?>" class="form-control" id="' . $field_name . '" />
                                    <span class="input-group-addon">
                                        <span class="glyphicon glyphicon-calendar"></span>
                                    </span>
                                </div>
                            </div>
                            <script type="text/javascript">
                                $(function () {
                                    $(\'#' . $field_name . '\').datetimepicker({
                                        locale: \'it\',
                                        format: \'DD/MM/YYYY\'
                                    });
                                });
                            </script>
                        </div>                            
                    </div>';
                        }
                        else
                        {
                            if ($field->type == "tinyint")
                            {
                                $view .= '
                    <div class="row clearfix">
                        <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                            <label for="' . $field_name . '" class="control-label">' . $label . '</label>
                            <div class="form-group">
                                <?php echo form_error(\'' . $field_name . '\'); ?>
                                <select name="' . $field_name . '" class="form-control">
                                    <option value="0" class="form-control" id="' . $field_name . '">No</option>
                                    <option value="1" class="form-control" id="' . $field_name . '">Si</option>
                                            
                                </select>
                            </div>    
                        </div>                            
                    </div>
';
                            }
                            else
                            {
                                $view .= '
                    <div class="row clearfix">
                        <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                            <label for="' . $field_name . '" class="control-label">' . $label . '</label>
                            <div class="form-group">
                                    <?php echo form_error(\'' . $field_name . '\'); ?>
                                    <input type="text" name="' . $field_name . '" value="<?php echo $this->input->post(\'' . $field_name . '\'); ?>" class="form-control" id="' . $field_name . '" />
                            </div>
                        </div>                            
                    </div>';
                            }
                        }
                    }
                }
                else
                {
                    if ($field_type == "date")
                    {
                        $view .= '
                    <div class="row clearfix">
                        <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                            <label for="' . $field_name . '" class="control-label">' . $label . '</label>
                            <div class="form-group">
                                <?php echo form_error(\'' . $field_name . '\'); ?>
                                <div class="input-group date" id="' . $field_name . '">
                                    <input type="text" name="' . $field_name . '" value="<?php echo $this->input->post(\'' . $field_name . '\'); ?>" class="form-control" id="' . $field_name . '" />
                                    <span class="input-group-addon">
                                        <span class="glyphicon glyphicon-calendar"></span>
                                    </span>
                                </div>
                            </div>
                            <script type="text/javascript">
                                $(function () {
                                    $(\'#' . $field_name . '\').datetimepicker({
                                        locale: \'it\',
                                        format: \'DD/MM/YYYY\'
                                    });
                                });
                            </script>
                        </div>                            
                    </div>';
                    }
                    else
                    {
                        if ($field->type == "tinyint")
                        {
                            $view .= '
                    <div class="row clearfix">
                        <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                            <label for="' . $field_name . '" class="control-label">' . $label . '</label>
                            <div class="form-group">
                                <?php echo form_error(\'' . $field_name . '\'); ?>
                                <select name="' . $field_name . '" class="form-control">
                                    <option value="0" class="form-control" id="' . $field_name . '">No</option>
                                    <option value="1" class="form-control" id="' . $field_name . '">Si</option>
                                            
                                </select>
                            </div>    
                        </div>                            
                    </div>
';
                        }
                        else
                        {
                            $view .= '
                    <div class="row clearfix">
                        <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                            <label for="' . $field_name . '" class="control-label">' . $label . '</label>
                            <div class="form-group">
                                <?php echo form_error(\'' . $field_name . '\'); ?>
                                    <input type="text" name="' . $field_name . '" value="<?php echo $this->input->post(\'' . $field_name . '\'); ?>" class="form-control" id="' . $field_name . '" />
                            </div>
                        </div>                            
                    </div>';
                        }
                    }
                }
            }
        }


        $view .= '
                    <div class="form-group">
                            <button type="submit" class="btn btn-success">
                                    <i class="fa fa-check"></i> Salva
                            </button>
                    </div>
                    <?php echo form_close(); ?>
                </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $this->load->view(\'templates/footer\');?>
';
        return $view;
    }

    function build_htm() {

        $htm_data = '<!DOCTYPE html>
<html>
<head>
	<title>403 Forbidden</title>
</head>
<body>
<!--Build by CRUD Generator-->
<p>Directory access is forbidden.</p>

</body>
</html>
    ';
        return $htm_data;
    }

    function build_view_view($fields = NULL, $fktrovate = NULL) {
        if ($fields == NULL)
        {
            return FALSE;
        }
        $view_edit = '<?php if (!defined(\'BASEPATH\')) exit(\'No direct script access allowed\');

$this->load->view(\'templates/header\');

//$arr = get_defined_vars();
//echo \'<pre>\';
//print_r($_ci_vars);
//echo \'</pre>\';

?>

<div class="row">
    <div class="col-md-12">
        <div class="panel panel-default">
            <nav class="navbar navbar-default">
                <div class="container-fluid">
                    <div class="navbar-header">
                        <a class="navbar-brand" href="#">view ' . $this->sname . '</a>
                    </div>    
                    <ul class="nav navbar-nav navbar-right">
                                <li><a href="<?php echo site_url(\'' . $this->controllername . '/edit_' . $this->sname . '/\'.$id_' . $this->sname . '[\'' . $this->tbl_pk . '\']); ?>"><span class="glyphicon glyphicon-edit"></span> edit</a></li>
                    </ul>
                </div>
            </nav>
            <div class="panel-body">
                <div class="row">
                    <div class="col-md-12">                     


                    ';


        foreach ($fields as $field)
        {
            $havefk     = 2000;
            $field_name = $field->name;
            $field_type = substr($field->type, 0, 4);
            $pk         = $field->primary_key;
            if ($pk != 1)
            {             //se non è una chiave primaria
                $label = str_replace('_', ' ', $field_name);

                if ($fktrovate != NULL)     //se sono state trovate delle fk
                {
                    foreach ($fktrovate as $key => $value)        //per tutte le chiavi trovate
                    {
                        $fktcname = $value[0]['COLUMN_NAME'];
                        if ($fktcname == $field_name)       //se il campo ha una fk, segna la chiave
                        {
                            $havefk = $key;
                        }
                    }
                    if ($havefk != 2000)                       // se ha la fk
                    {

                        $fktkname   = $fktrovate[$havefk][0]['CONSTRAINT_NAME'];
                        $fktcname   = $fktrovate[$havefk][0]['COLUMN_NAME'];
                        $fkttschema = $fktrovate[$havefk][0]['REFERENCED_TABLE_SCHEMA'];
                        $fkttname   = $fktrovate[$havefk][0]['REFERENCED_TABLE_NAME'];
                        $fktrcname  = $fktrovate[$havefk][0]['REFERENCED_COLUMN_NAME'];
                        $fktlabel   = str_replace('_', ' ', $fktcname);
                        $fktletter  = substr($fktrcname, 0, 2);

                        $view_edit .= '
                    <div class="row clearfix">
                        <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                            <label for="' . $fktcname . '" class="control-label">' . $fktlabel . '</label>
                                <p><?php echo ($this->input->post(\'' . $field_name . '\') ? $this->input->post(\'' . $field_name . '\') : $id_' . $this->sname . '[\'' . $field_name . '\']); ?></p>
                        </div>                            
                    </div>
';
                    }
                    else       // se non ha la fk
                    {
                        if ($field_type == "date")
                        {
                            $view_edit .= '
                    <div class="row clearfix">
                        <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                            <label for="' . $field_name . '" class="control-label">' . $label . '</label>
                                <p><?php echo ($this->input->post(\'' . $field_name . '\') ? $this->input->post(\'' . $field_name . '\') : $id_' . $this->sname . '[\'' . $field_name . '\']); ?></p>
                        </div>                            
                    </div>';
                        }
                        else
                        {
                            if ($field_type == "tinyint")
                            {
                                $view_edit .= '
                    <div class="row clearfix">
                        <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                            <label for="' . $field_name . '" class="control-label">' . $label . '</label>
                                <p><?php echo ($this->input->post(\'' . $field_name . '\') ? $this->input->post(\'' . $field_name . '\') : $id_' . $this->sname . '[\'' . $field_name . '\']); ?></p>
                        </div>                            
                    </div>
';
                            }
                            else
                            {
                                $view_edit .= '
                    <div class="row clearfix">
                        <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                            <label for="' . $field_name . '" class="control-label">' . $label . '</label>
                                <p><?php echo ($this->input->post(\'' . $field_name . '\') ? $this->input->post(\'' . $field_name . '\') : $id_' . $this->sname . '[\'' . $field_name . '\']); ?></p>
                        </div>                            
                    </div>';
                            }
                        }
                    }
                }
                else
                {
                    if ($field_type == "date")
                    {
                        $view_edit .= '
                    <div class="row clearfix">
                        <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                            <label for="' . $field_name . '" class="control-label">' . $label . '</label>
                                <p><?php echo ($this->input->post(\'' . $field_name . '\') ? $this->input->post(\'' . $field_name . '\') : $id_' . $this->sname . '[\'' . $field_name . '\']); ?></p>
                        </div>                            
                    </div>';
                    }
                    else
                    {
                        if ($field->type == "tinyint")
                        {
                            $view_edit .= '
                    <div class="row clearfix">
                        <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                            <label for="' . $field_name . '" class="control-label">' . $label . '</label>
                                <p><?php echo ($this->input->post(\'' . $field_name . '\') ? $this->input->post(\'' . $field_name . '\') : $id_' . $this->sname . '[\'' . $field_name . '\']); ?></p>
                        </div>                            
                    </div>
';
                        }
                        else
                        {
                            $view_edit .= '
                    <div class="row clearfix">
                        <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                            <label for="' . $field_name . '" class="control-label">' . $label . '</label>
                                <p><?php echo ($this->input->post(\'' . $field_name . '\') ? $this->input->post(\'' . $field_name . '\') : $id_' . $this->sname . '[\'' . $field_name . '\']); ?></p>
                        </div>                            
                    </div>';
                        }
                    }
                }
            }
        }

        $view_edit .= '
                </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $this->load->view(\'templates/footer\');?>
';
        return $view_edit;
    }

    function build_view_edit($fields = NULL, $fktrovate = NULL) {
        if ($fields == NULL)
        {
            return FALSE;
        }
        $view_edit = '<?php if (!defined(\'BASEPATH\')) exit(\'No direct script access allowed\');

$this->load->view(\'templates/header\');
//$arr = get_defined_vars();
//echo \'<pre>\';
//print_r($_ci_vars);
//echo \'</pre>\';

?>
<!--
<div class="row">
    <div class="col-lg-12">
        <h1 class="page-header">' . $this->controllername . ' - Modifica ' . $this->sname . '</h1>
    </div>
</div>-->
<div class="row">
    <div class="col-md-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                Modifica ' . $this->sname . '
            </div>
            <div class="panel-body">
                <div class="row">
                    <div class="col-md-12">
                        
                        <?php echo form_open(\'' . $this->controllername . '/edit_' . $this->sname . '/\'.$id_' . $this->sname . '[\'' . $this->tbl_pk . '\']); ?>
    ';


        foreach ($fields as $field)
        {
            $havefk     = 2000;
            $field_name = $field->name;
            $field_type = substr($field->type, 0, 4);
            $pk         = $field->primary_key;
            if ($pk != 1)
            {             //se non è una chiave primaria
                $label = str_replace('_', ' ', $field_name);

                if ($fktrovate != NULL)     //se sono state trovate delle fk
                {
                    foreach ($fktrovate as $key => $value)        //per tutte le chiavi trovate
                    {
                        $fktcname = $value[0]['COLUMN_NAME'];
                        if ($fktcname == $field_name)       //se il campo ha una fk, segna la chiave
                        {
                            $havefk = $key;
                        }
                    }
                    if ($havefk != 2000)                       // se ha la fk
                    {

                        $fktkname   = $fktrovate[$havefk][0]['CONSTRAINT_NAME'];
                        $fktcname   = $fktrovate[$havefk][0]['COLUMN_NAME'];
                        $fkttschema = $fktrovate[$havefk][0]['REFERENCED_TABLE_SCHEMA'];
                        $fkttname   = $fktrovate[$havefk][0]['REFERENCED_TABLE_NAME'];
                        $fktrcname  = $fktrovate[$havefk][0]['REFERENCED_COLUMN_NAME'];
                        $fktlabel   = str_replace('_', ' ', $fktcname);
                        $fktletter  = substr($fktrcname, 0, 2);

                        $view_edit .= '
                    <div class="row clearfix">
                        <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                            <label for="' . $fktcname . '" class="control-label">' . $fktlabel . '</label>
                            <div class="form-group">
                                <?php echo form_error(\'' . $fktcname . '\'); ?>
                                <select name="' . $fktcname . '" class="form-control">
                                    <?php foreach($' . $fkttname . ' as $' . $fktletter . '): ?>
                                        <option value="<?php echo $' . $fktletter . '[\'' . $fktrcname . '\']; ?>" <?php if ($id_' . $this->sname . '[\'' . $field_name . '\'] == $' . $fktletter . '[\'' . $fktrcname . '\']) echo "selected=\"selected\"";?> class="form-control" id="' . $fktcname . '"><?php echo $' . $fktletter . '[\'' . $fktrcname . '\']; ?></option>
                                    <?php endforeach;?>
                                </select>
                            </div>
                        </div>                            
                    </div>
';
                    }
                    else       // se non ha la fk
                    {
                        if ($field_type == "date")
                        {
                            $view_edit .= '
                    <div class="row clearfix">
                        <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                                <label for="' . $field_name . '" class="control-label">' . $label . '</label>
                                <div class="form-group">
                                <?php echo form_error(\'' . $field_name . '\'); ?>
                                    <div class="input-group date" id="' . $field_name . '">
                                        <input type="text" name="' . $field_name . '" value="<?php echo ($this->input->post(\'' . $field_name . '\') ? $this->input->post(\'' . $field_name . '\') : $id_' . $this->sname . '[\'' . $field_name . '\']); ?>" class="form-control" id="' . $field_name . '" />
                                        <span class="input-group-addon">
                                            <span class="glyphicon glyphicon-calendar"></span>
                                        </span>
                                    </div>
                                </div>
                            <script type="text/javascript">
                                $(function () {
                                    $(\'#' . $field_name . '\').datetimepicker({
                                        locale: \'it\',
                                        format: \'DD/MM/YYYY\'
                                    });
                                });
                            </script>                             

                        </div>                            
                    </div>';
                        }
                        else
                        {
                            if ($field_type == "tinyint")
                            {
                                $view_edit .= '
                    <div class="row clearfix">
                        <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                            <label for="' . $field_name . '" class="control-label">' . $field_name . '</label>
                            <div class="form-group">
                                <?php echo form_error(\'' . $field_name . '\'); ?>
                                <select name="' . $field_name . '" class="form-control">
                                        <option value="0" <?php if ($id_' . $this->sname . '[\'' . $field_name . '\'] == 0) echo "selected=\"selected\"";?> class="form-control" id="' . $field_name . '">No</option>
                                        <option value="1" <?php if ($id_' . $this->sname . '[\'' . $field_name . '\'] == 1) echo "selected=\"selected\"";?> class="form-control" id="' . $field_name . '">Si</option>
                                </select>
                            </div>
                        </div>                            
                    </div>
';
                            }
                            else
                            {
                                $view_edit .= '
                            <div class="row clearfix">
                        <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                                        <label for="' . $field_name . '" class="control-label">' . $label . '</label>
                                        <div class="form-group">
                                <?php echo form_error(\'' . $field_name . '\'); ?>
                                                <input type="text" name="' . $field_name . '" value="<?php echo ($this->input->post(\'' . $field_name . '\') ? $this->input->post(\'' . $field_name . '\') : $id_' . $this->sname . '[\'' . $field_name . '\']); ?>" class="form-control" id="' . $field_name . '" />
                                        </div>
                                </div>                            
                            </div>';
                            }
                        }
                    }
                }
                else
                {
                    if ($field_type == "date")
                    {
                        $view_edit .= '
                    <div class="row clearfix">
                        <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                                <label for="' . $field_name . '" class="control-label">' . $label . '</label>
                                <div class="form-group">
                                <?php echo form_error(\'' . $field_name . '\'); ?>
                                    <div class="input-group date" id="' . $field_name . '">
                                        <input type="text" name="' . $field_name . '" value="<?php echo ($this->input->post(\'' . $field_name . '\') ? $this->input->post(\'' . $field_name . '\') : $id_' . $this->sname . '[\'' . $field_name . '\']); ?>" class="form-control" id="' . $field_name . '" />
                                        <span class="input-group-addon">
                                            <span class="glyphicon glyphicon-calendar"></span>
                                        </span>
                                    </div>
                                </div>
                            <script type="text/javascript">
                                $(function () {
                                    $(\'#' . $field_name . '\').datetimepicker({
                                        locale: \'it\',
                                        format: \'DD/MM/YYYY\'
                                    });
                                });
                            </script>                             

                        </div>                            
                    </div>';
                    }
                    else
                    {
                        if ($field->type == "tinyint")
                        {
                            $view_edit .= '
                    <div class="row clearfix">
                        <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                            <label for="' . $field_name . '" class="control-label">' . $field_name . '</label>
                            <div class="form-group">
                                <?php echo form_error(\'' . $field_name . '\'); ?>
                                <select name="' . $field_name . '" class="form-control">
                                        <option value="0" <?php if ($id_' . $this->sname . '[\'' . $field_name . '\'] == 0) echo "selected=\"selected\"";?> class="form-control" id="' . $field_name . '">No</option>
                                        <option value="1" <?php if ($id_' . $this->sname . '[\'' . $field_name . '\'] == 1) echo "selected=\"selected\"";?> class="form-control" id="' . $field_name . '">Si</option>
                                </select>
                            </div>
                        </div>                            
                    </div>
';
                        }
                        else
                        {
                            $view_edit .= '
                            <div class="row clearfix">
                        <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                                        <label for="' . $field_name . '" class="control-label">' . $label . '</label>
                                        <div class="form-group">
                                                <input type="text" name="' . $field_name . '" value="<?php echo ($this->input->post(\'' . $field_name . '\') ? $this->input->post(\'' . $field_name . '\') : $id_' . $this->sname . '[\'' . $field_name . '\']); ?>" class="form-control" id="' . $field_name . '" />
                                        </div>
                                </div>                            
                            </div>';
                        }
                    }
                }
            }
        }

        $view_edit .= '
						<div class="form-group">
							<button type="submit" class="btn btn-success">
								<i class="fa fa-check"></i> Salva
							</button>
						</div>
						<?php echo form_close(); ?>
					</div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $this->load->view(\'templates/footer\');?>
';
        return $view_edit;
    }

    function build_view_listp($fields = NULL) {
        $view_listp = '<?php if (!defined(\'BASEPATH\')) exit(\'No direct script access allowed\');

$this->load->view(\'templates/header\');
//$arr = get_defined_vars();
//echo \'<pre>\';
//print_r($_ci_vars);
//echo \'</pre>\';

?>
<!--
<div class="row">
    <div class="col-lg-12">
        <h1 class="page-header">List ' . $this->controllername . '</h1>
    </div>
</div>
-->

<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-default">
            <nav class="navbar navbar-default">
                <div class="container-fluid">
                    <div class="navbar-header">
                        <a class="navbar-brand" href="#">Elenco ' . $this->fname . '</a>
                        <form class="navbar-form navbar-left form-inline" role="form" action="<?php echo base_url()."index.php/' . $this->controllername . '/' . $this->fname . '_search_id";?>" method="post">
                            
                                <input type="text" class="form-control" name="search_id" placeholder="Search id..." id="search_id">
                            
                                <button type="submit" class="btn btn-info" name="submit">Cerca...</button>
                        </form>
                            
                    </div>
                    <ul class="nav navbar-nav navbar-right">
                                <li><a href="<?php echo site_url(\'' . $this->controllername . '/create_' . $this->sname . '\'); ?>"><span class="glyphicon glyphicon-plus"></span> Aggiungi ' . $this->sname . '</a></li>
                    </ul>
                </div>
            </nav>

            <div class="panel-body"><p><?php echo $message;?></p>
                <div class="table-responsive">
                    <table class="table table-striped">                
                        <tr>
';

        foreach ($fields as $field)
        {
            $field_name = $field->name;
            $pk         = $field->primary_key;
            $label      = str_replace('_', ' ', $field_name);

            $view_listp .= ' 
                            <th>' . $label . '</th>';
        }
        $view_listp .= '
                            <th>Actions</th>
                        </tr>
                        
                <?php foreach($' . $this->fname . ' as $k){ ?>
                
                        <tr>
';
        foreach ($fields as $field)
        {
            $field_name = $field->name;
            $pk         = $field->primary_key;
            $label      = str_replace('_', ' ', $field_name);

            $view_listp .= ' 
                            <td><?php echo $k[\'' . $field_name . '\']; ?></td>';
        }
        $view_listp .= '
                            <td>
                                <!--<a href="<?php echo site_url(\'' . $this->controllername . '/create_' . $this->sname . '\'); ?>" class="btn btn-success btn-sm">Aggiungi ' . $this->sname . '</a>-->
                                <a href="<?php echo site_url(\'' . $this->controllername . '/edit_' . $this->sname . '/\'.$k[\'' . $this->tbl_pk . '\']); ?>" class="btn btn-warning btn-xs">Modifica</a>
                                <a href="<?php echo site_url(\'' . $this->controllername . '/remove_' . $this->sname . '/\'.$k[\'' . $this->tbl_pk . '\']); ?>" class="btn btn-danger btn-xs">Elimina</a>
                            </td>
                        </tr>
                        <?php } ?>
                    </table>
                    <div align="center"><?php echo $links;?></div>
                </div>
            </div>
        </div>
    </div>
</div>



<?php $this->load->view(\'templates/footer\');?>
        ';
        return $view_listp;
    }

    function build_view_listing($fields = NULL) {
        $view_list = '<?php if (!defined(\'BASEPATH\')) exit(\'No direct script access allowed\');

$this->load->view(\'templates/header\');
//$arr = get_defined_vars();
//echo \'<pre>\';
//print_r($_ci_vars);
//echo \'</pre>\';

?>
<!--
<div class="row">
    <div class="col-lg-12">
        <h1 class="page-header">List ' . $this->controllername . '</h1>
    </div>
</div>
-->
<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-default">
            <nav class="navbar navbar-default">
                <div class="container-fluid">
                    <div class="navbar-header">
                        <a class="navbar-brand" href="#">Elenco ' . $this->fname . '</a>
                        <form class="navbar-form navbar-left form-inline" role="form" action="<?php echo base_url()."index.php/' . $this->controllername . '/' . $this->fname . '_search_id";?>" method="post">
                            
                                <input type="text" class="form-control" name="search_id" placeholder="Search id..." id="search_id">
                            
                                <button type="submit" class="btn btn-info" name="submit">Cerca...</button>
                        </form>
                            
                    </div>
                    <ul class="nav navbar-nav navbar-right">
                                <li><a href="<?php echo site_url(\'' . $this->controllername . '/create_' . $this->sname . '\'); ?>"><span class="glyphicon glyphicon-plus"></span> Aggiungi ' . $this->sname . '</a></li>
                    </ul>
                </div>
            </nav>
            <div class="panel-body">
                <div class="table-responsive">
                    <table class="table table-striped">                
                        <tr>
';

        foreach ($fields as $field)
        {
            $field_name = $field->name;
            $pk         = $field->primary_key;
            $label      = str_replace('_', ' ', $field_name);

            $view_list .= ' 
                            <th>' . $label . '</th>';
        }
        $view_list .= '
                            <th>Actions</th>
                        </tr>
                        
                <?php foreach($' . $this->fname . ' as $k){ ?>
                
                        <tr>
';
        foreach ($fields as $field)
        {
            $field_name = $field->name;
            $pk         = $field->primary_key;
            $label      = str_replace('_', ' ', $field_name);

            $view_list .= ' 
                            <td><?php echo $k[\'' . $field_name . '\']; ?></td>';
        }
        $view_list .= '
                            <td>
                                <!--<a href="<?php echo site_url(\'' . $this->controllername . '/create_' . $this->sname . '\'); ?>" class="btn btn-success btn-sm">Aggiungi ' . $this->sname . '</a>-->
                                <a href="<?php echo site_url(\'' . $this->controllername . '/edit_' . $this->sname . '/\'.$k[\'' . $this->tbl_pk . '\']); ?>" class="btn btn-warning btn-xs">Modifica</a>
                                <a href="<?php echo site_url(\'' . $this->controllername . '/remove_' . $this->sname . '/\'.$k[\'' . $this->tbl_pk . '\']); ?>" class="btn btn-danger btn-xs">Elimina</a>
                            </td>
                        </tr>
                        <?php } ?>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>



<?php $this->load->view(\'templates/footer\');?>
        ';
        return $view_list;
    }

    function build_model($fields = NULL, $fktrovate = NULL) {
        if ($fields == NULL)
        {
            return FALSE;
        }
        $model = '<?php if (!defined("BASEPATH")) exit(\'No direct script access allowed\');
            
class ' . ucfirst($this->modelname) . ' extends CI_Model { 
                             
    public function __construct()
        {
            parent::__construct();
            $this->load->database();      
        }    
';

        $model .= '     
    function get_all_' . $this->fname . '()
        {';
        if ($fktrovate != NULL)
        {
            foreach ($fktrovate as $fkt)
            {
                $fktkname   = $fkt[0]['CONSTRAINT_NAME'];
                $fktcname   = $fkt[0]['COLUMN_NAME'];
                $fkttschema = $fkt[0]['REFERENCED_TABLE_SCHEMA'];
                $fkttname   = $fkt[0]['REFERENCED_TABLE_NAME'];
                $fktrcname  = $fkt[0]['REFERENCED_COLUMN_NAME'];

                $model .= '
            $this->db->join(\'' . $fkttname . '\', \'' . $fkttname . '.' . $fktrcname . ' = ' . $this->tname . '.' . $fktcname . '\');';
            }
        }
        else
        {
            $model .= '  
            //$this->db->join(\'ALTRATABELLA\', \'ALTRATABELLA.CAMPO = ' . $this->tname . '.CAMPO\');';
        }

        $model .= '         
            // $this->db->order_by("data", "desc");
            //$this->db->where(\'' . $this->obsolfield . '\',FALSE);
            $query = $this->db->get(\'' . $this->tname . '\');       
            return $query->result_array();
        }
        ';

        $model .= '     
    function get_all_' . $this->fname . '_active()
        {
            //$query = $this->db->get_where(\'' . $this->tname . '\',array(\'CAMPOOBSOLESCENZA\'=>FALSE));';
        if ($fktrovate != NULL)
        {
            foreach ($fktrovate as $fkt)
            {
                $fktkname   = $fkt[0]['CONSTRAINT_NAME'];
                $fktcname   = $fkt[0]['COLUMN_NAME'];
                $fkttschema = $fkt[0]['REFERENCED_TABLE_SCHEMA'];
                $fkttname   = $fkt[0]['REFERENCED_TABLE_NAME'];
                $fktrcname  = $fkt[0]['REFERENCED_COLUMN_NAME'];

                $model .= '         
            $this->db->join(\'' . $fkttname . '\', \'' . $fkttname . '.' . $fktrcname . ' = ' . $this->tname . '.' . $fktcname . '\');';
            }
        }
        else
        {
            $model .= '
            //$this->db->join(\'ALTRATABELLA\', \'ALTRATABELLA.CAMPO = ' . $this->tname . '.CAMPO\');';
        }

        $model .= '
            // $this->db->order_by("data", "desc");
            $this->db->where(\'' . $this->obsolfield . '\',FALSE);
            $query = $this->db->get(\'' . $this->tname . '\');       
            return $query->result_array();
        }
        ';        
        
        $model .= '
    function count_all_' . $this->fname . '()
        {
            //$this->db->where(\'clcodcol\',$id);
            $this->db->from(\'' . $this->tname . '\');
            //$this->db->where(\'' . $this->obsolfield . '\',FALSE);
            //$this->db->get_where(\'' . $this->tname . '\',array(\'CAMPOOBSOLESCENZA\'=>FALSE));
            return $this->db->count_all_results();
        }

    function count_all_' . $this->fname . '_active()
        {
            $this->db->from(\'' . $this->tname . '\');
            $this->db->where(\'' . $this->obsolfield . '\',FALSE);
            return $this->db->count_all_results();
        }
    
    function fetch_' . $this->fname . '($limit,$offset)
        {
            $this->db->limit($limit,$offset);';
        if ($fktrovate != NULL)
        {
            foreach ($fktrovate as $fkt)
            {
                $fktkname   = $fkt[0]['CONSTRAINT_NAME'];
                $fktcname   = $fkt[0]['COLUMN_NAME'];
                $fkttschema = $fkt[0]['REFERENCED_TABLE_SCHEMA'];
                $fkttname   = $fkt[0]['REFERENCED_TABLE_NAME'];
                $fktrcname  = $fkt[0]['REFERENCED_COLUMN_NAME'];

                $model .= '
            $this->db->join(\'' . $fkttname . '\', \'' . $fkttname . '.' . $fktrcname . ' = ' . $this->tname . '.' . $fktcname . '\');';
            }
        }
        else
        {
            $model .= '            
            //$this->db->join(\'ALTRATABELLA\', \'ALTRATABELLA.CAMPO = ' . $this->tname . '.CAMPO\');';
        }

        $model .= '
            //$this->db->where(\'' . $this->obsolfield . '\',FALSE);            
            $this->db->from(\'' . $this->tname . '\');
            $query = $this->db->get();
            if ($query->num_rows() > 0){
                return $query->result_array();
            }else{
                return array();
            }
        }
    
    function fetch_' . $this->fname . '_active($limit,$offset)
        {
            $this->db->limit($limit,$offset);';
        if ($fktrovate != NULL)
        {
            foreach ($fktrovate as $fkt)
            {
                $fktkname   = $fkt[0]['CONSTRAINT_NAME'];
                $fktcname   = $fkt[0]['COLUMN_NAME'];
                $fkttschema = $fkt[0]['REFERENCED_TABLE_SCHEMA'];
                $fkttname   = $fkt[0]['REFERENCED_TABLE_NAME'];
                $fktrcname  = $fkt[0]['REFERENCED_COLUMN_NAME'];

                $model .= '           
            $this->db->join(\'' . $fkttname . '\', \'' . $fkttname . '.' . $fktrcname . ' = ' . $this->tname . '.' . $fktcname . '\');';
            }
        }
        else
        {
            $model .= '          
            //$this->db->join(\'ALTRATABELLA\', \'ALTRATABELLA.CAMPO = ' . $this->tname . '.CAMPO\');';
        }

        $model .= '
            $this->db->where(\'' . $this->obsolfield . '\',FALSE);
            $this->db->from(\'' . $this->tname . '\');
            $query = $this->db->get();
            if ($query->num_rows() > 0){
                return $query->result_array();
            }else{
                return $query->result_array();
            }
        }
    
    function search_' . $this->fname . '_by_id($id)
        {
            $this->db->select(\'*\');
            $this->db->from(\'' . $this->tname . '\');
            $this->db->like(\'' . $this->tbl_pk . '\',$id);
            $query = $this->db->get();
            if ($query->num_rows() > 0){
                return $query->result_array();
            }else{
                return FALSE;
            }    
        }

    function get_' . $this->sname . '_by_id($id)
        {
            $this->db->select(\'*\');
            $this->db->from(\'' . $this->tname . '\');
            //$this->db->where(\'' . $this->obsolfield . '\',FALSE);
            $this->db->where(\'' . $this->tbl_pk . '\',$id);';
        if ($fktrovate != NULL)
        {
            foreach ($fktrovate as $fkt)
            {
                $fktkname   = $fkt[0]['CONSTRAINT_NAME'];
                $fktcname   = $fkt[0]['COLUMN_NAME'];
                $fkttschema = $fkt[0]['REFERENCED_TABLE_SCHEMA'];
                $fkttname   = $fkt[0]['REFERENCED_TABLE_NAME'];
                $fktrcname  = $fkt[0]['REFERENCED_COLUMN_NAME'];

                $model .= '
            $this->db->join(\'' . $fkttname . '\', \'' . $fkttname . '.' . $fktrcname . ' = ' . $this->tname . '.' . $fktcname . '\');';
            }
        }
        else
        {
            $model .= '               
            //$this->db->join(\'ALTRATABELLA\', \'ALTRATABELLA.CAMPO = ' . $this->tname . '.CAMPO\');';
        }

        $model .= '
            $this->db->limit(1);
            $query = $this->db->get();

            return $query->row_array();  
        }
        ';

        $model .= '
            
    function add_' . $this->sname . '($params) 
        {
            $this->db->insert(\'' . $this->tname . '\',$params);
            return $this->db->insert_id();
        }
        ';

        $model .= '
            
    function update_' . $this->sname . '($id,$params) 
        {
            $this->db->where(\'' . $this->tbl_pk . '\',$id);
            $response = $this->db->update(\'' . $this->tname . '\',$params);
            if($response)
            {
                $this->session->set_flashdata(\'flashSuccess\', \'' . $this->sname . ' updated successfully\');
                //return "' . $this->sname . ' updated successfully";
            }
            else
            {
                $this->session->set_flashdata(\'flashError\', \'Error occuring while updating ' . $this->sname . '\');
                //return "Error occuring while updating ' . $this->sname . '";
            }
        }
        ';

        $model .= '
            
    function delete_' . $this->sname . '_by_id($id) 
        {
            $response = $this->db->delete(\'' . $this->tname . '\',array(\'' . $this->tbl_pk . '\'=>$id));
            if($response)
            {
                $this->session->set_flashdata(\'flashSuccess\', \'' . $this->sname . ' deleted successfully\');
                //return "' . $this->sname . ' deleted successfully";
            }
            else
            {
                $this->session->set_flashdata(\'flashError\', \'Error occuring while deleting ' . $this->sname . '\');
                //return "Error occuring while deleting ' . $this->sname . '";
            }
//          $this->db->where(\'' . $this->tbl_pk . '\',$id);
//          $this->db->update(\'' . $this->tname . '\',array(\'CAMPOOBSOLESCENZA\'=>FALSE));
        }
        ';

        $model .= '
    function get_last_' . $this->sname . '() 
        {
            $this->db->select(\'*\');
            $this->db->from(\'' . $this->tname . '\');
            $this->db->order_by(\'' . $this->tbl_pk . '\', "desc");
            $this->db->limit(1);
            //$this->db->where(\'' . $this->obsolfield . '\',FALSE);
            $query = $this->db->get();        

            return $query->row_array();
        }
        ';

        $model .= '            
    function get_all_' . $this->sname . '_by_QUALCOSA($par) 
        {
            $this->db->select(\'*\');
            $this->db->from(\'' . $this->tname . '\');
            //$this->db->where(\'' . $this->obsolfield . '\',FALSE);
            $this->db->like(\'CAMPODACONFRONTARE\',$par);';
        if ($fktrovate != NULL)
        {
            foreach ($fktrovate as $fkt)
            {
                $fktkname   = $fkt[0]['CONSTRAINT_NAME'];
                $fktcname   = $fkt[0]['COLUMN_NAME'];
                $fkttschema = $fkt[0]['REFERENCED_TABLE_SCHEMA'];
                $fkttname   = $fkt[0]['REFERENCED_TABLE_NAME'];
                $fktrcname  = $fkt[0]['REFERENCED_COLUMN_NAME'];

                $model .= '               
            $this->db->join(\'' . $fkttname . '\', \'' . $fkttname . '.' . $fktrcname . ' = ' . $this->tname . '.' . $fktcname . '\');';
            }
        }
        else
        {
            $model .= '               
            //$this->db->join(\'ALTRATABELLA\', \'ALTRATABELLA.CAMPO = ' . $this->tname . '.CAMPO\');';
        }

        $model .= '
            $this->db->order_by(\'CAMPOPERORDINARE\', "desc");
            $query = $this->db->get();

            return $query->result_array();
        }
        ';

        $model .= '            
    function get_all_' . $this->sname . '_QUALCOSA($par) 
        {
            $this->db->select(\'*\');
            $this->db->from(\'' . $this->tname . '\');';
        if ($fktrovate != NULL)
        {
            foreach ($fktrovate as $fkt)
            {
                $fktkname   = $fkt[0]['CONSTRAINT_NAME'];
                $fktcname   = $fkt[0]['COLUMN_NAME'];
                $fkttschema = $fkt[0]['REFERENCED_TABLE_SCHEMA'];
                $fkttname   = $fkt[0]['REFERENCED_TABLE_NAME'];
                $fktrcname  = $fkt[0]['REFERENCED_COLUMN_NAME'];

                $model .= '               
            $this->db->join(\'' . $fkttname . '\', \'' . $fkttname . '.' . $fktrcname . ' = ' . $this->tname . '.' . $fktcname . '\');';
            }
        }
        else
        {
            $model .= '               
            //$this->db->join(\'ALTRATABELLA\', \'ALTRATABELLA.CAMPO = ' . $this->tname . '.CAMPO\');';
        }

        $model .= '
            $this->db->where(\'PARAMETRO\', $par); 
            //$this->db->where(\'' . $this->obsolfield . '\',FALSE);
            $this->db->order_by(\'' . $this->tname . '.CAMPO\', "desc");
            $query = $this->db->get();

            return $query->result_array();
        }
        ';

        $model .= '            
    function is_present_' . $this->sname . '_id($id) 
        {
            $record = $this->get_' . $this->sname . '_by_id($id);
            if($record)
            {
                return $record;
            }
            else
            {
                return FALSE;
            }
        }
        ';

        $model .= ' 
//  POST CRUD




}';
        return $model;
    }

    function download() {
        $this->load->library('zip');
        $controller_date          = $this->controller_data;
        $model_date               = $this->model_data;
        $create_view_date         = $this->create_data;
        $edit_view_date           = $this->edit_data;
        $viewdetail_view_date     = $this->viewdetail_data;
        $create_list_date         = $this->list_data;
        $create_listp_date        = $this->listp_data;
        $htm_file_data            = $this->ind_htm_data;
        $header_date              = $this->header_data;
        $footer_date              = $this->footer_data;
        $controller_file_name     = 'controllers/' . ucfirst($this->controllername) . '.php';
        $model_file_name          = 'models/' . ucfirst($this->modelname) . '.php';
        $createview_file_name     = 'views/' . $this->tname . '/' . $this->create_viewname . '.php';
        $editview_file_name       = 'views/' . $this->tname . '/' . $this->edit_viewname . '.php';
        $viewdetailview_file_name = 'views/' . $this->tname . '/' . $this->viewdetail_viewname . '.php';
        $listview_file_name       = 'views/' . $this->tname . '/' . $this->listviewname . '.php';
        $listviewp_file_name      = 'views/' . $this->tname . '/' . $this->list_viewpagname . '.php';
        $htm_file_name            = 'views/' . $this->tname . '/' . $this->ind_htm_name . '.html';
        $this->zip->add_data($controller_file_name, $controller_date);
        $this->zip->add_data($model_file_name, $model_date);
        $this->zip->add_data($createview_file_name, $create_view_date);
        $this->zip->add_data($editview_file_name, $edit_view_date);
        $this->zip->add_data($viewdetailview_file_name, $viewdetail_view_date);
        $this->zip->add_data($listview_file_name, $create_list_date);
        $this->zip->add_data($listviewp_file_name, $create_listp_date);
        $this->zip->add_data($htm_file_name, $htm_file_data);
        $this->zip->archive('temp/' . $this->controllername . '.zip');
        $this->zip->download($this->controllername . '.zip');
    }

}
