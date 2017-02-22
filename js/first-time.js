var currentPasswordTimer;
var confirmPasswordTimer;
var currentPasswordCorrect = false;
var passwordsMatch = false;

$(function() {
	$("#firsttimeButton").on({
		click: function () {
			if (currentPasswordCorrect && passwordsMatch) {
				$.post(ROOT_FOLDER + "php/customer/firsttimepasswordchange.php", {
					username: sessionStorage.username,
					password: $("#firsttimeNewPasswordInput").val()
				}, function(response) {
					if (response == 'success') {
						window.location = "dashboard.php#my-profile";
					} else {
						displayMessage('error', 'Something went wrong changing your password. The web admin has been notified.');
					}
				}).fail(function (request, textStatus, errorThrown) {
					displayMessage('error', "Error: Something went wrong with firsttime AJAX POST");
				});
			} else if (!currentPasswordCorrect) {
				displayMessage('error', "The current password entered doesn't match the temporary password sent to you.");
			} else if (!passwordsMatch) {
				displayMessage('error', "The passwords don't match.");
			}
		}
	});

	// Set timer to check current password
	$("#firsttimeCurrentPasswordInput").on({
		input: function () {
			currentPasswordTimer = setTimeout(checkCurrentPassword, 1000);
		}
	});

	// Set timer to check if passwords match
	$("#firsttimeNewPasswordInput, #firsttimeConfirmPasswordInput").on({
		input: function () {
			confirmPasswordTimer = setTimeout(doPasswordsMatch, 1000);
		}
	});
});

// Show change password container, set title, and active nav item
function checkCurrentPassword() {
	$.post(ROOT_FOLDER + "php/customer/checkcurrentpassword.php", {
		username: sessionStorage.username,
		password: $("#firsttimeCurrentPasswordInput").val()
	}, function(response) {
		if (response == 'correct') {
			currentPasswordCorrect = true;
			$("#firsttimeCurrentPasswordCross").hide();
			$("#firsttimeCurrentPasswordCheck").show();
		} else if (response == 'incorrect') {
			displayMessage('error', "The current password entered doesn't match the temporary password sent to you.");
			currentPasswordCorrect = false;
			$("#firsttimeCurrentPasswordCheck").hide();
			$("#firsttimeCurrentPasswordCross").show();
		} else {
			displayMessage('error', "Something went wrong checking your current password. The web admin has been notified and will fix the problem as soon as possible.");
			currentPasswordCorrect = false;
			$("#firsttimeCurrentPasswordCheck").hide();
			$("#firsttimeCurrentPasswordCross").show();
		}
	}).fail(function (request, textStatus, errorThrown) {
		displayMessage('error', "Error: Something went wrong with  AJAX POST");
	});
}

// Check if passwords match
function doPasswordsMatch() {
	var newPassword = $("#firsttimeNewPasswordInput").val();
	var confirmPassword = $("#firsttimeConfirmPasswordInput").val();
	if (newPassword != '' && confirmPassword != '') {
		if (newPassword == confirmPassword) {
			passwordsMatch = true;
			$("#firsttimeConfirmPasswordCross").hide();
			$("#firsttimeConfirmPasswordCheck").show();
		} else {
			passwordsMatch = false;
			$("#firsttimeConfirmPasswordCheck").hide();
			$("#firsttimeConfirmPasswordCross").show();
			displayMessage('error', "The passwords don't match.");
		}
	}
}
