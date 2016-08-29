$(function() {
    $(document).keypress(function(e) {
        if(e.which == 13) {
            $("#loginButton").click();
        }
    });

    $("#loginButton").on({
        click: function () {
            $.post("./php/customer/login.php", {
                username: $("#loginEmailInput").val(),
                password: $("#loginPasswordInput").val()
            }, function(response) {
                if (response.substr(0, 7) == 'correct') {
                    // If username and password are correct and it's not the first time logging in then call setUserInfo and redirect to dashboard
                    setUserInfo(response, 7);
                    window.location = "dashboard.php#welcome";
                } else if (response.substr(0, 9) == 'firsttime') {
                    // If username and password are correct and it's the first time logging in then call setUserInfo and redirect to first-time to set new password
                    setUserInfo(response, 9);
                    window.location = "first-time.php"
                } else if (response == 'retired') {
                    // If the account has been marked as retired display a message
                    displayMessage('error', "This account has been marked as retired. If this is incorrect, please send an email to hello@hostkeep.com.au");
                } else if (response == 'incorrectpassword') {
                    // If username is correct but password isn't then display message
                    displayMessage('error', 'Your password appears to be incorrect, please try again');
                    $("#loginPasswordInput").val('');
                } else if (response == 'incorrectusername') {
                    // If username isn't in database then display message telling user to register for an account
                    displayMessage('error', "User does not exist, if you are a HostKeep client please register below");
                } else {
                    displayMessage('error', 'Something went wrong logging in. The web admin has been notified.');
                }
            }).fail(function (request, textStatus, errorThrown) {
                displayMessage('error', "Error: Something went wrong with login AJAX POST");
            });
        }
    });

    $("div#headerTitle").text("Login");
});

// Set sessionStorage variables
function setUserInfo(response, index) {
    var userInfo = JSON.parse(response.substr(index));
    sessionStorage.loggedIn = 'true';
    sessionStorage.customerID = userInfo['customerID'];
    sessionStorage.username = userInfo['username'];
    sessionStorage.salutation = userInfo['salutation'];
    sessionStorage.firstName = userInfo['firstName'];
    sessionStorage.lastName = userInfo['lastName'];
    sessionStorage.company = userInfo['company'];
    sessionStorage.phoneNumber = userInfo['phoneNumber'];
    sessionStorage.mobileNumber = userInfo['mobileNumber'];
    sessionStorage.bankName = userInfo['bankName'];
    sessionStorage.bsb = userInfo['bsb'];
    sessionStorage.accountNumber = userInfo['accountNumber'];
    sessionStorage.postalAddress = userInfo['postalAddress'];
    sessionStorage.suburb = userInfo['suburb'];
    sessionStorage.state = userInfo['state'];
    sessionStorage.postcode = userInfo['postcode'];
    sessionStorage.country = userInfo['country'];
    sessionStorage.lastModified = userInfo['lastModified'];
    sessionStorage.lastLogin = userInfo['lastLogin'];
    sessionStorage.lastLoginIP = userInfo['lastLoginIP'];
    sessionStorage.status = userInfo['status'];
}
