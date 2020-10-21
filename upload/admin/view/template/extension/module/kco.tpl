<?php echo $header; ?>
<?php echo $column_left; ?>

<div id="content">

	<div class="page-header">
		<div class="container-fluid">

			<div class="pull-right">
				<button type="submit" form="form-filter" data-toggle="tooltip" title="<?php echo $button_save; ?>" class="btn btn-primary"><i class="fa fa-save"></i></button>
				<a href="<?php echo $cancel; ?>" data-toggle="tooltip" title="<?php echo $button_cancel; ?>" class="btn btn-default"><i class="fa fa-reply"></i></a>
			</div>

			<h1><?php echo $heading_title; ?></h1>

			<ul class="breadcrumb">
				<?php foreach ($breadcrumbs as $breadcrumb) { ?>
					<li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li>
				<?php } ?>
			</ul>

		</div>
	</div>

	<div class="container-fluid">

		<?php if ($error_warning) { ?>
			<div class="alert alert-danger">
				<i class="fa fa-exclamation-circle"></i> <?php echo $error_warning; ?>
				<button type="button" class="close" data-dismiss="alert">&times;</button>
			</div>
		<?php } ?>

		<div class="panel panel-default">
			<div class="panel-heading">
			<h3 class="panel-title"><i class="fa fa-pencil"></i> <?php echo $text_edit; ?></h3>
		</div>

		<div class="panel-body">
			<form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form-filter" class="form-horizontal">

				<ul class="nav nav-tabs">
					<li class="active"><a href="#tab-general" data-toggle="tab"><?php echo $tab_general; ?></a></li>
					<li><a href="#tab-settings" data-toggle="tab"><?php echo $tab_settings; ?></a></li>
					<li><a href="#tab-au" data-toggle="tab"><?php echo $tab_au; ?></a></li>
					<li><a href="#tab-dk" data-toggle="tab"><?php echo $tab_dk; ?></a></li>
					<li><a href="#tab-de" data-toggle="tab"><?php echo $tab_de; ?></a></li>
					<li><a href="#tab-fi" data-toggle="tab"><?php echo $tab_fi; ?></a></li>
					<li><a href="#tab-no" data-toggle="tab"><?php echo $tab_no; ?></a></li>
					<li><a href="#tab-se" data-toggle="tab"><?php echo $tab_se; ?></a></li>
				</ul>

				<div class="tab-content">

					<!-- GENERAL -->
					<div class="tab-pane active" id="tab-general">

						<div class="form-group">
							<label class="col-sm-2 control-label" for="input-test_mode"><?php echo $entry_test_mode; ?></label>
							<div class="col-sm-10">
								<select name="kco_test_mode" id="input-test_mode" class="form-control">
									<?php if ($kco_test_mode) { ?>
										<option value="1" selected="selected"><?php echo $text_enabled; ?></option>
										<option value="0"><?php echo $text_disabled; ?></option>
									<?php } else { ?>
										<option value="1"><?php echo $text_enabled; ?></option>
										<option value="0" selected="selected"><?php echo $text_disabled; ?></option>
									<?php } ?>
								</select>
							</div>
						</div>

						<div class="form-group">
							<label class="col-sm-2 control-label" for="input-log_mode"><?php echo $entry_log_mode; ?></label>
							<div class="col-sm-10">
								<select name="kco_log_mode" id="input-log_mode" class="form-control">
									<?php if ($kco_log_mode) { ?>
										<option value="1" selected="selected"><?php echo $text_enabled; ?></option>
										<option value="0"><?php echo $text_disabled; ?></option>
									<?php } else { ?>
										<option value="1"><?php echo $text_enabled; ?></option>
										<option value="0" selected="selected"><?php echo $text_disabled; ?></option>
									<?php } ?>
								</select>
							</div>
						</div>

						<div class="form-group">
							<label class="col-sm-2 control-label" for="input-status_account"><?php echo $entry_status_account; ?></label>
							<div class="col-sm-10">
								<select name="kco_status_account" id="input-status_account" class="form-control">
									<?php if ($kco_status_account) { ?>
										<option value="1" selected="selected"><?php echo $text_enabled; ?></option>
										<option value="0"><?php echo $text_disabled; ?></option>
									<?php } else { ?>
										<option value="1"><?php echo $text_enabled; ?></option>
										<option value="0" selected="selected"><?php echo $text_disabled; ?></option>
									<?php } ?>
								</select>
							</div>
						</div>

						<div class="form-group">
							<label class="col-sm-2 control-label" for="input-status_coupon"><?php echo $entry_status_coupon; ?></label>
							<div class="col-sm-10">
								<select name="kco_status_coupon" id="input-status_coupon" class="form-control">
									<?php if ($kco_status_coupon) { ?>
										<option value="1" selected="selected"><?php echo $text_enabled; ?></option>
										<option value="0"><?php echo $text_disabled; ?></option>
									<?php } else { ?>
										<option value="1"><?php echo $text_enabled; ?></option>
										<option value="0" selected="selected"><?php echo $text_disabled; ?></option>
									<?php } ?>
								</select>
							</div>
						</div>

						<div class="form-group">
							<label class="col-sm-2 control-label" for="input-status_voucher"><?php echo $entry_status_voucher; ?></label>
							<div class="col-sm-10">
								<select name="kco_status_voucher" id="input-status_voucher" class="form-control">
									<?php if ($kco_status_voucher) { ?>
										<option value="1" selected="selected"><?php echo $text_enabled; ?></option>
										<option value="0"><?php echo $text_disabled; ?></option>
									<?php } else { ?>
										<option value="1"><?php echo $text_enabled; ?></option>
										<option value="0" selected="selected"><?php echo $text_disabled; ?></option>
									<?php } ?>
								</select>
							</div>
						</div>

						<div class="form-group">
							<label class="col-sm-2 control-label" for="input-status_comment"><?php echo $entry_status_comment; ?></label>
							<div class="col-sm-10">
								<select name="kco_status_comment" id="input-status_comment" class="form-control">
									<?php if ($kco_status_comment) { ?>
										<option value="1" selected="selected"><?php echo $text_enabled; ?></option>
										<option value="0"><?php echo $text_disabled; ?></option>
									<?php } else { ?>
										<option value="1"><?php echo $text_enabled; ?></option>
										<option value="0" selected="selected"><?php echo $text_disabled; ?></option>
									<?php } ?>
								</select>
							</div>
						</div>

						<div class="form-group">
							<label class="col-sm-2 control-label" for="input-status_analytics"><?php echo $entry_status_analytics; ?></label>
							<div class="col-sm-10">
								<select name="kco_status_analytics" id="input-status_analytics" class="form-control">
									<?php if ($kco_status_analytics) { ?>
										<option value="1" selected="selected"><?php echo $text_enabled; ?></option>
										<option value="0"><?php echo $text_disabled; ?></option>
									<?php } else { ?>
										<option value="1"><?php echo $text_enabled; ?></option>
										<option value="0" selected="selected"><?php echo $text_disabled; ?></option>
									<?php } ?>
								</select>
							</div>
						</div>

						<div class="form-group">
							<label class="col-sm-2 control-label" for="input-status_checkout"><?php echo $entry_status_checkout; ?></label>
							<div class="col-sm-10">
								<select name="kco_status_checkout" id="input-status_checkout" class="form-control">
									<?php if ($kco_status_checkout) { ?>
										<option value="1" selected="selected"><?php echo $text_enabled; ?></option>
										<option value="0"><?php echo $text_disabled; ?></option>
									<?php } else { ?>
										<option value="1"><?php echo $text_enabled; ?></option>
										<option value="0" selected="selected"><?php echo $text_disabled; ?></option>
									<?php } ?>
								</select>
							</div>
						</div>

						<div class="form-group">
							<label class="col-sm-2 control-label" for="input-order_status_id"><?php echo $entry_order_status; ?></label>
							<div class="col-sm-10">
								<select name="kco_order_status_id" id="input-order_status_id" class="form-control">
									<?php foreach ($order_statuses as $order_status) { ?>
										<?php if ($order_status['order_status_id'] == $kco_order_status_id) { ?>
											<option value="<?php echo $order_status['order_status_id']; ?>" selected="selected"><?php echo $order_status['name']; ?></option>
										<?php } else { ?>
											<option value="<?php echo $order_status['order_status_id']; ?>"><?php echo $order_status['name']; ?></option>
										<?php } ?>
									<?php } ?>
								</select>
							</div>
						</div>

						<div class="form-group">
							<label class="col-sm-2 control-label" for="input-status"><?php echo $entry_status; ?></label>
							<div class="col-sm-10">
								<select name="kco_status" id="input-status" class="form-control">
									<?php if ($kco_status) { ?>
										<option value="1" selected="selected"><?php echo $text_enabled; ?></option>
										<option value="0"><?php echo $text_disabled; ?></option>
									<?php } else { ?>
										<option value="1"><?php echo $text_enabled; ?></option>
										<option value="0" selected="selected"><?php echo $text_disabled; ?></option>
									<?php } ?>
								</select>
							</div>
						</div>

					</div>

					<!-- SETTINGS -->
					<div class="tab-pane" id="tab-settings">

						<div class="form-group">
							<label class="col-sm-2 control-label" for="input-logo"><?php echo $entry_logo; ?></label>
							<div class="col-sm-10">
								<select name="kco_logo" id="input-logo" class="form-control">
									<?php if ($kco_logo) { ?>
										<option value="1" selected="selected"><?php echo $text_enabled; ?></option>
										<option value="0"><?php echo $text_disabled; ?></option>
									<?php } else { ?>
										<option value="1"><?php echo $text_enabled; ?></option>
										<option value="0" selected="selected"><?php echo $text_disabled; ?></option>
									<?php } ?>
								</select>
							</div>
						</div>

						<div class="form-group">
							<label class="col-sm-2 control-label" for="input-auto_focus"><?php echo $entry_auto_focus; ?></label>
							<div class="col-sm-10">
								<select name="kco_auto_focus" id="input-auto_focus" class="form-control">
									<?php if ($kco_auto_focus) { ?>
										<option value="1" selected="selected"><?php echo $text_enabled; ?></option>
										<option value="0"><?php echo $text_disabled; ?></option>
									<?php } else { ?>
										<option value="1"><?php echo $text_enabled; ?></option>
										<option value="0" selected="selected"><?php echo $text_disabled; ?></option>
									<?php } ?>
								</select>
							</div>
						</div>

						<div class="form-group">
							<label class="col-sm-2 control-label" for="input-product_option"><?php echo $entry_product_option; ?></label>
							<div class="col-sm-10">
								<select name="kco_product_option" id="input-product_option" class="form-control">
									<?php if ($kco_product_option) { ?>
										<option value="1" selected="selected"><?php echo $text_enabled; ?></option>
										<option value="0"><?php echo $text_disabled; ?></option>
									<?php } else { ?>
										<option value="1"><?php echo $text_enabled; ?></option>
										<option value="0" selected="selected"><?php echo $text_disabled; ?></option>
									<?php } ?>
								</select>
							</div>
						</div>

						<div class="form-group">
							<label class="col-sm-2 control-label" for="input-override_shipping"><?php echo $entry_override_shipping; ?></label>
							<div class="col-sm-10">
								<select name="kco_override_shipping" id="input-override_shipping" class="form-control">
									<?php if ($kco_override_shipping) { ?>
										<option value="1" selected="selected"><?php echo $text_enabled; ?></option>
										<option value="0"><?php echo $text_disabled; ?></option>
									<?php } else { ?>
										<option value="1"><?php echo $text_enabled; ?></option>
										<option value="0" selected="selected"><?php echo $text_disabled; ?></option>
									<?php } ?>
								</select>
							</div>
						</div>

						<div class="form-group">
							<label class="col-sm-2 control-label" for="input-override_subtotal"><?php echo $entry_override_subtotal; ?></label>
							<div class="col-sm-10">
								<select name="kco_override_subtotal" id="input-override_subtotal" class="form-control">
									<?php if ($kco_override_subtotal) { ?>
										<option value="1" selected="selected"><?php echo $text_enabled; ?></option>
										<option value="0"><?php echo $text_disabled; ?></option>
									<?php } else { ?>
										<option value="1"><?php echo $text_enabled; ?></option>
										<option value="0" selected="selected"><?php echo $text_disabled; ?></option>
									<?php } ?>
								</select>
							</div>
						</div>

						<div class="form-group">
							<label class="col-sm-2 control-label" for="input-override_tax"><?php echo $entry_override_tax; ?></label>
							<div class="col-sm-10">
								<select name="kco_override_tax" id="input-override_tax" class="form-control">
									<?php if ($kco_override_tax) { ?>
										<option value="1" selected="selected"><?php echo $text_enabled; ?></option>
										<option value="0"><?php echo $text_disabled; ?></option>
									<?php } else { ?>
										<option value="1"><?php echo $text_enabled; ?></option>
										<option value="0" selected="selected"><?php echo $text_disabled; ?></option>
									<?php } ?>
								</select>
							</div>
						</div>

						<div class="form-group">
							<label class="col-sm-2 control-label" for="input-override_total"><?php echo $entry_override_total; ?></label>
							<div class="col-sm-10">
								<select name="kco_override_total" id="input-override_total" class="form-control">
									<?php if ($kco_override_total) { ?>
										<option value="1" selected="selected"><?php echo $text_enabled; ?></option>
										<option value="0"><?php echo $text_disabled; ?></option>
									<?php } else { ?>
										<option value="1"><?php echo $text_enabled; ?></option>
										<option value="0" selected="selected"><?php echo $text_disabled; ?></option>
									<?php } ?>
								</select>
							</div>
						</div>

					</div>

					<!-- AU -->
					<div class="tab-pane" id="tab-au">
						<div class="form-group">
							<label class="col-sm-2 control-label" for="input-au_eid"><?php echo $entry_eid; ?></label>
							<div class="col-sm-10">
								<input type="text" name="kco_au_eid" id="input-au_eid" class="form-control" value="<?php echo $kco_au_eid; ?>">
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-2 control-label" for="input-au_secret"><?php echo $entry_secret; ?></label>
							<div class="col-sm-10">
								<input type="text" name="kco_au_secret" id="input-au_secret" class="form-control" value="<?php echo $kco_au_secret; ?>">
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-2 control-label" for="input-au_country_id"><?php echo $entry_country; ?></label>
							<div class="col-sm-10">
								<select name="kco_au_country_id" id="input-au_country_id" class="form-control">
									<?php foreach ($countries as $country) { ?>
										<?php if ($country['country_id']==$kco_au_country_id) { ?>
											<option value="<?php echo $country['country_id']; ?>" selected="selected"><?php echo $country['name']; ?></option>
										<?php } else { ?>
											<option value="<?php echo $country['country_id']; ?>"><?php echo $country['name']; ?></option>
										<?php } ?>
									<?php } ?>
								</select>
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-2 control-label" for="input-au_status"><?php echo $entry_status; ?></label>
							<div class="col-sm-10">
								<select name="kco_au_status" id="input-au_status" class="form-control">
									<?php if ($kco_au_status) { ?>
										<option value="1" selected="selected"><?php echo $text_enabled; ?></option>
										<option value="0"><?php echo $text_disabled; ?></option>
									<?php } else { ?>
										<option value="1"><?php echo $text_enabled; ?></option>
										<option value="0" selected="selected"><?php echo $text_disabled; ?></option>
									<?php } ?>
								</select>
							</div>
						</div>
					</div>

					<!-- DK -->
					<div class="tab-pane" id="tab-dk">
						<div class="form-group">
							<label class="col-sm-2 control-label" for="input-dk_eid"><?php echo $entry_eid; ?></label>
							<div class="col-sm-10">
								<input type="text" name="kco_dk_eid" id="input-dk_eid" class="form-control" value="<?php echo $kco_dk_eid; ?>">
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-2 control-label" for="input-dk_secret"><?php echo $entry_secret; ?></label>
							<div class="col-sm-10">
								<input type="text" name="kco_dk_secret" id="input-dk_secret" class="form-control" value="<?php echo $kco_dk_secret; ?>">
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-2 control-label" for="input-dk_country_id"><?php echo $entry_country; ?></label>
							<div class="col-sm-10">
								<select name="kco_dk_country_id" id="input-dk_country_id" class="form-control">
									<?php foreach ($countries as $country) { ?>
										<?php if ($country['country_id']==$kco_dk_country_id) { ?>
											<option value="<?php echo $country['country_id']; ?>" selected="selected"><?php echo $country['name']; ?></option>
										<?php } else { ?>
											<option value="<?php echo $country['country_id']; ?>"><?php echo $country['name']; ?></option>
										<?php } ?>
									<?php } ?>
								</select>
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-2 control-label" for="input-dk_status"><?php echo $entry_status; ?></label>
							<div class="col-sm-10">
								<select name="kco_dk_status" id="input-dk_status" class="form-control">
									<?php if ($kco_dk_status) { ?>
										<option value="1" selected="selected"><?php echo $text_enabled; ?></option>
										<option value="0"><?php echo $text_disabled; ?></option>
									<?php } else { ?>
										<option value="1"><?php echo $text_enabled; ?></option>
										<option value="0" selected="selected"><?php echo $text_disabled; ?></option>
									<?php } ?>
								</select>
							</div>
						</div>
					</div>

					<!-- DE -->
					<div class="tab-pane" id="tab-de">
						<div class="form-group">
							<label class="col-sm-2 control-label" for="input-de_eid"><?php echo $entry_eid; ?></label>
							<div class="col-sm-10">
								<input type="text" name="kco_de_eid" id="input-de_eid" class="form-control" value="<?php echo $kco_de_eid; ?>">
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-2 control-label" for="input-de_secret"><?php echo $entry_secret; ?></label>
							<div class="col-sm-10">
								<input type="text" name="kco_de_secret" id="input-de_secret" class="form-control" value="<?php echo $kco_de_secret; ?>">
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-2 control-label" for="input-de-country_id"><?php echo $entry_country; ?></label>
							<div class="col-sm-10">
								<select name="kco_de_country_id" id="input-de_country_id" class="form-control">
									<?php foreach ($countries as $country) { ?>
										<?php if ($country['country_id']==$kco_de_country_id) { ?>
											<option value="<?php echo $country['country_id']; ?>" selected="selected"><?php echo $country['name']; ?></option>
										<?php } else { ?>
											<option value="<?php echo $country['country_id']; ?>"><?php echo $country['name']; ?></option>
										<?php } ?>
									<?php } ?>
								</select>
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-2 control-label" for="input-de_status"><?php echo $entry_status; ?></label>
							<div class="col-sm-10">
								<select name="kco_de_status" id="input-de_status" class="form-control">
									<?php if ($kco_de_status) { ?>
										<option value="1" selected="selected"><?php echo $text_enabled; ?></option>
										<option value="0"><?php echo $text_disabled; ?></option>
									<?php } else { ?>
										<option value="1"><?php echo $text_enabled; ?></option>
										<option value="0" selected="selected"><?php echo $text_disabled; ?></option>
									<?php } ?>
								</select>
							</div>
						</div>
					</div>

					<!-- FI -->
					<div class="tab-pane" id="tab-fi">
						<div class="form-group">
							<label class="col-sm-2 control-label" for="input-fi_eid"><?php echo $entry_eid; ?></label>
							<div class="col-sm-10">
								<input type="text" name="kco_fi_eid" id="input-fi_eid" class="form-control" value="<?php echo $kco_fi_eid; ?>">
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-2 control-label" for="input-fi_secret"><?php echo $entry_secret; ?></label>
							<div class="col-sm-10">
								<input type="text" name="kco_fi_secret" id="input-fi_secret" class="form-control" value="<?php echo $kco_fi_secret; ?>">
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-2 control-label" for="input-fi-country_id"><?php echo $entry_country; ?></label>
							<div class="col-sm-10">
								<select name="kco_fi_country_id" id="input-fi_country_id" class="form-control">
									<?php foreach ($countries as $country) { ?>
										<?php if ($country['country_id']==$kco_fi_country_id) { ?>
											<option value="<?php echo $country['country_id']; ?>" selected="selected"><?php echo $country['name']; ?></option>
										<?php } else { ?>
											<option value="<?php echo $country['country_id']; ?>"><?php echo $country['name']; ?></option>
										<?php } ?>
									<?php } ?>
								</select>
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-2 control-label" for="input-fi_status"><?php echo $entry_status; ?></label>
							<div class="col-sm-10">
								<select name="kco_fi_status" id="input-fi_status" class="form-control">
									<?php if ($kco_fi_status) { ?>
										<option value="1" selected="selected"><?php echo $text_enabled; ?></option>
										<option value="0"><?php echo $text_disabled; ?></option>
									<?php } else { ?>
										<option value="1"><?php echo $text_enabled; ?></option>
										<option value="0" selected="selected"><?php echo $text_disabled; ?></option>
									<?php } ?>
								</select>
							</div>
						</div>
					</div>

					<!-- NO -->
					<div class="tab-pane" id="tab-no">
						<div class="form-group">
							<label class="col-sm-2 control-label" for="input-no_eid"><?php echo $entry_eid; ?></label>
							<div class="col-sm-10">
								<input type="text" name="kco_no_eid" id="input-no_eid" class="form-control" value="<?php echo $kco_no_eid; ?>">
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-2 control-label" for="input-no_secret"><?php echo $entry_secret; ?></label>
							<div class="col-sm-10">
								<input type="text" name="kco_no_secret" id="input-no_secret" class="form-control" value="<?php echo $kco_no_secret; ?>">
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-2 control-label" for="input-no-country_id"><?php echo $entry_country; ?></label>
							<div class="col-sm-10">
								<select name="kco_no_country_id" id="input-no_country_id" class="form-control">
									<?php foreach ($countries as $country) { ?>
										<?php if ($country['country_id']==$kco_no_country_id) { ?>
											<option value="<?php echo $country['country_id']; ?>" selected="selected"><?php echo $country['name']; ?></option>
										<?php } else { ?>
											<option value="<?php echo $country['country_id']; ?>"><?php echo $country['name']; ?></option>
										<?php } ?>
									<?php } ?>
								</select>
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-2 control-label" for="input-no_status"><?php echo $entry_status; ?></label>
							<div class="col-sm-10">
								<select name="kco_no_status" id="input-no_status" class="form-control">
									<?php if ($kco_no_status) { ?>
										<option value="1" selected="selected"><?php echo $text_enabled; ?></option>
										<option value="0"><?php echo $text_disabled; ?></option>
									<?php } else { ?>
										<option value="1"><?php echo $text_enabled; ?></option>
										<option value="0" selected="selected"><?php echo $text_disabled; ?></option>
									<?php } ?>
								</select>
							</div>
						</div>
					</div>

					<!-- SE -->
					<div class="tab-pane" id="tab-se">
						<div class="form-group">
							<label class="col-sm-2 control-label" for="input-se_eid"><?php echo $entry_eid; ?></label>
							<div class="col-sm-10">
								<input type="text" name="kco_se_eid" id="input-se_eid" class="form-control" value="<?php echo $kco_se_eid; ?>">
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-2 control-label" for="input-se_secret"><?php echo $entry_secret; ?></label>
							<div class="col-sm-10">
								<input type="text" name="kco_se_secret" id="input-se_secret" class="form-control" value="<?php echo $kco_se_secret; ?>">
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-2 control-label" for="input-se-country_id"><?php echo $entry_country; ?></label>
							<div class="col-sm-10">
								<select name="kco_se_country_id" id="input-se_country_id" class="form-control">
									<?php foreach ($countries as $country) { ?>
										<?php if ($country['country_id']==$kco_se_country_id) { ?>
											<option value="<?php echo $country['country_id']; ?>" selected="selected"><?php echo $country['name']; ?></option>
										<?php } else { ?>
											<option value="<?php echo $country['country_id']; ?>"><?php echo $country['name']; ?></option>
										<?php } ?>
									<?php } ?>
								</select>
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-2 control-label" for="input-se_status"><?php echo $entry_status; ?></label>
							<div class="col-sm-10">
								<select name="kco_se_status" id="input-se_status" class="form-control">
									<?php if ($kco_se_status) { ?>
										<option value="1" selected="selected"><?php echo $text_enabled; ?></option>
										<option value="0"><?php echo $text_disabled; ?></option>
									<?php } else { ?>
										<option value="1"><?php echo $text_enabled; ?></option>
										<option value="0" selected="selected"><?php echo $text_disabled; ?></option>
									<?php } ?>
								</select>
							</div>
						</div>
					</div>

				</div>

			</form>
		</div>

	</div>

</div>

<?php echo $footer; ?>
