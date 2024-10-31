jQuery(document).ready(function(){
	let selected_page = jQuery("#selected_widget_page").val();
	if(selected_page.length != 0) {
		selected_page_arr = selected_page.split(",");
		jQuery("#pvcw_widget_page").val(selected_page_arr);
		jQuery("#pvcw_widget_page").trigger("chosen:updated");
	}
});

jQuery(document).on('submit','#paidvideocall-widget-key-form', function(e){
	e.preventDefault();
	var paidvideocall_api_url = jQuery("#api_url").val().trim();
	var paidvideocall_access_key = jQuery("#access_key").val().trim();
	var widget_key = jQuery("#pvcw_widget_api_key").val().trim();
	jQuery('.form-bottom #submit').prop('disabled', false);
	jQuery('#pvcw_widget_api_key').prop('disabled', false);
	jQuery('.pvcw_loader').hide();
	jQuery('#disable_widget_btn').hide();
	jQuery('#pvcw_widget_key_response').removeClass('success-msg');
	jQuery('#pvcw_widget_key_response').removeClass('error-msg');
	if(widget_key == "") {
		jQuery("#pvcw_widget_api_key").focus();
		show_response('Please enter widget key.', 'pvcw_widget_key_response', 'error-msg');
	}
	else {
		var jsonData = '{"accessKey":"'+paidvideocall_access_key+'", "widget_key": "'+widget_key+'"}';
		jQuery('.form-bottom #submit').prop('disabled', true);
		jQuery('#pvcw_widget_api_key').prop('disabled', true);
		jQuery('.pvcw_loader').show();
		jQuery.ajax({
			url: paidvideocall_api_url+'get_widget_url',
			type: "POST",
			dataType: "json",
			data: jsonData,
			contentType: "application/json",
			success: function(response) {
				if (response.cmd != "get_widget_url_response") {
					jQuery("#pvcw_widget_api_key").focus();
					jQuery('.form-bottom #submit').prop('disabled', false);
					jQuery('#pvcw_widget_api_key').prop('disabled', false);
					jQuery('.pvcw_loader').hide();
					jQuery('#disable_widget_btn').hide();
					jQuery('#widget_page_section').hide();
					show_response('Please enter valid widget key.', 'pvcw_widget_key_response', 'error-msg');
				}
				else {
					if(response.payload.widget_url != "") {
						var data = {
							'action': 'pvcw_widget_url',
							'pvcw_widget_key': widget_key, 
							'pvcw_widget_url': response.payload.widget_url, 
						};		
						jQuery.post(ajax_var.url, data, function (response) {
							if(response == 'saved') {
								jQuery("#pvcw_widget_api_key").val(widget_key);
								jQuery('.pvcw_loader').hide();
								jQuery('.form-bottom #submit').prop('disabled', true);
								jQuery('#pvcw_widget_api_key').prop('disabled', true);
								jQuery('#disable_widget_btn').show();
								jQuery('#widget_page_section').show();
								show_response('Congratulation!! Your widget is implemented successfully.', 'pvcw_widget_key_response', 'success-msg');
							}
							else {
								jQuery("#pvcw_widget_api_key").focus();
								jQuery('.form-bottom #submit').prop('disabled', false);
								jQuery('#pvcw_widget_api_key').prop('disabled', false);
								jQuery('.pvcw_loader').hide();
								jQuery('#disable_widget_btn').hide();
								jQuery('#widget_page_section').hide();
								show_response('Something went wrong. Please try again.', 'pvcw_widget_key_response', 'error-msg');
							}
						});
					}
					else {
						jQuery("#pvcw_widget_api_key").focus();
						jQuery('.form-bottom #submit').prop('disabled', false);
						jQuery('#pvcw_widget_api_key').prop('disabled', false);
						jQuery('.pvcw_loader').hide();
						jQuery('#disable_widget_btn').hide();
						jQuery('#widget_page_section').hide();
						show_response('Something went wrong. Please try again.', 'pvcw_widget_key_response', 'error-msg');
					}
				}
			}
		});
	}
});

jQuery(document).on("click", "#disable_widget_btn", function(){
	var widget_key = jQuery("#pvcw_widget_api_key").val().trim();
	jQuery('#pvcw_widget_key_response').removeClass('success-msg');
	jQuery('#pvcw_widget_key_response').removeClass('error-msg');
	var data = {
		'action': 'pvcw_disable_widget_url',
		'pvcw_widget_key': widget_key,
	};	
	jQuery.post(ajax_var.url, data, function (response) {
		if(response == 'disabled') {
			jQuery('.pvcw_loader').hide();
			jQuery('.form-bottom #submit').prop('disabled', false);
			jQuery('#pvcw_widget_api_key').prop('disabled', false);
			jQuery('#pvcw_widget_api_key').val('');
			jQuery('#disable_widget_btn').hide();
			jQuery('#widget_page_section').hide();
			show_response('Your widget is disabled successfully!', 'pvcw_widget_key_response', 'success-msg');
		}
		else {
			jQuery('.form-bottom #submit').prop('disabled', true);
			jQuery('#pvcw_widget_api_key').prop('disabled', true);
			jQuery('.pvcw_loader').hide();
			jQuery('#disable_widget_btn').show();
			show_response('Something went wrong. Please try again.', 'pvcw_widget_key_response', 'error-msg');
		}
	});
});

jQuery(document).on("click", "#pvcw_widget_page_save", function(){
	var pvcw_widget_page;
	var field_valid;
	if(jQuery("#show_all_pages").prop("checked")) {
		pvcw_widget_page = 'all';
		field_valid = 'yes';
	}
	else {
		pvcw_widget_page = jQuery("#pvcw_widget_page").val();
		if(pvcw_widget_page.length == 0) {
			field_valid = 'no';
			show_response('Please choose at least one page to show widget.', 'pvcw_widget_page_response', 'error-msg');
		}
		else {
			field_valid = 'yes';
		}
	}

	if(field_valid == 'yes') {
		jQuery('#pvcw_widget_page_save').prop('disabled', true);
		jQuery('#pvcw_widget_page_response').removeClass('success-msg');
		jQuery('#pvcw_widget_page_response').removeClass('error-msg');
		var data = {
			'action': 'pvcw_widget_page',
			'pvcw_widget_page_id': pvcw_widget_page,
		};	
		jQuery.post(ajax_var.url, data, function (response) {
			if(response == 'saved') {
				jQuery('#pvcw_widget_page_save').prop('disabled', false);
				show_response('Saved successfully!', 'pvcw_widget_page_response', 'success-msg');
			}
			else {
				jQuery('#pvcw_widget_page_save').prop('disabled', false);
				show_response('Something went wrong. Please try again.', 'pvcw_widget_page_response', 'error-msg');
			}
		});
	}
});

function show_response(message, id, response_type) {
	jQuery('#'+id).html(message);
	jQuery('#'+id).addClass(response_type);
	setTimeout(function(){
		jQuery('#'+id).removeClass(response_type);
		jQuery('#'+id).html('');
	}, 3000);
}

jQuery(".pvcw-chosen-select").chosen({no_results_text: "Oops, nothing found!", width:"350px"}); 

jQuery(document).on("change","#show_all_pages",function() {
    if(jQuery(this).is(':checked')) {
		jQuery(".pvcw_pages_row").removeClass("show_row");
		jQuery("#pvcw_widget_page").val('');
		jQuery('#pvcw_widget_page').trigger("chosen:updated");
	}
	else {
		jQuery(".pvcw_pages_row").addClass("show_row");
	}
}); 