<?php if ($voucher) { ?>

	<!-- VOUCHER: VOUCHER IS SET -->

	<div class="input-group">
		<div class="form-control"><?php echo $text_voucher_code; ?> <b><?php echo $voucher['code']; ?></b></div>
		<span class="input-group-btn">
			<button class="btn btn-danger" id="kco-voucher-remove"><i class="glyphicon glyphicon-trash"></i></button>
		</span>
	</div>

<?php } else { ?>

	<!-- VOUCHER: VOUCHER IS NOT SET -->

	<div class="input-group">
		<input type="text" class="form-control" name="voucher" />
		<span class="input-group-btn">
			<button class="btn btn-primary" id="kco-voucher-add" type="button"><i class="glyphicon glyphicon-plus"></i></button>
		</span>
	</div>

<?php } ?>
