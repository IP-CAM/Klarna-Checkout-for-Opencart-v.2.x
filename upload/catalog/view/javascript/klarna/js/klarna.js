$(document).ready(function(){

	$('[data-toggle="tooltip"]').tooltip()

	$('#kco-login-toggle').on('click', function(event) {

		event.preventDefault();

		$('#kco-alert').fadeOut();
		$('#kco-login-form').toggle('slide');

	});

	$('#kco-login-submit').on('click', function(event) {

		event.preventDefault();

		$.ajax({
			type: 'POST',
			url: 'index.php?route=klarna/auth',
			data: $('#kco-login-form input'),
			dataType: 'json',
			success : function(json) {

				$('#kco-login-alert .alert').hide();

				if (json['error']) {
					$('#kco-login-alert').html('<div class="alert alert-danger" role="alert">' + json['error'] + '</div>');
					$('#kco-login-alert .alert').fadeIn();
					$('#kco-login-alert .alert').delay(8000).fadeOut();
				}
				else {
					location.reload();
				}

			}
		});

	});

	$('.kco-locale-button').on('click', function(event) {

		event.preventDefault();

		var data_locale = $(this).attr('data-locale');

		if (!data_locale) { return false; }

		$.ajax({
			type: 'POST',
			url: 'index.php?route=klarna/checkout/locale',
			data: { locale: data_locale},
			dataType: 'json',
			success : function(json) {
				location.reload();
			}
		});

	});

	$('#kco-postcode').on('keyup', function(event) {

		var postcode 	= $('#kco-postcode').val();
		var valid 		= $('#kco-valid').val();

		postcode 		= postcode.replace(/\s+/g, '');

		if (postcode.length==valid) {
			$('#kco-postcode').blur();
			$('#kco-postcode').trigger('change');
		}

	});

	$('#kco-form input').on('change', function(event) {

		var email 	 	= $('#kco-email').val();
		var postcode 	= $('#kco-postcode').val();
		postcode 		= postcode.replace(/\s+/g, '');
		var valid 		= $('#kco-valid').val();

		if (email.length>6 && postcode.length==valid) {

			$.ajax({
				type: 'POST',
				url: 'index.php?route=klarna/shipping',
				data: $('#kco-form input'),
				dataType: 'json',
				success : function(json) {

					html = '';

					for (i = 0; i < json['methods'].length; i++) {
						html += '<option value="' + json['methods'][i]['id'] + '"';
						if (json['methods'][i]['selected']==1) {
							html += ' selected="selected"'
						}
						html += '>' + json['methods'][i]['name'] + '</option>';
					}

					$('#kco-shipping').html(html);
					$('#kco-details').slideDown();
					$('#kco-shipping').trigger('change');

				}
			});

		}

	});

	$('#kco-shipping').on('change', function(event) {

		event.preventDefault();

		var status = false;
		var email 	 = $('#kco-email').val();
		var postcode = $('#kco-postcode').val();
		postcode 		= postcode.replace(/\s+/g, '');
		var valid 		= $('#kco-valid').val();

		if (email.length>6 && postcode.length==valid) { status = true; }

		$.ajax({
			type: 'POST',
			url: 'index.php?route=klarna/shipping/save',
			data: $('#kco-shipping'),
			dataType: 'json',
			success : function(json) {

				$('#kco-cart').load('index.php?route=klarna/cart');

				var email 	 = $('#kco-email').val();
				var postcode = $('#kco-postcode').val();

				if (status) {

					$.ajax({
						type: 'POST',
						url: 'index.php?route=klarna/payment',
						data: $('#kco-form input'),
						dataType: 'html',
						success : function(data) {
							$("#kco-snippet-section").html(data);
						}
					});

				} else {

					$("#kco-snippet-section").html('');

				}

			}
		});

	});

	$('#comment').on('blur', function(event) {
		$.ajax({
			type: 'POST',
			url: 'index.php?route=klarna/comment',
			data: $('#comment'),
			dataType: 'json',
			success : function(json) {
				console.log('COMMENT - OK');
			}
		});
	});

	$(document).on('click', '#account-button' ,function() {
		$.ajax({
			url: 'index.php?route=klarna/account',
			type: 'post',
			data: $('input[name="account"], input[name="email"], input[name="postcode"]'),
			dataType: 'json',
			beforeSend: function() {
				$('.alert').remove();
			},
			success: function(json) {
				if (json['error']) {
					$('#kco-account .input-group').after('<div class="alert alert-danger" role="alert">' + json['error'] + '</div>');
					$('#kco-account .alert').fadeIn();
					$('#kco-account .alert').delay(8000).slideUp();
				}
				else {
					location.reload();
				}
			}
		});
	});

	$(document).on('click', '#kco-coupon-add' ,function() {
		$.ajax({
			url: 'index.php?route=klarna/coupon/add',
			type: 'post',
			data: $('input[name="coupon"]'),
			dataType: 'json',
			beforeSend: function() {
				$('.alert').remove();
			},
			success: function(json) {
				if (json['error']) {
					$('#kco-coupon .input-group').after('<div class="alert alert-danger" role="alert">' + json['error'] + '</div>');
					$('#kco-coupon .alert').fadeIn();
					$('#kco-coupon .alert').delay(8000).slideUp();
				}
				else {
					$('#kco-coupon').load('index.php?route=klarna/coupon');
					$('#kco-shipping').trigger('change');
				}
			}
		});
	});

	$(document).on('click', '#kco-coupon-remove' ,function() {
		$.ajax({
			url: 'index.php?route=klarna/coupon/remove',
			type: 'post',
			dataType: 'json',
			beforeSend: function() {
				$('.alert').remove();
			},
			success: function(json) {
				$('#kco-coupon').load('index.php?route=klarna/coupon');
				$('#kco-shipping').trigger('change');
			}
		});
	});

	$(document).on('click', '#kco-voucher-add' ,function() {
		$.ajax({
			url: 'index.php?route=klarna/voucher/add',
			type: 'post',
			data: $('input[name="voucher"]'),
			dataType: 'json',
			beforeSend: function() {
				$('.alert').remove();
			},
			success: function(json) {
				if (json['error']) {
					$('#kco-voucher .input-group').after('<div class="alert alert-danger" role="alert">' + json['error'] + '</div>');
					$('#kco-voucher .alert').fadeIn();
					$('#kco-voucher .alert').delay(8000).slideUp();
				}
				else {
					$('#kco-voucher').load('index.php?route=klarna/voucher');
					$('#kco-shipping').trigger('change');
				}
			}
		});
	});

	$(document).on('click', '#kco-voucher-remove' ,function() {
		$.ajax({
			url: 'index.php?route=klarna/voucher/remove',
			type: 'post',
			dataType: 'json',
			beforeSend: function() {
				$('.alert').remove();
			},
			success: function(json) {
				$('#kco-voucher').load('index.php?route=klarna/voucher');
				$('#kco-shipping').trigger('change');
			}
		});
	});

	$('#kco-postcode').trigger('change');
	$('#kco-cart').load('index.php?route=klarna/cart');
	$('#kco-coupon').load('index.php?route=klarna/coupon');
	$('#kco-voucher').load('index.php?route=klarna/voucher');

});
