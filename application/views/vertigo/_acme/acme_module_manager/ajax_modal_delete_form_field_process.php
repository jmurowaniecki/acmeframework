<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="pt-br" xml:lang="pt-br">
<head>
	<meta http-equiv="content-type" content="text/html; charset=utf-8" />
    <title><?php echo APP_TITLE; ?></title>
	<?php echo $this->template->load_array_config_js_files(); ?>
	<?php echo $this->template->load_array_config_css_files(); ?>
	<script type="text/javascript" language="javascript">
		$(document).ready(function () {
			// Carrega tabela novamente
			parent.ajax_load_box_config_form_fields('<?php echo get_value($field, 'operation') ?>', <?php echo get_value($field, 'id_module') ?>);
			
			// Fecha a janela
			parent.close_modal();
		});		
	</script>
</head>
<body>
</body>
</html>
