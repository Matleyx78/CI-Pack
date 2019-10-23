<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

$this->load->view('templates/header');
//$arr = get_defined_vars();
//echo '<pre>';
//print_r($_ci_vars);
//echo '</pre>';
?>
<div class="row">
<div class="col-md-6">
    <div style="text-align: center"><h3>Crud</h3></div><br/>
<?php echo form_open('crud/generator',array("class"=>"form-horizontal")); ?>    
<div class="row">
	<div class="form-group">
		<label for="tipo" class="col-md-2 control-label">Tabella</label>
		<div class="col-md-4">
                    <select id="table_name" name="tname" class="form-control">
                        <?php foreach($result as $row){ ?>
                        <option value="<?php echo $row['TABLES']; ?>"><?php echo $row['TABLES']; ?></option>
                        <?php } ?>
                    </select>
		</div>
	</div>
    	<div class="form-group">
		<label for="data" class="col-md-2 control-label">Controller</label>
		<div class="col-md-4">
			<input type="text" name="cname" value="<?php echo ($this->input->post('cname')); ?>" class="form-control" id="cname" />
			

			</div>
	</div>
        	<div class="form-group">
		<label for="data" class="col-md-2 control-label">Plurale</label>
		<div class="col-md-4">
			<input type="text" name="fname" value="<?php echo ($this->input->post('fname')); ?>" class="form-control" id="fname" />
			

			</div>
	</div>
            	<div class="form-group">
		<label for="data" class="col-md-2 control-label">Singolare</label>
		<div class="col-md-4">
			<input type="text" name="sname" value="<?php echo ($this->input->post('sname')); ?>" class="form-control" id="sname" />
			

			</div>
	</div>
            	<div class="form-group">
		<label for="data" class="col-md-2 control-label">Campo Obsolescenza</label>
		<div class="col-md-4">
			<input type="text" name="obsofield" value="<?php echo ($this->input->post('obsofield')); ?>" class="form-control" id="obsofield" />
			

			</div>
	</div>    
	<div class="form-group">
		<div class="col-sm-offset-2 col-sm-10">
<!--			<button type="submit" class="btn btn-success">Genera</button>-->
                        <button type="submit" name="download" class="btn btn-success">Download</button>
<!--                        <input type="submit" name="download" class="btn btn-lg btn-primary " value="Dowload">-->
        </div>
	</div>
</div>

<?php echo form_close(); ?>    
    
</div>
<div class="col-md-6">

</div>
</div>


<?php $this->load->view('templates/footer');?>
