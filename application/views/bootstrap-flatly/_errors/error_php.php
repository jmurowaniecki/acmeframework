<?php if(ENVIRONMENT != 'production') {?>
<span class="text-danger">
	<strong><?php echo $message ?></strong> in <span><?php echo $filepath?> on line <?php echo $line ?></span>
</span>
<?php } ?>