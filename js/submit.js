$(document).ready(function() {
    var form = $('form#contact');
    $(form).submit(function(event) {
        event.preventDefault();

	formData = {
		'name': $('input[name=name]').val(),
		'email': $('input[name=email]').val(),
		'message': $('textarea[name=message]').val(),
		'captcha': $('textarea[name=g-recaptcha-response]').val()
	}

	if (formData.name!='' && formData.email!='' && formData.message!='' && formData.captcha!='') {
		$.ajax({
	    		type: 'POST',
    			url: $(form).attr('action'),
			data: formData
		});
	}


    });
});

