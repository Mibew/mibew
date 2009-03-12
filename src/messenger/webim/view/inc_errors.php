<?php if( isset($errors) && count($errors) > 0 ) { ?>
	<div class="errinfo">
		<img src='<?php echo $webimroot ?>/images/icon_err.gif' width="40" height="40" border="0" alt="" class="left"/>
<?php
		print getlocal("errors.header");
		foreach( $errors as $e ) {
			print getlocal("errors.prefix");
			print $e;
			print getlocal("errors.suffix");
		}
		print getlocal("errors.footer");
?>
	</div>
	<br clear="all"/>
<?php } ?>
