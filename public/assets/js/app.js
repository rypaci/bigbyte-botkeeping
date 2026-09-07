;(function(global, $){
	
})(window, $);

function get_alert_icons(type){
	var icons = {
		error:   'ban',
		info:    'info',
		warning: 'warning',
		success: 'check',
	};
	
	return icons[ type ];
}

function create_alert(type, selector, message){
	var html = '<div class="alert alert-'+ type +' alert-dismissible"> \
		<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button> \
		<h4><i class="icon fa fa-'+ get_alert_icons(type) +'"></i> Alert!</h4> \
		'+ message +' \
	</div>';
	
	if(typeof selector == 'string' && selector.length > 0)
		$(selector).html(html);
	else
		return html;
}

function create_alert2(type, selector, data){
	var html = '<div class="alert alert-'+ type +' alert-dismissible"> \
		<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button> \
		<h4><i class="icon fa fa-'+ get_alert_icons(type) +'"></i> Alert!</h4> \
		<ul>';
	
	for(var i in data) {
		html += '<li>'+ data[i] +'</li>';
	}
	
	html += '</ul> \
	</div>';
	
	if(typeof selector == 'string' && selector.length > 0)
		$(selector).html(html);
	else
		return html;
}

String.prototype.replaceAt = function(index, replacement){
	return this.substr(0, index) + replacement + this.substr(index + replacement.length);
}

function isNumeric(e) {
	var charCode = (e.which) ? e.which : e.keyCode;
	
	if(charCode == 46 || (48 <= charCode && charCode <= 57)){
		return true;
	}
	
	return false;
}

$('body').on('keypress', 'input.numbers-only', function(e){
	return isNumeric(e);
});
	
