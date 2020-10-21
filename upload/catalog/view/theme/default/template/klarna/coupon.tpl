<?php if ($coupon) { ?>

	<!-- COUPON: COUPON IS SET -->

	<div class="input-group">
		<div class="form-control"><?php echo $text_coupon_code; ?> <b><?php echo $coupon['code']; ?></b></div>
		<span class="input-group-btn">
			<button class="btn btn-danger" id="kco-coupon-remove"><i class="glyphicon glyphicon-trash"></i></button>
		</span>
	</div>

<?php } else { ?>

	<!-- COUPON: COUPON IS NOT SET -->

	<div class="input-group">
		<input type="text" class="form-control" name="coupon" />
		<span class="input-group-btn">
			<button class="btn btn-primary" id="kco-coupon-add" type="button"><i class="glyphicon glyphicon-plus"></i></button>
		</span>
	</div>

<?php } ?>
