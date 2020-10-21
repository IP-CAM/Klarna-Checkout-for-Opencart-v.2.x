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
							<a href="<?php echo $home; ?>" title="<?php echo $text_continue; ?>"><i class="glyphicon glyphicon-arrow-left"></i> <?php echo $text_continue; ?></a>
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

					<div class="row">
						<div class="col-sm-5 col-md-4">
							<section id="kco-left">
								<h2><?php echo $heading_thank_you; ?></h2>
								<p><?php echo $text_thank_you; ?></p>
								<p><?php echo $text_pay_now; ?></p>
								<a href="<?php echo $home; ?>" class="btn btn-success btn-block" title="<?php echo $text_continue; ?>"><?php echo $text_continue; ?></a>
							</section>
						</div>
						<div class="col-sm-7 col-md-8">
							<section id="kco-right">
								<h2><?php echo $heading_receipt; ?></h2>
								<div id="kco-snippet-section">
									<?php echo $snippet; ?>
								</div>
							</section>
						</div>
					</div>
				</div>

			</section>


		</div>

	</body>

</html>
