$(function() {
    $("#registerButton").on({
        click: function() {
            $.post("./php/customer/checkusername.php", {
                username: $("#registerEmailInput").val()
            }, function(response) {
                if (response == 'doesntexist') {
                    // If the username isn't already in the database, create a new user
                    $.post("./php/customer/createnewcustomer.php", {
                        email: $("#registerEmailInput").val()
                    }, function(response) {
                        if (response == 'success') {
                            displayMessage('info', 'A temporary password has been sent to your email.');
                        } else {
                            displayMessage('error', 'There was a problem creating your account.');
                        }
                    }).fail(function (request, textStatus, errorThrown) {
                        displayMessage('error', "Error: Something went wrong with registerButton AJAX POST");
                    });
                } else if (response == 'exists') {
                    // If username is associated with an another account then inform the user
                    displayMessage("error", "There is already a customer with that email address. Please either login or choose a different email address.")
                } else {
                    displayMessage('error', "Error: Something went wrong checking if your email address is already taken");
                }
            }).fail(function (request, textStatus, errorThrown) {
                displayMessage('error', "Error: Something went wrong with usernameexists AJAX POST");
            });
        }
    });
});
