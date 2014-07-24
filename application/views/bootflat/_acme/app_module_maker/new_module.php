<div class="module-header">

	<div class="row">

		<div class="col-xs-10 col-sm-10 col-md-10 col-lg-10">
			<h1><?php echo lang($this->label) ?>
			<?php if($this->description != ''){ ?><small>// <?php echo lang($this->description)?></small> <?php } ?>
			</h1>
		</div>

	</div>

</div>

<div class="module-body">

	<?php if ( ! $path_permissions) {?>
	<div class="row">
		<div class="col-sm-12">
			<?php 
			echo message('error', '', 
			lang('Check write permissions for the following paths and files before you continue') . '
			<br />
			<strong>> application/controllers</strong><br />
			<strong>> application/models</strong><br />
			<strong>> application/views</strong><br />
			<strong>> application/core/acme/engine_files/maker_template_controller.php</strong><br />
			<strong>> application/core/acme/engine_files/maker_template_model.php</strong><br />
			'); ?>
		</div>
	</div>
	<?php } ?>

	<?php if ( ! $timezone) {?>
	<div class="row">
		<div class="col-sm-12">
			<?php 
			echo message('error', '', 
			lang('Check your php.ini settings before you continue') . '
			<br />
			<strong>> date.timezone value is not set</strong><br />
			'); ?>
		</div>
	</div>
	<?php } ?>

	<div class="row">

		<div class="col-sm-8 col-md-7">
			
			<form action="<?php echo URL_ROOT ?>/app_module_maker/new_module/true" method="post">
			
				<h3 style="margin: 0 0 30px 0"><?php echo lang('New module') ?></h3>

				<div class="form-group">
	                <label>
	                	<?php echo lang('Table') ?>
	                	<i class="fa fa-fw fa-info-circle" data-toggle="tooltip" data-placement="right" data-original-title="<?php echo lang('Section Forms below will be ignored if you leave this field empty or if the informed table doesn\'t exist') ?>"></i>
	                </label>
	                <input type="text" name="table_name" id="table_name" class="form-control" />
	            </div>

	            <div class="form-group">
	                <label>
	                	<?php echo lang('Controller name') ?>*
	                	<i class="fa fa-fw fa-info-circle" data-toggle="tooltip" data-placement="right" data-original-title="<?php echo lang('Some MVC files will be created with this name') ?>"></i>
	                </label>
	                <input type="text" id="controller" name="controller" class="form-control validate[required,custom[className,funcCall[validate_controller_custom]]]" value="" />
	            </div>

	            <div class="form-group">
	                <label><?php echo lang('Label') ?>*</label>
	                <input name="label" id="label" class="form-control validate[required]" value="" />
	            </div>

	            <div class="form-group">
	                <label><?php echo lang('Description') ?></label>
	               	<input type="text" id="description" name="description" class="form-control" value="" />
	            </div>

		        <div class="form-group">
	                <label><?php echo lang('Image URL') ?></label>
	                <input type="text" id="url_img" name="url_img" class="form-control" value="" />
	            </div>

	            <div class="form-group">
	                <label><?php echo lang('SQL List') ?></label>
	                <textarea name="sql_list" id="sql_list" class="form-control" style="height: 180px"></textarea>
	            </div>

	            <h3 style="margin: 50px 0 30px 0">
	            	<?php echo lang('Access menu') ?>
	            	<i class="fa fa-fw fa-info-circle" data-toggle="tooltip" data-placement="right" data-original-title="<?php echo lang('A menu pointing to this module will be created for the following groups') ?>"></i>
	            </h3>

				<div class="form-group">
	                <label>
	                	<?php echo lang('Create menu for following groups') ?>
	                	<small class="text-muted">// <?php echo lang('type and hit Enter'); ?></small>
	                </label>
	                <input type="text" name="menu_groups" id="menu_groups" class="form-control" data-role="tagsinput" />
	            </div>

	            <h3 style="margin: 50px 0 30px 0">
	            	<?php echo lang('Forms') ?>
	            	<i class="fa fa-fw fa-info-circle" data-toggle="tooltip" data-placement="right" data-original-title="<?php echo lang('A permission with the same name will be created for each checked form') ?>"></i>
	            </h3>

				<div class="form-group">
	                <label>
	                	<?php echo lang('Enable the following forms to the new module') ?>
	                </label>
	                <div class="checkbox">
			            <label>
			                <input type="checkbox" name="forms[]" id="form-insert" value="insert" />
			                <span>INSERT</span>
			            </label>
			        </div>
			        <div class="checkbox">
			            <label>
			                <input type="checkbox" name="forms[]" id="form-update" value="update" />
			                <span>UPDATE</span>
			            </label>
			        </div>
			        <div class="checkbox">
			            <label>
			                <input type="checkbox" name="forms[]" id="form-delete" value="delete" />
			                <span>DELETE</span>
			            </label>
			        </div>
			        <div class="checkbox">
			            <label>
			                <input type="checkbox" name="forms[]" id="form-view" value="view" />
			                <span>VIEW</span>
			            </label>
			        </div>
	            </div>

				<div class="row bottom-group-buttons">
		            <div class="col-sm-12">
		                <input class="btn btn-primary" type="submit" value="<?php echo lang('Create') ?>" />
		                <a class="btn btn-default" href="<?php echo URL_ROOT ?>"><?php echo lang('Cancel') ?></a>
		            </div>
		        </div>

			</form>

	    </div>

	</div>

</div>

<link rel="stylesheet" type="text/css" href="<?php echo URL_CSS ?>/plugins/icheck/flat/red.css" />
<link rel="stylesheet" type="text/css" href="<?php echo URL_CSS ?>/plugins/magicsuggest/magicsuggest.css" />
<link rel="stylesheet" type="text/css" href="<?php echo URL_CSS ?>/plugins/validationEngine/validationEngine.jquery.css" />
<script src="<?php echo URL_JS ?>/plugins/icheck/icheck.min.js"></script>
<script src="<?php echo URL_JS ?>/plugins/magicsuggest/magicsuggest-min.js"></script>
<script src="<?php echo URL_JS ?>/plugins/validationEngine/jquery.validationEngine.js"></script>
<script src="<?php echo URL_JS ?>/plugins/validationEngine/jquery.validationEngine-<?php echo $this->session->userdata('language') ?>.js"></script>

<script>

    // tooltips
    $('body').tooltip( { selector: "[data-toggle=tooltip]" } );

    // Set validations to all forms
    $('form').validationEngine('attach', {
        
        promptPosition : "bottomRight",
        onValidationComplete: function (form, status) {
        	if( ! status ) 
        		return false;

        	$.enable_loading();
        	return true;
        }
    });

    // Reposition the alerts from form
    $( window ).resize( function () {
        $("form").validationEngine('updatePromptsPosition');
    });

    var groups = <?php echo $groups ?>;

    // tags input
    $('input#menu_groups').magicSuggest({
    	allowFreeEntries : false,
    	data : groups
    });

    // ichecks
    $('input[type="checkbox"]').iCheck({
    	checkboxClass: 'icheckbox_flat-red',
    	radioClass: 'iradio_flat-red'
    });

    // Disable all fields in case of needed permissions
    <?php if ( ! $path_permissions || ! $timezone) {?>
    $('form input, form button, form textarea, form select').attr('disabled', true);
   	<?php } ?>

   	// custom validation for controller name
   	// this function trigger an ajax that check if module already exist
    var validate_controller_custom = function(field, rules, i, options) {

        var exist = false;
        
        $.ajax({
            
            url: $('#URL_ROOT').val() + '/app_module_maker/check_controller/',
            context: document.body,
            cache: false,
            async: false,
            data: { 'controller' : field.val() },
            type: 'POST',
            success: function(data){

                json = $.parseJSON(data);
                
                if(json.return == true)
                    exist = true;
            }
        });
        
        if( exist )
            return "<?php echo lang('A module with this controller already exist') ?>";
    };

</script>