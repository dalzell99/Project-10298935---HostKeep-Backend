var ROOT_FOLDER = "https://stephencolman.com.au/owners/";

// Set toastr notification options
$(function () {
	toastr.options = {
		"closeButton": true,
		"debug": false,
		"newestOnTop": true,
		"progressBar": false,
		"positionClass": "toast-top-right",
		"preventDuplicates": true,
		"onclick": null,
		"showDuration": "300",
		"hideDuration": "1000",
		"timeOut": "7500",
		"extendedTimeOut": "1000",
		"showEasing": "swing",
		"hideEasing": "linear",
		"showMethod": "fadeIn",
		"hideMethod": "fadeOut"
	};
});

// Display a notification message with bootstrap message types
function displayMessage(type, message) {
	toastr[type](message);
}

// Retrieve the GET variables from url
function getUrlVars() {
	var vars = {};
	var parts = window.location.href.replace(/[?&]+([^=&]+)=([^&]*)/gi, function(m,key,value) {
		vars[key] = value;
	});
	return vars;
}

function isInt(value) {
  return !isNaN(value) &&
		 parseInt(Number(value)) == value &&
		 !isNaN(parseInt(value, 10));
}

function pad(num, len) {
	num = '' + num;
	while (num.length < len) {
		num = '0' + num;
	}

	return num;
}
