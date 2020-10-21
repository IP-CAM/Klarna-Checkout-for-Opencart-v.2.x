<!DOCTYPE html>

<html dir="<?php echo $direction; ?>" lang="<?php echo $lang; ?>">
	<head>

		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1">

		<base href="<?php echo $base; ?>" />

		<meta name="description" content="<?php echo $description; ?>" />
		<meta name="keywords" content= "<?php echo $keywords; ?>" />
		<meta name="robots" content="NOFOLLOW,NOINDEX">

		<title><?php echo $title; ?></title>

		<link rel="canonical" href="<?php echo $canonical; ?>" />

		<?php if ($icon) { ?><link href="<?php echo $icon; ?>" rel="icon" /><?php } ?>

		<link href="catalog/view/javascript/klarna/css/bootstrap.min.css" rel="stylesheet">
		<link href="catalog/view/javascript/klarna/css/kco.css" rel="stylesheet">
		<link href="catalog/view/javascript/klarna/css/custom.css" rel="stylesheet">

		<script src="catalog/view/javascript/klarna/js/jquery.min.js"></script>
		<script src="catalog/view/javascript/klarna/js/bootstrap.min.js"></script>
		<script src="catalog/view/javascript/klarna/js/klarna.js"></script>

		<!--[if lt IE 9]>
			<script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
			<script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
		<![endif]-->

		<?php foreach ($analytics as $analytic) { ?>
			<?php echo $analytic; ?>
		<?php } ?>

	</head>

	<body>

		<div class="container">

			<section id="kco-wrapper">

				<div id="kco-header">
					<div class="row">
						<div class="col-xs-6 margin-bottom text-left">
							<a href="<?php echo $home; ?>"><i class="glyphicon glyphicon-arrow-left"></i> <?php echo $text_back; ?></a>
						</div>
						<div class="col-xs-6 margin-bottom text-right">
							<?php if ($logged) { ?>
								<?php echo $text_logout; ?>
							<?php } elseif (!$logged AND $status_account) { ?>
								<a href="#" id="kco-login-toggle"><?php echo $text_login; ?> <i class="glyphicon glyphicon-user"></i></a>
							<?php } ?>
						</div>
					</div>

					<div class="row">
						<div class="col-xs-12 col-sm-6 margin-bottom text-left">

							<?php if ($logo) { ?>
								<img src="<?php echo $logo; ?>" title="<?php echo $name; ?>" alt="<?php echo $name; ?>" class="img-responsive" />
							<?php } else { ?>
								<h1><?php echo $name; ?></h1>
							<?php } ?>

						</div>
						<div class="col-sm-6 hidden-xs margin-bottom text-right">
							<img src="https://cdn.klarna.com/1.0/shared/image/generic/logo/sv_se/basic/white.png?width=100" />
						</div>
					</div>
				</div>

				<div id="kco-body">

					<?php if ($status_account) { ?>
						<section id="kco-login-form">
							<div class="col-xs-12 col-sm-5">
								<div class="addon">
									<i class="glyphicon glyphicon-user"></i>
									<input type="text" name="email" value="" class="form-control" placeholder="<?php echo $entry_email; ?>" />
								</div>
							</div>
							<div class="col-xs-12 col-sm-5">
								<div class="addon">
									<i class="glyphicon glyphicon-lock"></i>
									<input type="password" name="password" value="" class="form-control" placeholder="<?php echo $entry_password; ?>" />
								</div>
							</div>
							<div class="col-xs-12 col-sm-2">
								<a href="#" id="kco-login-submit" class="btn btn-success btn-block"><i class="glyphicon glyphicon-log-in"></i></a>
							</div>
							<div class="col-xs-12" id="kco-login-alert">
							</div>
						</section>
					<?php } ?>

					<?php if ($status_checkout) { ?>
						<section id="kco-allow-checkout">
							<div class="row">
								<div class="col-xs-12">
									<p><i class="glyphicon glyphicon-briefcase"></i> <?php echo $text_normal_checkout; ?></p>
								</div>
							</div>
						</section>
					<?php } ?>

					<div class="row">
						<div class="col-sm-7">
							<section id="kco-left">

								<h2>
									<?php echo $heading_order; ?>
									<span class="pull-right">
										<?php foreach ($locales as $locale) { ?>
											<a href="#" class="kco-locale-button" id="kco-locale-<?php echo $locale['value']; ?>" data-locale="<?php echo $locale['value']; ?>"><img src="catalog/view/javascript/klarna/img/<?php echo $locale['icon']; ?>" alt="<?php echo $locale['title']; ?>" title="<?php echo $locale['title']; ?>" /></a>
										<?php } ?>
									</span>
								</h2>
								<section id="kco-form">

									<input type="hidden" name="valid" id="kco-valid" value="<?php echo $valid; ?>"  />

									<div class="form-group">
										<div class="addon">
											<i class="glyphicon glyphicon-envelope"></i>
											<input type="text" name="email" id="kco-email" value="<?php echo $kco_email; ?>" class="form-control" placeholder="<?php echo $entry_email; ?>" />
										</div>
									</div>
									<div class="form-group">
										<div class="addon">
											<i class="glyphicon glyphicon-map-marker"></i>
											<input type="text" name="postcode" id="kco-postcode" value="<?php echo $kco_postcode; ?>" class="form-control" placeholder="<?php echo $entry_postcode; ?>" />
										</div>
									</div>
								</section>

								<div id="kco-details">

									<h3><?php echo $heading_shipping; ?> <i class="glyphicon glyphicon-question-sign" data-toggle="tooltip" data-placement="right" title="<?php echo $tooltip_shipping; ?>"></i></h3>
									<select name="shipping" id="kco-shipping" class="form-control">
									</select>

									<h3><?php echo $heading_misc; ?></h3>
									<ul class="list-group">
										<?php if (!$logged AND $status_account) { ?>
											<li class="list-group-item">
												<a data-toggle="collapse" href="#kco-account" aria-expanded="false" aria-controls="kco-account"><?php echo $text_account; ?></a>
												<div class="collapse" id="kco-account">
													<p class="small"><?php echo $text_create_account; ?></p>
													<div class="input-group">
														<input type="text" class="form-control" name="account" id="account" placeholder="<?php echo $entry_new_password; ?>" />
														<span class="input-group-btn">
															<button class="btn btn-success" type="button" id="account-button"><i class="glyphicon glyphicon-ok"></i></button>
														</span>
													</div>
													<div id="kco-account-alert" class="alert"></div>
												</div>
											</li>
										<?php } ?>
										<?php if ($status_coupon) { ?>
											<li class="list-group-item">
												<a data-toggle="collapse" href="#kco-coupon" aria-expanded="false" aria-controls="kco-coupon"><?php echo $text_coupon; ?></a>
												<div class="collapse" id="kco-coupon">
													<div class="input-group">
														<input type="text" class="form-control" name="coupon" id="coupon" />
														<span class="input-group-btn">
															<button class="btn btn-primary" type="button" id="coupon-button"><i class="glyphicon glyphicon-plus"></i></button>
														</span>
													</div>
													<div id="kco-coupon-alert" class="alert"></div>
												</div>
											</li>
										<?php } ?>
										<?php if ($status_voucher) { ?>
											<li class="list-group-item">
												<a data-toggle="collapse" href="#kco-voucher" aria-expanded="false" aria-controls="kco-voucher"><?php echo $text_voucher; ?></a>
												<div class="collapse" id="kco-voucher">
													<div class="input-group">
														<input type="text" class="form-control" name="voucher" id="voucher" />
														<span class="input-group-btn">
															<button class="btn btn-primary" type="button" id="voucher-button"><i class="glyphicon glyphicon-plus"></i></button>
														</span>
													</div>
													<div id="kco-voucher-alert" class="alert"></div>
												</div>
											</li>
										<?php } ?>
										<?php if ($status_comment) { ?>
											<li class="list-group-item">
												<a data-toggle="collapse" href="#kco-comment" aria-expanded="false" aria-controls="kco-comment"><?php echo $text_comment; ?></a>
												<div class="collapse" id="kco-comment">
													<textarea class="form-control" name="comment" id="comment"><?php echo $comment; ?></textarea>
												</div>
											</li>
										<?php } ?>
									</ul>

								</div>

								<h3><?php echo $heading_cart; ?></h3>
								<div id="kco-cart">
								</div>
							</section>
						</div>
						<div class="col-sm-5">
							<section id="kco-right">
								<h2><?php echo $heading_payment; ?></h2>
								<div id="kco-snippet-section">
									<p><?php echo $text_information; ?></p>
									<div class="text-center">
										<img src="https://cdn.klarna.com/1.0/shared/image/generic/badge/sv_se/checkout/short-blue.png?width=300" class="img-responsive" />
									</div>
								</div>
							</section>
						</div>
					</div>
				</div>
				<div id="kco-footer">
					<div class="row">
						<div class="col-sm-6 hidden-xs text-left">
							<i class="glyphicon glyphicon-lock text-success"></i> <?php echo $text_safe_shopping; ?>
						</div>
						<div class="col-sm-6 hidden-xs text-right">
							<?php echo $version; ?>
						</div>
					</div>
				</div>
			</section>


		</div>

	</body>

</html>
