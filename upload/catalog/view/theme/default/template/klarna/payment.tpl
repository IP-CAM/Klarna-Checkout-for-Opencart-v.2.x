<?php if ($snippet) { ?>

	<div id="kco-snippet-wrapper"><?php echo $snippet; ?></div>

<?php } else { ?>

	<p><b><?php echo $heading_error; ?></b><br/><?php echo $error_unknown; ?></p>
	<div class="text-center">
		<img src="https://cdn.klarna.com/1.0/shared/image/generic/badge/sv_se/checkout/short-blue.png?width=300" class="img-responsive" />
	</div>

<?php } ?>
