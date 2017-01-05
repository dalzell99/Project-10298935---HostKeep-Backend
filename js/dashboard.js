var ADMIN_USERNAME = "hello@hostkeep.com.au";
var AIRBNB_URL = "https://www.airbnb.com.au/rooms/";

var userList = [];
var userPropertyList = [];
var allPropertyList = [];
var documentList = [];
var filenameList = [];
var bookingList = [];
var changes = [];
var currentPasswordTimer;
var confirmPasswordTimer;
var currentPasswordCorrect = false;
var passwordsMatch = false;
var done = 0;
var currentProperty;
var checkinDatePicker;
var checkoutDatePicker;
var currentYear;
var currentMonth;
var currentAirbnbID;
var currentPropertyID;
var calendarContainer;
var currentDateInput;
var currentContractExpiryDate = '';

$(function() {
	if (sessionStorage.loggedIn === 'true') {
		// Change the displayed section based on the url hash
		$(window).on({
			// Fake responsive because of my bad planning. Switches between the property layouts based on viewport width. Used when switching from property subpage to property list page.
			resize: function () {
				if (window.location.hash.substr(1) === 'my-properties') {
					var width = (window.innerWidth > 0) ? window.innerWidth : screen.width;

					if (width <= 600) {
						$("#propertyMobile").show();
						$("#propertyTable").hide();
					} else {
						$("#propertyMobile").hide();
						$("#propertyTable").show();
					}
				}
			},

			hashchange: function() {
				switch(window.location.hash.substr(1)) {
					case '':
					case 'welcome':
						welcome();
						break;
					case 'my-profile':
						profile();
						break;
					case 'my-properties':
						properties();
						break;
					case 'documents':
						documents();
						break;
					case 'direct-booking':
						directBooking();
						break;
					case 'change-password':
						password();
						break;
					case 'admin':
						admin();
						break;
					default:
						var propertyName = window.location.hash.substr(1);
						var exists = userPropertyList.some(function (value) {
							if (value.name === propertyName) {
								sessionStorage.propertySubpage = value.propertyID;
								return true;
							} else {
								return false;
							}
						});

						if (exists) {
							propertySubpage();
						} else {
							displayMessage("warning", "You don't have permission to access that property subpage. If this is an error please send an email to hello@hostkeep.com.au");
						}
				}
			}
		});

		$("#headerLogoutButton").on({
			click: function () {
				logout();
			}
		});

		$("#adminCreateNewCustomerButton").on({
			click: function () {
				createNewUser();
			}
		});

		// Set the admin sessionStorage vaiable to true if the admin account is used.
		if (sessionStorage.username === ADMIN_USERNAME) {
			sessionStorage.admin = 'true';
			// Show admin section of dashboard if logged in as admin
			$(".admin").show();
			// Change the nav item widths to accommodate the Admin nav
			$("nav table td").css('width', '16.67%');
		} else if (sessionStorage.lastLogin !== '') {
			// If the user isn't admin, show username and last login time and IP address
			$("#headerCustomerInfo").html(sessionStorage.username);
		}

		// Get customer list if admin logged in
		if (sessionStorage.admin !== null && sessionStorage.admin === 'true') {
			$.post("./php/customer/getallcustomers.php", {

			}, function(response) {
				if (response === 'fail') {
					displayMessage('error', 'Something went wrong getting the customer list. The web admin has been notified and will fix the problem as soon as possible.');
				} else {
					// Dynamically create customer dropdown
					var html = '<select id="headerUserSelect">';
					userList = JSON.parse(response);
					userList.forEach(function(value, index, array) {
						var propertyString = '';
						value.properties.forEach(function (prop) {
							propertyString += prop.propertyID + ": " + prop.name + ", ";
						});

						// Remove last comma
						propertyString = propertyString.substr(0, propertyString.length - 2);

						html += "<option value='" + value.username + "'>" + value.firstName + " " + value.lastName + " - " + value.username + " (" + propertyString + ")</option>";
					});
					html += "</select>";

					$("#headerCustomerInfo").empty().append(html);

					$("#headerUserSelect").val(sessionStorage.username);

					$("#headerUserSelect").select2();

					// When a different user is selected change, get user info and call changeUser function
					$("#headerUserSelect").on({
						change: function () {
							$.post("./php/customer/getuserinfo.php", {
								username: $(this).val()
							}, function(response) {
								if (response === 'fail') {
									displayMessage('error', 'Something went wrong getting the customer information. The web admin has been notified and will fix the problem as soon as possible.');
								} else {
									changeUser(JSON.parse(response));
								}
							}).fail(function (request, textStatus, errorThrown) {
								displayMessage('error', "Error: Something went wrong with getuserinfo AJAX POST");
							});
						}
					});
				}
			}).fail(function (request, textStatus, errorThrown) {
				displayMessage('error', "Error: Something went wrong with getallcustomers AJAX POST");
			});
		} else {
			// Restrict the access of a user with a proposal account status
			if (sessionStorage.status === 'proposal') {
				$("#welcome .nonProposalUser").hide();
				$("#welcome .proposalUser").show();
				$("nav .properties, nav .documents, nav .directBooking").hide();
			}
		}

		// Get the direct bookings for the logged in user
		$.post("./php/directbooking/getbookings.php", {
			customerID: sessionStorage.customerID
		}, function(response) {
			if (response === 'fail') {
				displayMessage('error', 'Error retrieving the direct booking list. The web admin has been notified and will fix the problem as soon as possible.');
			} else {
				bookingList = JSON.parse(response);

				// Check if the getProperties and getDocuments post AJAX have finished
				if (done === 3) {
					$(window).hashchange();
					done = 0;
				} else {
					// Otherwise add 1 to done
					done += 1;
				}
			}
		}).fail(function (request, textStatus, errorThrown) {
			//displayMessage('error', "Error: Something went wrong with getproperties AJAX POST");
		});


		// Get the properties for the logged in user
		$.post("./php/properties/getproperties.php", {
			username: sessionStorage.username
		}, function(response) {
			if (response === 'fail') {
				displayMessage('error', 'Error retrieving user property list. The web admin has been notified and will fix the problem as soon as possible.');
			} else {
				userPropertyList = JSON.parse(response);

				// Check if the getBookings (if user isn't admin) and getDocuments post AJAX have finished
				if (done === 3) {
					$(window).hashchange();
					done = 0;
				} else {
					// Otherwise add 1 to done
					done += 1;
				}
			}
		}).fail(function (request, textStatus, errorThrown) {
			//displayMessage('error', "Error: Something went wrong with getproperties AJAX POST");
		});

		// Get all the properties
		$.post("./php/properties/getallproperties.php", {

		}, function(response) {
			if (response === 'fail') {
				displayMessage('error', 'Error retrieving all property list. The web admin has been notified and will fix the problem as soon as possible.');
			} else {
				allPropertyList = JSON.parse(response).sort(function (a, b) {
					if (a.name === b.name) {
						return 0;
					} else if (a.name > b.name) {
						return 1;
					} else {
						return -1;
					}
				});

				// Check if the other post AJAXs have finished
				if (done === 3) {
					$(window).hashchange();
					done = 0;
				} else {
					// Otherwise add 1 to done
					done += 1;
				}
			}
		}).fail(function (request, textStatus, errorThrown) {
			//displayMessage('error', "Error: Something went wrong with getproperties AJAX POST");
		});

		$.post("./php/documents/getdocuments.php", {
			username: sessionStorage.username
		}, function(response) {
			if (response === 'fail') {
				displayMessage('error', 'Error retrieving document list. The web admin has been notified and will fix the problem as soon as possible.');
			} else {
				documentList = response[0];
				$.each(response[1], function (key, value) {
					filenameList.push(value);
				});

				filenameList.sort();

				// Check if the getBookings (if user isn't admin) and getProperties post AJAX have finished
				if (done === 3) {
					$(window).hashchange();
					done = 0;
				} else {
					// Otherwise add 1 to done
					done += 1;
				}
			}
		}, 'json').fail(function (request, textStatus, errorThrown) {
			//displayMessage('error', "Error: Something went wrong with  AJAX POST");
		});
	} else {
		// If user came to page without logging in redirect to login page
		window.location = 'index.php';
	}
});

// Set sessionStorage variables and reload page
function changeUser(userInfo) {
	sessionStorage.customerID = userInfo.customerID;
	sessionStorage.username = userInfo.username;
	sessionStorage.salutation = userInfo.salutation;
	sessionStorage.firstName = userInfo.firstName;
	sessionStorage.lastName = userInfo.lastName;
	sessionStorage.propertyIDs = userInfo.propertyIDs;
	sessionStorage.documentIDs = userInfo.documentIDs;
	sessionStorage.company = userInfo.company;
	sessionStorage.phoneNumber = userInfo.phoneNumber;
	sessionStorage.mobileNumber = userInfo.mobileNumber;
	sessionStorage.bankName = userInfo.bankName;
	sessionStorage.bsb = userInfo.bsb;
	sessionStorage.accountNumber = userInfo.accountNumber;
	sessionStorage.postalAddress = userInfo.postalAddress;
	sessionStorage.suburb = userInfo.suburb;
	sessionStorage.state = userInfo.state;
	sessionStorage.postcode = userInfo.postcode;
	sessionStorage.country = userInfo.country;
	sessionStorage.lastModified = userInfo.lastModified;
	sessionStorage.lastLogin = userInfo.lastLogin;
	sessionStorage.lastLoginIP = userInfo.lastLoginIP;

	// If the admin is on a property subpage, change the hash to my-properties before reloading the page.
	if (['', 'welcome', 'my-profile', 'my-properties', 'documents', 'direct-booking', 'change-password', 'admin'].indexOf(window.location.hash.substr(1)) === -1) {
		window.location.hash = 'my-properties';
	}

	location.reload();
}

// Set sessionStorage variables to empty and redirect to login page
function logout() {
	sessionStorage.loggedIn = 'false';
	sessionStorage.admin = 'false';
	sessionStorage.username = '';
	sessionStorage.salutation = '';
	sessionStorage.firstName = '';
	sessionStorage.lastName = '';
	sessionStorage.company = '';
	sessionStorage.phoneNumber = '';
	sessionStorage.mobileNumber = '';
	sessionStorage.postalAddress = '';
	sessionStorage.suburb = '';
	sessionStorage.state = '';
	sessionStorage.postcode = '';
	sessionStorage.country = '';
	sessionStorage.lastModified = '';
	sessionStorage.lastLogin = '';
	sessionStorage.lastLoginIP = '';
	sessionStorage.timeZone = '';
	window.location = 'index.php';
}

// Hide all the section containers
function hideAllContainers() {
	$("div#welcome").hide();
	$("div#profile").hide();
	$("div#properties").hide();
	$("div#documents").hide();
	$("div#password").hide();
	$("div#directBooking").hide();
	$("div#admin").hide();

	// Hide calender icon link from footer
	$("#footer #calendarIconLink").hide();
}

// Show welcome container, set title, and active nav item and add last login time to bottom of page
function welcome() {
	hideAllContainers();
	$("div#headerTitle").text("Welcome");
	$("nav .active").removeClass("active");
	$("nav .welcome").addClass("active");
	$("#welcomeLastLogin").text(sessionStorage.lastLogin !== '' ? "[Last login: " + moment(sessionStorage.lastLogin).format("ddd Do MMM YYYY h:mm a") + "]" : "");
	$("div#welcome").show();
}

// Show profile container, set title, and active nav item and populate the input fields
function profile() {
	$("div#headerTitle").text("Profile");
	$("nav .active").removeClass("active");
	$("nav .profile").addClass("active");

	if (sessionStorage.admin !== 'true') {
		if (sessionStorage.salutation !== '') { $("#profileSalutation").prop('disabled', true); }
		if (sessionStorage.firstName !== '') { $("#profileFirstName").prop('disabled', true); }
		if (sessionStorage.lastName !== '') { $("#profileLastName").prop('disabled', true); }
	}

	$("#profileSalutation").val(sessionStorage.salutation);
	$("#profileFirstName").val(sessionStorage.firstName);
	$("#profileLastName").val(sessionStorage.lastName);
	$("#profileCompany").val(sessionStorage.company);
	$("#profileEmail").val(sessionStorage.username);
	$("#profileTelephone").val(sessionStorage.phoneNumber);
	$("#profileMobile").val(sessionStorage.mobileNumber);
	$("#profileBankName").val(sessionStorage.bankName);
	$("#profileBSB").val(sessionStorage.bsb);
	$("#profileAccountNumber").val(sessionStorage.accountNumber);
	$("#profileAddress").val(sessionStorage.postalAddress);
	$("#profileSuburb").val(sessionStorage.suburb);
	$("#profileState").val(sessionStorage.state);
	$("#profilePostcode").val(sessionStorage.postcode);
	$("#profileCountry").val(sessionStorage.country);
	$("#profileLastModified").text("[Last modified: " + moment(sessionStorage.lastModified).format("ddd Do MMM YYYY h:mm a") + "]");

	// Record what changes are made
	$("input.changeable, textarea.changeable").on({
		blur: function () {
			if ($(this).val() !== sessionStorage.contenteditable) {
				var classList = this.className.split(/\s+/);
				changes.push(classList[1]);
			}
		},

		focus: function () {
			sessionStorage.contenteditable = $(this).val();
		}
	});

	// Record what changes are made
	$("select.changeable").on({
		change: function () {
			var classList = this.className.split(/\s+/);
			changes.push(classList[1]);
		}
	});

	// Save profile changes on button click
	$("#profileButton").on({
		click: function () {
			$.post("./php/customer/saveprofilechanges.php", {
				admin: sessionStorage.admin,
				changes: JSON.stringify(changes),
				salutation: $("#profileSalutation").val(),
				firstName: $("#profileFirstName").val(),
				lastName: $("#profileLastName").val(),
				company: $("#profileCompany").val(),
				username: $("#profileEmail").val(),
				telephone: $("#profileTelephone").val(),
				mobile: $("#profileMobile").val(),
				bankName: $("#profileBankName").val(),
				bsb: $("#profileBSB").val(),
				accountNumber: $("#profileAccountNumber").val(),
				address: $("#profileAddress").val(),
				suburb: $("#profileSuburb").val(),
				state: $("#profileState").val(),
				postcode: $("#profilePostcode").val(),
				country: $("#profileCountry").val()
			}, function(response) {
				if (response === 'success') {
					displayMessage('info', 'Your profile changes have been saved.');
					// Change last modified text to the current time
					$("#profileLastModified").text("[Last modified: " + moment().format("ddd Do MMM YYYY h:mm a") + "]");

					changes = []; // Reset changes
				} else {
					displayMessage('error', 'Something went wrong while saving your profile changes. The web admin has been notified and will fix the problem as soon as possible.');
				}
			}).fail(function (request, textStatus, errorThrown) {
				displayMessage('error', "Error: Something went wrong with  AJAX POST");
			});
		}
	});

	hideAllContainers();
	$("div#profile").show();
}

// Show properties container, set title, and active nav item, dynamically create property table
function properties() {
	$("div#headerTitle").text("Properties");
	$("nav .active").removeClass("active");
	$("nav .properties").addClass("active");

	// Create property table
	var html = '';
	var htmlMobile = '';
	userPropertyList.forEach(function(value, index, array) {
		var imageURL = (value.imageURL === '' ? 'https://placeholdit.imgix.net/~text?txtsize=25&bg=ffffff&txt=No+Image&w=200&h=130&fm=png&txttrack=0' : value.imageURL);
		html += "<tr>";
		html += "    <td class='imageURL'><img src='" + imageURL + "' alt='' /></td>";
		html += "    <td class='propertyID' style='display:none'>" + value.propertyID + "</td>";
		html += "    <td class='name'>" + value.name + "</td>";
		html += "    <td class='description'>" + value.description + "</td>";
		html += "    <td class='address'>" + value.address + "</td>";
		html += "    <td class='propertyFee'>" + value.propertyFee + "%</td>";
		html += "    <td class='minimumNightlyPrice'>$" + value.minimumNightlyPrice + "</td>";
		html += "</tr>";

		htmlMobile += "<tr>";
		htmlMobile += "    <td class='imageURL'><img src='" + imageURL + "' alt='' /></td>";
		htmlMobile += "    <td class='propertyID' style='display:none'>" + value.propertyID + "</td>";
		htmlMobile += "    <td>";
		htmlMobile += "        <div><span class='name'><strong>" + value.name + "</strong></span>: " + value.description + "</div>";
		htmlMobile += "        <div>" + value.address + "</div>";
		htmlMobile += "        <div>Min. nightly price <strong class='minimumNightlyPrice'>$" + value.minimumNightlyPrice + "</strong></div>";
		htmlMobile += "    </td>";
		htmlMobile += "</tr>";
	});

	$("#propertyTable tbody").empty().append(html);
	$("#propertyMobile tbody").empty().append(htmlMobile);

	// Make table sortable
	var newTableObject = $("#properties #propertyTable")[0];
	sorttable.makeSortable(newTableObject);

	addPropertySubpageEvent();

	// Show add property button if user is admin
	if (sessionStorage.admin !== null && sessionStorage.admin === 'true') {
		$("#propertiesAdd").show();
	}

	// Move submit button below DropZone
	$("#propertiesDropzone").after($("#propertiesAddButton").parent());

	// Toggle add property section
	$("#propertiesShowAdd").on({
		click: function () {
			if ($("#propertiesAddNewProperty").css('display') === 'none') {
				$("#propertiesAddNewProperty").slideDown();
				$("#propertiesShowAdd").text("Hide Add New Property");
			} else {
				$("#propertiesAddNewProperty").slideUp();
				$("#propertiesShowAdd").text("Show Add New Property");
			}
		}
	});

	// Add property to the current user
	$("#propertiesAddButton").on({
		click: function () {
			var filename = $("#propertiesAddImageURL").val() !== '' ?
				$("#propertiesAddImageURL").val() :
				"http://owners.hostkeep.com.au/images/properties/" + $("#propertiesDropzone").find(".dz-filename:first > *").text();

			var valid = arePropertyInputsValid();
			if (valid[0]) {
				$.post("./php/properties/addproperty.php", {
					username: sessionStorage.username,
					propertyID: $("#propertiesAddID").val(),
					name: $("#propertiesAddName").val(),
					description: $("#propertiesAddDescription").val(),
					address: $("#propertiesAddAddress").val(),
					minimumNightlyPrice: $("#propertiesAddPrice").val(),
					propertyFee: $("#propertiesAddFee").val(),
					imageURL: filename
				}, function(response) {
					if (response.substr(0, 7) === 'success') {
						displayMessage('info', 'The property has been added to the current customer');

						// Add new property to table
						var html = '';
						var a = (sessionStorage.admin === 'true' ? true : false);
						var imageURL = (filename === '' ? 'https://placeholdit.imgix.net/~text?txtsize=25&bg=ffffff&txt=No+Image&w=200&h=130&fm=png&txttrack=0' : filename);
						html += "<tr>";
						html += "    <td class='imageURL'><img src='" + imageURL + "' alt='' /></td>";
						html += "    <td class='propertyID' style='display:none'>" + $("#propertiesAddID").val() + "</td>";
						html += "    <td class='name'>" + $("#propertiesAddName").val() + "</td>";
						html += "    <td class='description'>" + $("#propertiesAddDescription").val() + "</td>";
						html += "    <td class='address'>" + $("#propertiesAddAddress").val() + "</td>";
						html += "    <td class='propertyFee'>" + $("#propertiesAddFee").val() + "%</td>";
						html += "    <td class='minimumNightlyPrice'>$" + $("#propertiesAddPrice").val() + "</td>";
						html += "</tr>";

						$("#properties tbody").append(html);

						userPropertyList.push({
							'propertyID': $("#propertiesAddID").val(),
							'name': $("#propertiesAddName").val(),
							'description': $("#propertiesAddDescription").val(),
							'address': $("#propertiesAddAddress").val(),
							'minimumNightlyPrice': $("#propertiesAddPrice").val(),
							'propertyFee': $("#propertiesAddFee").val()
						});

						// Clear inputs
						$("#propertiesAddID").val('');
						$("#propertiesAddName").val('');
						$("#propertiesAddDescription").val('');
						$("#propertiesAddAddress").val('');
						$("#propertiesAddPrice").val('');
						$("#propertiesAddFee").val();

						addPropertySubpageEvent();

						// Remove image from upload box
						$("#propertiesDropzone > .dz-preview").remove();
					} else {
						displayMessage('error', 'Something went wrong added the property to the current user. The web admin has been notified and will fix the problem as soon as possible.');
					}
				}).fail(function (request, textStatus, errorThrown) {
					//displayMessage('error', "Error: Something went wrong with addproperty AJAX POST");
				});
			} else {
				displayMessage("warning", valid[1]);
			}
		}
	});

	hideAllContainers();
	$("#propertySubpage").hide();
	var width = (window.innerWidth > 0) ? window.innerWidth : screen.width;
	if (width <= 600) {
		$("#propertyMobile").show();
		$("#propertyTable").hide();
	} else {
		$("#propertyMobile").hide();
		$("#propertyTable").show();
	}
	$("div#properties").show();
}

function propertySubpage() {
	// get property info from list of users properties
	var exists = userPropertyList.some(function (value) {
		if (value.propertyID === sessionStorage.propertySubpage) {
			propertyInfo = value;
			return true;
		} else {
			return false;
		}
	});

	if (exists) {
		var a = (sessionStorage.admin === 'true' ? true : false);
		var imageURL = (propertyInfo.imageURL=== '' ? 'https://placeholdit.imgix.net/~text?txtsize=25&bg=ffffff&w=200&h=130&fm=png&txttrack=0' : propertyInfo.imageURL);

		var html = '';

		html += "<div class='col-md-6 col-md-push-6 col-xs-12'>";
		html += "    <table id='propertySubpageImageAndButton'>";
		html += "        <tr>";
		html += "            <td><img class='propertyImage' src='" + imageURL + "' alt=''></td>";
		html += "        </tr>";
		html += "        <tr>";
		html += "            <td class='propertySubpageSeparator'></td>";
		html += "        </tr>";
		html += "        <tr>";
		html += "            <td>";
		// html += "                <div>";
		// html += "                    <button onclick='showPreviousMonth()'>Previous</button>";
		// html += "                    <button onclick='showNextMonth()'>Next</button>";
		// html += "                </div>";
		html += "                <div id='calendarContainer'></div>";
		html += "            </td>";
		html += "        <tr>";
		html += "            <td class='calendarWarning'>Booking values are gross of cleaning fee, Airbnb fees, and management cost, so should be considered representative only. Please refer to the property End of Month report for confirmed values.</td>";
		html += "        </tr>";
		html += "        <tr>";
		html += "            <td>";
		html += "                <button class='propertySubpageLinkButtons' onclick='viewListingPage(" + propertyInfo.airbnbURL + ")'><img class='airbnbButtonImage' src='./images/airbnbLogo.png' alt='' />View Airbnb listing</button>";
		html += "                <button id='propertySubpageMakeBookingButton' class='propertySubpageLinkButtons' onclick='window.location.hash = \"#direct-booking\";'><img class='makeBookingButtonImage' src='./images/makeBooking.png' alt='' />Make a booking</button>";
		html += "            </td>";
		html += "        </tr>";
		html += "    </table>";
		html += "</div>";

		html += "<div id='infoContainer' class='col-md-6 col-md-pull-6 col-xs-12'>";
		html += "    <div class='row'>";
		html += "        <div class='col-xs-12'>";
		html += "            <table>";
		html += "                <tr>";
		html += "                    <td colspan='2'><strong>General</strong></td>";
		html += "                </tr>";
		html += "                <tr>";
		html += "                    <td>PropertyID</td>";
		html += "                    <td id='propertySubpagePropertyID'>" + propertyInfo.propertyID + "</td>";
		html += "                </tr>";
		html += "                <tr>";
		html += "                    <td>Property name</td>";
		html += "                    <td id='propertySubpageName' " + (a ? 'contenteditable=true' : '') + ">" + propertyInfo.name + "</td>";
		html += "                </tr>";
		html += "                <tr>";
		html += "                    <td>Property description</td>";
		html += "                    <td id='propertySubpageDescription' " + (a ? 'contenteditable=true' : '') + ">" + propertyInfo.description + "</td>";
		html += "                </tr>";
		html += "                <tr>";
		html += "                    <td>Address</td>";
		html += "                    <td id='propertySubpageAddress' " + (a ? 'contenteditable=true' : '') + ">" + propertyInfo.address + "</td>";
		html += "                </tr>";
		html += "            </table>";
		html += "        </div>";
		html += "    </div>";
		html += "    <div class='row'>";
		html += "        <div class='col-xs-12 col-sm-6'>";
		html += "            <table>";
		html += "                <tr>";
		html += "                    <td colspan='2'><strong>Price engine settings</strong></td>";
		html += "                </tr>";
		html += "                <tr>";
		html += "                    <td>Base price</td>";
		html += "                    <td>";
		html += "                        $<div id='propertySubpageBasePrice' contenteditable='true'>" + propertyInfo.basePrice + "</div>";
		html += "                    </td>";
		html += "                </tr>";
		html += "                <tr>";
		html += "                    <td>Minimum price</td>";
		html += "                    <td>";
		html += "                        $<div id='propertySubpageMinimumNightlyPrice' contenteditable='true'>" + propertyInfo.minimumNightlyPrice + "</div>";
		html += "                    </td>";
		html += "                </tr>";
		html += "            </table>";
		html += "        </div>";
		html += "        <div class='col-xs-12 col-sm-6'>";
		html += "            <table>";
		html += "                <tr>";
		html += "                    <td colspan='2'><strong>HostKeep management</strong></td>";
		html += "                </tr>";
		html += "                <tr>";
		html += "                    <td>Commencement date</td>";
		if (a) {
			if (propertyInfo.commencementDate !== '') {
				html += "            <td><input id='propertySubpageCommencementDate' type='date' name='commencementDate' data-value='" + propertyInfo.commencementDate + "' /></td>";
			} else {
				html += "            <td><input id='propertySubpageCommencementDate' type='date' name='commencementDate' /></td>";
			}
		} else {
			if (propertyInfo.commencementDate !== '') {
				html += "            <td>" + moment(propertyInfo.commencementDate).format('ddd Do MMM YYYY') + "</td>";
			} else {
				html += "            <td>Hasn't been set yet</td>";
			}
		}
		html += "                </tr>";
		html += "                <tr>";
		html += "                    <td>Management fee</td>";
		if (a) {
			html += "                    <td><div id='propertySubpagePropertyFee' contenteditable=true>" + propertyInfo.propertyFee + "</div>%</td>";
		} else {
			if (propertyInfo.propertyFee === '') {
				html += "                    <td><span id='propertySubpagePropertyFee'></span>Hasn't been set yet</td>";
			} else {
				html += "                    <td><span id='propertySubpagePropertyFee'>" + propertyInfo.propertyFee + "</span>%</td>";
			}
		}
		html += "                </tr>";
		html += "                <tr>";
		html += "                    <td>Cleaning fee</td>";
		if (a) {
			html += "                    <td>$<div id='propertySubpageCleaningFee' contenteditable=true>" + propertyInfo.cleaningFee + "</div></td>";
		} else {
			if (propertyInfo.cleaningFee === '') {
				html += "                    <td><span id='propertySubpageCleaningFee'></span>Hasn't been set yet</td>";
			} else {
				html += "                    <td>$<span id='propertySubpageCleaningFee'>" + propertyInfo.cleaningFee + "</span></td>";
			}
		}

		html += "                </tr>";
		html += "                <tr>";
		html += "                    <td>Contract renewal date</td>";
		if (a) {
			if (propertyInfo.contractExpiryDate !== '') {
				currentContractExpiryDate = propertyInfo.contractExpiryDate;
				html += "            <td><input id='propertySubpageContractExpiryDate' type='date' name='contractExpiryDate' data-value='" + propertyInfo.contractExpiryDate + "' /></td>";
			} else {
				html += "            <td><input id='propertySubpageContractExpiryDate' type='date' name='contractExpiryDate' /></td>";
			}
		} else {
			if (propertyInfo.contractExpiryDate !== '') {
				html += "            <td>" + moment(propertyInfo.contractExpiryDate).format('ddd Do MMM YYYY') + "</td>";
			} else {
				html += "            <td>Hasn't been set yet</td>";
			}
		}
		html += "                </tr>";
		html += "            </table>";
		html += "        </div>";
		html += "    </div>";
		html += "    <div class='row'>";
		html += "        <div class='col-xs-12'>";
		html += "            <table>";
		html += "                <tr>";
		html += "                    <td colspan='2'><strong>Related links</strong></td>";
		html += "                </tr>";
		html += "                <tr>";
		html += "                    <td>Airbnb URL</td>";
		if (a) {
			html += "                    <td>" + AIRBNB_URL + "<div id='propertySubpageAirbnbURL' contenteditable=true>" + propertyInfo.airbnbURL + "</div></td>";
		} else {
			if (propertyInfo.airbnbURL === '') {
				html += "                    <td><span id='propertySubpageAirbnbURL'></span>Hasn't been set yet</td>";
			} else {
				html += "                    <td><a href='" + AIRBNB_URL + propertyInfo.airbnbURL + "' target='_blank'>" + AIRBNB_URL + "<span id='propertySubpageAirbnbURL'>" + propertyInfo.airbnbURL + "</span></a></td>";
			}
		}

		html += "                </tr>";
		html += "                <tr>";
		html += "                    <td>Guest greet guide</td>";
		if (a) {
			html += "                    <td id='propertySubpageGuestGreetURL' contenteditable=true>" + propertyInfo.guestGreetURL + "</td>";
		} else {
			if (propertyInfo.guestGreetURL === '') {
				html += "                    <td><span id='propertySubpageGuestGreetURL'></span>Hasn't been set yet</td>";
			} else {
				html += "                    <td><a href='" + propertyInfo.guestGreetURL + "' target='_blank'><span id='propertySubpageGuestGreetURL'>" + propertyInfo.guestGreetURL + "</span></a></td>";
			}
		}
		html += "                </tr>";
		html += "                <tr>";
		html += "                    <td>Self-check-in URL</td>";
		if (a) {
			html += "                    <td id='propertySubpageSelfCheckinURL' contenteditable=true>" + propertyInfo.selfCheckinURL + "</td>";
		} else {
			if (propertyInfo.selfCheckinURL === '') {
				html += "                    <td><span id='propertySubpageSelfCheckinURL'></span>Hasn't been set yet</td>";
			} else {
				html += "                    <td><a href='" + propertyInfo.selfCheckinURL + "' target='_blank'><span id='propertySubpageSelfCheckinURL'>" + propertyInfo.selfCheckinURL + "</span></a></td>";
			}
		}
		html += "                </tr>";
		if (a) {
			html += "            <tr>";
			html += "                <td>iCal URL</td>";
			html += "                <td id='propertySubpageIcalURL' contenteditable=true>" + propertyInfo.icalURL + "</td>";
			html += "            </tr>";
		} else {
			html += "            <tr style='display:none'>";
			html += "                <td id='propertySubpageIcalURL'" + propertyInfo.icalURL + "</td>";
			html += "            </tr>";
		}
		html += "            </table>";
		html += "        </div>";
		html += "    </div>";
		html += "    <div class='row'>";
		html += "        <div class='col-xs-12'>";
		html += "            <table>";
		html += "                <tr>";
		html += "                    <td colspan='2'><strong>Admin Settings</strong></td>";
		html += "                </tr>";
		html += "                <tr>";
		html += "                    <td>Property status</td>";
		html += "                    <td>";
		if (a) {
			html += "                    <select id='propertySubpagePropertyStatus'>";
			html += "                        <option value='proposed'>Proposed</option>";
			html += "                        <option value='active'>Active</option>";
			html += "                        <option value='retired'>Retired</option>";
			html += "                    </select>";
		} else {
			html += "                    <span id='propertySubpagePropertyStatus'>" + propertyInfo.status + "</span>";
		}
		html += "                    </td>";
		html += "                </tr>";
		html += "                <tr>";
		html += "                    <td>Commencement Fee</td>";
		if (a) {
			html += "                    <td>$<div id='propertySubpageCommencementFee' contenteditable=true>" + propertyInfo.commencementFee + "</div></td>";
		} else {
			if (propertyInfo.commencementFee === '') {
				html += "                    <td><span id='propertySubpageCommencementFee'></span>Hasn't been set yet</td>";
			} else {
				html += "                    <td>$<span id='propertySubpageCommencementFee'>" + propertyInfo.commencementFee + "</span></td>";
			}
		}
		html += "                </tr>";
		html += "                <tr>";
		html += "                    <td>Fee received</td>";
		html += "                    <td>";
		if (a) {
			html += "                        <input id='propertySubpageCommencementFeeReceivedYes' type='radio' name='commencementFee'>Yes";
			html += "                        <input id='propertySubpageCommencementFeeReceivedNo' type='radio' name='commencementFee'>No";
		} else {
			html += propertyInfo.commencementFeeReceived;
		}
		html += "                    </td>";
		html += "                </tr>";
		html += "            </table>";
		html += "        </div>";
		html += "    </div";
		html += "</div>";
		html += "<div><button onclick='saveSubpageInfo()'>Save Changes</button></div>";

		$("div#headerTitle").text(propertyInfo.name + " - " + propertyInfo.description);

		$("#propertyTable, #propertyMobile, #propertiesAdd").hide();
		$("#propertySubpage").empty().append(html).show();

		if (a) {
			$("#propertySubpageCommencementDate").pickadate({
				format: 'ddd d mmm yyyy',
				formatSubmit: 'yyyy/mm/dd',
				hiddenName: true
			});

			$("#propertySubpageContractExpiryDate").pickadate({
				format: 'ddd d mmm yyyy',
				formatSubmit: 'yyyy/mm/dd',
				hiddenName: true
			});

			$("#propertySubpagePropertyStatus").val(propertyInfo.status);

			if (propertyInfo.commencementFeeReceived === 'true') {
				$("#propertySubpageCommencementFeeReceivedYes").prop('checked', true);
			} else {
				$("#propertySubpageCommencementFeeReceivedNo").prop('checked', true);
			}
		}

		addPropertySubpageImageEvent();

		// Create calendar
		currentAirbnbID = propertyInfo.airbnbURL;
		calendarContainer = "#calendarContainer";
		currentYear = moment().get('year');
		currentMonth = moment().get('month');
		createCalendar();

		$("#footer #calendarIconLink").show();
		$("div#properties").show();
	} else {
		displayMessage("warning", "You don't have permission to access that property subpage. If this is an error please send an email to hello@hostkeep.com.au");
	}
}

function viewListingPage(url) {
	window.open(AIRBNB_URL + url);
}

function saveSubpageInfo() {
	var commencementFeeReceived;
	var status;
	var commencementDate;
	var contractExpiryDate;

	if ($("#propertySubpageCommencementFeeReceivedYes").prop('checked')) {
		commencementFeeReceived = 'true';
	} else {
		commencementFeeReceived = 'false';
	}

	if (sessionStorage.admin === 'true') {
		status = $("#propertySubpagePropertyStatus").val();
	} else {
		status = $("#propertySubpagePropertyStatus").text();
	}

	// This is undefined if the customer is saving the property info
	if ($("#propertySubpage input[name='commencementDate']").val() === undefined) {
		commencementDate = '';
	} else {
		// The datepicker dates are stored in a hidden input. This gets the first one
		commencementDate = $("#propertySubpage input[name='commencementDate']").val();
	}

	// This is undefined if the customer is saving the property info
	if ($("#propertySubpage input[name='contractExpiryDate']").val() === undefined) {
		contractExpiryDate = '';
	} else {
		// The datepicker dates are stored in a hidden input. This gets the second one
		contractExpiryDate = $("#propertySubpage input[name='contractExpiryDate']").val();
	}

	var contractExpiryDateChanged = (contractExpiryDate != currentContractExpiryDate ? 'true' : 'false');

	$.post("./php/properties/savesubpagechanges.php", {
		propertyID: $("#propertySubpagePropertyID").text(),
		name: $("#propertySubpageName").text(),
		description: $("#propertySubpageDescription").text(),
		address: $("#propertySubpageAddress").text(),
		basePrice: $("#propertySubpageBasePrice").text(),
		minimumNightlyPrice: $("#propertySubpageMinimumNightlyPrice").text(),
		commencementDate: commencementDate,
		propertyFee: $("#propertySubpagePropertyFee").text(),
		cleaningFee: $("#propertySubpageCleaningFee").text(),
		contractExpiryDate: contractExpiryDate,
		contractExpiryDateChanged: contractExpiryDateChanged,
		airbnbURL: $("#propertySubpageAirbnbURL").text(),
		guestGreetURL: $("#propertySubpageGuestGreetURL").text(),
		selfCheckinURL: $("#propertySubpageSelfCheckinURL").text(),
		icalURL: $("#propertySubpageIcalURL").text(),
		status: status,
		commencementFee: $("#propertySubpageCommencementFee").text(),
		commencementFeeReceived: commencementFeeReceived
	}, function(response) {
		if (response === 'success') {
			displayMessage('info', 'Changes have been saved');
		} else {
			displayMessage('error', 'Something went wrong saving the changes you have made. The web admin has been notified and will fix the problem as soon as possible.');
		}
	}).fail(function (request, textStatus, errorThrown) {
		//displayMessage('error', "Error: Something went wrong with  AJAX POST");
	});
}

function arePropertyInputsValid() {
	if ($("#propertiesAddID").val() === '') {
		return [false, "Please enter an ID"];
	}

	if ($("#propertiesAddName").val() === '') {
		return [false, "Please enter a property name"];
	}

	if ($("#propertiesAddDescription").val() === '') {
		return [false, "Please enter a description"];
	}

	if ($("#propertiesAddAddress").val() === '') {
		return [false, "Please enter an address"];
	}

	if ($("#propertiesAddPrice").val() === '') {
		return [false, "Please enter a minimum nightly price"];
	} else if (!isInt($("#propertiesAddPrice").val().substr(1))) {
		return [false, "The minimum nightly price must be a whole number"];
	}

	if ($("#propertiesDropzone").find(".dz-filename:first > *").text() === '' && $("#propertiesAddImageURL").val() === '') {
		return [false, "Please add an image"];
	}

	return [true];
}

// Show documents container, set title, and active nav item
function documents() {
	$("div#headerTitle").text("Documents");
	$("nav .active").removeClass("active");
	$("nav .documents").addClass("active");

	var months = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];

	// Create property table
	var html = '';
	var htmlTemp = '';
	var html2016 = '';
	var html2017 = '';
	var html2018 = '';
	documentList.forEach(function(value, index, array) {
		var a = (sessionStorage.admin === 'true' ? true : false);
		htmlTemp += "<tr>";
		htmlTemp += "    <td class='name'><div onclick='viewDocument(\"" + value.documentFilename + "\")'>" + value.name + "</div></td>";
		htmlTemp += "    <td class='propertyID'>" + value.propertyName + "</td>";
		htmlTemp += "    <td class='month' sorttable_customkey='" + value.year + pad(months.indexOf(value.month), 2) + "'>" + value.month + "</td>";
		htmlTemp += "    <td class='year' sorttable_customkey='" + value.year +  pad(months.indexOf(value.month), 2) + "'>" + value.year + "</td>";
		htmlTemp += "    <td><img src='images/download.png' alt='' onclick='viewDocument(\"" + value.documentFilename + "\")' /></td>";
		if (sessionStorage.admin !== null && sessionStorage.admin === 'true') {
			htmlTemp += "    <td><img src='images/delete.png' alt='' onclick='deleteDocument(\"" + value.documentID + "\")' /></td>";
		}
		htmlTemp += "    <td></td>";
		htmlTemp += "    <td style='display:none'>" + value.documentID + "</td>";
		htmlTemp += "</tr>";

		html += htmlTemp;
		if (value.year == 2015) {
			html2016 += htmlTemp;
		} else if (value.year == 2016) {
			if (months.indexOf(value.month) < 6) {
				html2016 += htmlTemp;
			} else {
				html2017 += htmlTemp;
			}
		} else if (value.year == 2017) {
			if (months.indexOf(value.month) < 6) {
				html2017 += htmlTemp;
			} else {
				html2018 += htmlTemp;
			}
		} else if (value.year == 2018) {
			if (months.indexOf(value.month) < 6) {
				html2018 += htmlTemp;
			}
		}

		htmlTemp = '';
	});

	$("#documents #documentsAll tbody").empty().append(html);
	$("#documents #documents2016 tbody").empty().append(html2016);
	$("#documents #documents2017 tbody").empty().append(html2017);
	$("#documents #documents2018 tbody").empty().append(html2018);

	// Make table sortable
	$("#documents table").each(function (i) {
		var newTableObject = $("#documents table")[i];
		sorttable.makeSortable(newTableObject);

		// Sort by month
		var monthHeader = $("#documents th:nth-child(3)")[i];
		sorttable.innerSortFunction.apply(monthHeader, []);
	});

	// Set current year as default tab
	var year = moment().get('year');
	var month = moment().get('month');
	if (year == 2015) {
		$("#documents > .nav.nav-tabs > li:nth-child(2) > a").click();
	} else if (year == 2016) {
		if (month < 6) {
			$("#documents > .nav.nav-tabs > li:nth-child(2) > a").click();
		} else {
			$("#documents > .nav.nav-tabs > li:nth-child(3) > a").click();
		}
	} else if (year == 2017) {
		if (month < 6) {
			$("#documents > .nav.nav-tabs > li:nth-child(3) > a").click();
		} else {
			$("#documents > .nav.nav-tabs > li:nth-child(4) > a").click();
		}
	} else if (year == 2018) {
		if (month < 6) {
			$("#documents > .nav.nav-tabs > li:nth-child(4) > a").click();
		}
	}

	// If user is admin, add filenames to dropdown to be selected
	if (sessionStorage.admin === 'true') {
		// Add filenames to dropdown
		var htmlFilename = "<option value=''></option>";

		$.each(filenameList, function(key, value) {
			htmlFilename += "<option value='" + value + "'>" + value + "</option>";
		});

		$("#documentsAddFilename").empty().append(htmlFilename).show();
	}

	// Add contenteditable change events
	addDocumentChangeEvent();

	// Show add property button if user is admin
	if (sessionStorage.admin !== null && sessionStorage.admin === 'true') {
		$("#documentsAdd").show();
	}

	// Move submit button below DropZone
	$("#documentsDropzone").after($("#documentsAddButton").parent());

	// Empty property dropdown and rebuild it
	$("#documentsAddPropertyID").empty();
	allPropertyList.forEach(function (value) {
		$("#documentsAddPropertyID").append("<option value='" + value.propertyID + "'>" + value.name + "</option>");
	});

	// Set default selected month as last month
	var currentMonth = (new Date().getMonth() + 11) % 12 + 1;
	$("#documentsAddMonth option:nth-child(" + currentMonth + ")").prop("selected", true);

	// Toggle add property section
	$("#documentsShowAdd").on({
		click: function () {
			if ($("#documentsAddNewDocument").css('display') === 'none') {
				$("#documentsAddNewDocument").slideDown();
				$("#documentsShowAdd").text("Hide Add New Document");
			} else {
				$("#documentsAddNewDocument").slideUp();
				$("#documentsShowAdd").text("Show Add New Document");
			}
		}
	});

	$("#documentsAddButton").on({
		click: function () {
			// Get the filename value from the span child of the first dz-filename (DropZone)
			var filename = $("#documentsAddFilename").val() !== '' ?
				$("#documentsAddFilename").val() :
				$("#documentsDropzone").find(".dz-filename:first > *").text();

			var filenameMinusExtension = filename.substr(0, filename.lastIndexOf("."));

			if (filename !== '') {
				$.post("./php/documents/adddocument.php", {
					name: filenameMinusExtension,
					propertyID: $("#documentsAddPropertyID").val(),
					month: $("#documentsAddMonth").val(),
					year: $("#documentsAddYear").val(),
					notes: $("#documentsAddNotes").val(),
					filename: filename
				}, function(response) {
					if (response[0] === 'success') {
						displayMessage('info', 'The document has been added to the current customer');

						if (response[1] === sessionStorage.username) {
							// Add new document to table
							var html = '';
							var a = (sessionStorage.admin === 'true' ? true : false);
							html += "<tr>";
							html += "    <td class='name'><div onclick='viewDocument(\"" + filename + "\")'>" + filenameMinusExtension + "</div></td>";
							html += "    <td class='propertyID'>" + $("#documentsAddPropertyID option:selected").text() + "</td>";
							html += "    <td class='month' sorttable_customkey='" + $("#documentsAddYear").val() + pad(months.indexOf($("#documentsAddMonth").val()), 2) + "'>" + $("#documentsAddMonth").val() + "</td>";
							html += "    <td class='year' sorttable_customkey='" + $("#documentsAddYear").val() + pad(months.indexOf($("#documentsAddMonth").val()), 2) + "'>" + $("#documentsAddYear").val() + "</td>";
							html += "    <td><img src='images/download.png' alt='' onclick='viewDocument(\"" + filename + "\")' /></td>";
							html += "    <td><img src='images/delete.png' alt='' onclick='deleteDocument(\"" + response[2] + "\")' /></td>";
							html += "    <td></td>";
							html += "    <td style='display:none'>" + response[2] + "</td>";
							html += "</tr>";

							$("#documents tbody").append(html);

							documentList.push({
								'documentID': response[2],
								'name': $("#documentsAddName").val(),
								'propertyID': $("#documentsAddPropertyID").val(),
								'month': $("#documentsAddMonth").val(),
								'dateUploaded': moment(),
								'notes': $("#documentsAddNotes").val(),
								'documentFilename': filename
							});
						}

						// Clear inputs
						//$("#documentsAddName").val('');
						//$("#documentsAddPropertyID").val('');
						$("#documentsAddNotes").val('');

						// Add contenteditable change event to the just added table row
						addDocumentChangeEvent();

						// Remove document from upload box
						$("#documentsDropzone > .dz-preview").remove();

						// Remove document from dropdown
						var selectedIndex = $("#documentsAddFilename").prop("selectedIndex") + 1;
						$("#documentsAddFilename option:nth-child(" + selectedIndex + ")").css("display", "none");
						$("#documentsAddFilename").prop("selectedIndex", 0);
					} else {
						displayMessage('error', 'Something went wrong added the document to the current user. The web admin has been notified and will fix the problem as soon as possible.');
					}
				}, 'json').fail(function (request, textStatus, errorThrown) {
					//displayMessage('error', "Error: Something went wrong with  AJAX POST");
				});
			} else {
				displayMessage('error', 'Please upload a document below before continuing');
			}
		}
	});

	hideAllContainers();
	$("div#documents").show();
}

function viewDocument(filename) {
	window.open("./documents/" + filename);
}

function deleteDocument(id) {
	$.post("./php/documents/deletedocument.php", {
		documentID: id
	}, function(response) {
		if (response === 'success') {
			displayMessage('info', 'Document has been deleted.');
			location.reload();
		} else {
			displayMessage('error', 'Something went wrong trying to delete the document. The web admin has been notified and will fix the problem as soon as possible.');
		}
	}).fail(function (request, textStatus, errorThrown) {
		//displayMessage('error', "Error: Something went wrong with  AJAX POST");
	});
}

function password() {
	$("div#headerTitle").text("Change Password");

	// Set timer to check current password
	$("#changepasswordCurrentPassword").on({
		input: function () {
			currentPasswordTimer = setTimeout(checkCurrentPassword, 1000);
		}
	});

	// Set timer to check if passwords match
	$("#changepasswordConfirmPassword").on({
		input: function () {
			confirmPasswordTimer = setTimeout(doPasswordsMatch, 1000);
		}
	});

	// Change the password for the current user
	$("#changepasswordButton").on({
		click: function () {
			if (currentPasswordCorrect && passwordsMatch) {
				$.post("./php/customer/changepassword.php", {
					username: sessionStorage.username,
					password: $("#changepasswordNewPassword").val()
				}, function(response) {
					if (response === 'success') {
						displayMessage('info', 'Your password has been changed');
					} else {
						displayMessage('error', 'Error while changing your password. The web admin has been notified and will fix the problem as soon as possible.');
					}
				}).fail(function (request, textStatus, errorThrown) {
					displayMessage('error', "Error: Something went wrong with changepassword AJAX POST");
				});
			} else if (!currentPasswordCorrect) {
				displayMessage('error', "The current password entered doesn't match the temporary password sent to you.");
			} else if (!passwordsMatch) {
				displayMessage('error', "The passwords don't match.");
			}
		}
	});

	hideAllContainers();
	$("div#password").show();
}

// Check the current password against the database and show tick or cross based on response
function checkCurrentPassword() {
	$.post("./php/customer/checkcurrentpassword.php", {
		username: sessionStorage.username,
		password: $("#changepasswordCurrentPassword").val()
	}, function(response) {
		if (response === 'correct') {
			toastr.clear();
			currentPasswordCorrect = true;
			$("#changepasswordCurrentPasswordCross").hide();
			$("#changepasswordCurrentPasswordCheck").show();
		} else if (response === 'incorrect') {
			displayMessage('error', "The password entered doesn't match your current password.");
			currentPasswordCorrect = false;
			$("#changepasswordCurrentPasswordCheck").hide();
			$("#changepasswordCurrentPasswordCross").show();
		} else {
			displayMessage('error', "Something went wrong checking your current password. The web admin has been notified and will fix the problem as soon as possible.");
			currentPasswordCorrect = false;
			$("#changepasswordCurrentPasswordCheck").hide();
			$("#changepasswordCurrentPasswordCross").show();
		}
	}).fail(function (request, textStatus, errorThrown) {
		displayMessage('error', "Error: Something went wrong with  AJAX POST");
	});
}

// Check if passwords match
function doPasswordsMatch() {
	var newPassword = $("#changepasswordNewPassword").val();
	var confirmPassword = $("#changepasswordConfirmPassword").val();
	if (newPassword !== '' && confirmPassword !== '') {
		if (newPassword === confirmPassword) {
			toastr.clear();
			passwordsMatch = true;
			$("#changepasswordConfirmPasswordCross").hide();
			$("#changepasswordConfirmPasswordCheck").show();
		} else {
			passwordsMatch = false;
			$("#changepasswordConfirmPasswordCheck").hide();
			$("#changepasswordConfirmPasswordCross").show();
			displayMessage('error', "The passwords don't match.");
		}
	}
}

function directBooking() {
	$("div#headerTitle").text("Direct Booking");
	$("nav .active").removeClass("active");
	$("nav .directBooking").addClass("active");

	// Add properties to dropdown
	var htmlProp = '';
	userPropertyList.forEach(function (value) {
		htmlProp += "<option value='" + value.propertyID + "'>" + value.name + "</option>";
	});
	$("#directBookingAddProperty").empty().append(htmlProp);

	// Create booking table
	var html = '';
	bookingList.forEach(function(value, index, array) {
		html += "<tr>";
		html += "    <td class='propertyID'>" + value.propertyName + "</td>";
		html += "    <td class='guestName'>" + value.guestName + "</td>";
		html += "    <td class='guestCheckIn'>" + moment(value.guestCheckIn).format('ddd Do MMM YYYY') + "</td>";
		html += "    <td class='guestCheckOut'>" +  moment(value.guestCheckOut).format('ddd Do MMM YYYY') + "</td>";
		html += "    <td class='invoiced'><input type='checkbox' " + (value.invoiced === 'true' ? 'checked' : '') + " /></td>";
		html += "    <td class='cleanUp'>" + value.cleanUp + "</td>";
		if (value.invoiced === 'true') {
			html += "    <td class='nightlyRate'>$" + value.nightlyRate + "</td>";
		} else {
			html += "    <td></td>";
		}
		html += "    <td><img src='images/delete.png' alt='' onclick='deleteBooking(" + value.bookingID + ")' />";
		html += "</tr>";
	});
	$("#directBooking #bookingTable tbody").empty().append(html);

	// Make table sortable
	var newTableObject = $("#directBooking #bookingTable")[0];
	sorttable.makeSortable(newTableObject);

	// Create calendar
	currentPropertyID = $("#directBookingAddProperty").val();
	calendarContainer = "#directBookingCalendarContainer .content";
	currentYear = moment().get('year');
	currentMonth = moment().get('month');
	createCalendar();

	$("#directBookingAddProperty").on({
		change: function () {
			currentPropertyID = $("#directBookingAddProperty").val();
			currentYear = moment().get('year');
			currentMonth = moment().get('month');
			createCalendar();
		}
	});

	$("#directBookingAddCheckIn").on({
		click: function () {
			currentDateInput = 'checkin';
			$("#directBookingCalendarContainer").show();
		}
	});

	$("#directBookingAddCheckOut").on({
		click: function () {
			currentDateInput = 'checkout';
			$("#directBookingCalendarContainer").show();
		}
	});

	$("#directBookingAddInvoiceYes").on({
		click: function () {
			$("#nightlyRateRow").show();
		}
	});

	$("#directBookingAddInvoiceNo").on({
		click: function () {
			$("#nightlyRateRow").hide();
		}
	});

	// Add property to the current user
	$("#directBookingAddButton").on({
		click: function () {
			var valid = areDirectBookingInputsValid();

			if (valid[0]) {
				var invoice = ($("#directBookingAddInvoiceYes").prop('checked') ? 'true' : 'false');

				var cleanUp;
				if ($("#directBookingAddInvoiceHostkeep").prop('checked')) {
					cleanUp = 'HostKeep - Guest Invoiced';
				} else if ($("#directBookingAddInvoiceHostkeepBilled").prop('checked')) {
					cleanUp = 'HostKeep - Owners Billed';
				} else {
					cleanUp = 'Guest';
				}

				$.post("./php/directbooking/addbooking.php", {
					customerID: sessionStorage.customerID,
					propertyID: $("#directBookingAddProperty").val(),
					guestName: $("#directBookingAddGuestName").val(),
					guestMobile: $("#directBookingAddGuestMobile").val(),
					guestEmail: $("#directBookingAddGuestEmail").val(),
					guestCheckIn: $("#directBookingAddCheckIn").prop('date'),
					guestCheckOut: $("#directBookingAddCheckOut").prop('date'),
					invoiced: invoice,
					nightlyRate: $("#directBookingNightlyRate").val(),
					cleanUp: cleanUp,
					notes: $("#directBookingAddNotes").val(),
					admin: sessionStorage.admin,
					username: sessionStorage.username,
					propertyName: $("#directBookingAddProperty option:selected").text()
				}, function(response) {
					if (response.substr(0, 7) === 'success') {
						displayMessage('info', 'The booking has been added');

						// Add new property to table
						var html = '';
						html += "<tr>";
						html += "    <td class='propertyID'>" + $("#directBookingAddProperty").children(":selected").text() + "</td>";
						html += "    <td class='guestName'>" + $("#directBookingAddGuestName").val() + "</td>";
						html += "    <td class='guestCheckIn'>" + $("#directBookingAddCheckIn").val() + "</td>";
						html += "    <td class='guestCheckOut'>" + $("#directBookingAddCheckOut").val() + "</td>";
						html += "    <td class='invoiced'><input type='checkbox' " + (invoice === 'true' ? 'checked' : '') + " /></td>";
						html += "    <td class='cleanUp'>" + cleanUp + "</td>";
						html += "    <td class='nightlyRate'>$" + $("#directBookingNightlyRate").val() + "</td>";
						html += "    <td><img src='images/delete.png' alt='' onclick='deleteBooking(" + response.substr(7) + ")' />";
						html += "</tr>";

						$("#directBooking #bookingTable tbody").append(html);

						bookingList.push({
							'customerID': sessionStorage.customerID,
							'propertyID': $("#directBookingAddProperty").val(),
							'propertyName': $("#directBookingAddProperty").children(':selected').text(),
							'guestName': $("#directBookingAddGuestName").val(),
							'guestMobile': $("#directBookingAddGuestMobile").val(),
							'guestEmail': $("#directBookingAddGuestEmail").val(),
							'guestCheckIn': $("#directBookingAddCheckIn").prop('date'),
							'guestCheckOut': $("#directBookingAddCheckOut").prop('date'),
							'invoiced': invoice,
							'nightlyRate': $("#directBookingNightlyRate").val(),
							'cleanUp': cleanUp,
							'notes': $("#directBookingAddNotes").val()
						});

						// Clear inputs
						$("#directBookingAddGuestName").val('');
						$("#directBookingAddGuestMobile").val('');
						$("#directBookingAddGuestEmail").val('');
						$("#directBookingAddCheckIn").val('');
						$("#directBookingAddCheckOut").val('');
						$("#directBookingAddInvoice").prop('checked', false);
						$("#directBookingAddNotes").val('');
						$("#directBookingNightlyRate").val('');
					} else {
						displayMessage('error', 'Something went wrong added the booking. The web admin has been notified and will fix the problem as soon as possible.');
					}
				}).fail(function (request, textStatus, errorThrown) {
					//displayMessage('error', "Error: Something went wrong with addproperty AJAX POST");
				});
			} else {
				displayMessage('warning', valid[1]);
			}
		}
	});

	hideAllContainers();
	$("div#directBooking").show();
}

function areDirectBookingInputsValid() {
	if ($("#directBookingAddGuestName").val() === '') {
		return [false, "Please enter the guests name"];
	}

	if ($("#directBookingAddGuestEmail").val() === '') {
		return [false, "Please enter the guests email"];
	}

	if ($("#directBooking input[type='hidden']:eq(0)").val() === '') {
		return [false, "Please enter the check-in date"];
	}

	if ($("#directBooking input[type='hidden']:eq(1)").val() === '') {
		return [false, "Please enter the check-out date"];
	}

	if ($("#directBookingAddInvoiceYes").prop('checked') && $("#directBookingNightlyRate").val() === '') {
		return [false, "Please enter the nightly rate"];
	}

	return [true];
}

function setDisabledDates() {
	var propertyID = $("select#directBookingAddProperty").children(":selected").val();

	$.get("./php/bookings/getbookings.php", {
		propertyID: propertyID
	}, function(response) {
		var disabledDates = [];

		response.forEach(function (booking) {
			if (booking.availability === 'booked') {
				var date = booking.date;
				disabledDates.push([date.substr(0, 4), date.substr(5, 2) - 1, date.substr(8, 2)]);
			}
		});

		// Enable all dates then disable the dates in disabledDates array
		$('#directBookingAddCheckIn').pickadate().pickadate('picker').set('enable', true);
		$('#directBookingAddCheckIn').pickadate().pickadate('picker').set('disable', disabledDates);

		$('#directBookingAddCheckOut').pickadate().pickadate('picker').set('enable', true);
		$('#directBookingAddCheckOut').pickadate().pickadate('picker').set('disable', disabledDates);

	}, 'json').fail(function (request, textStatus, errorThrown) {
		//displayMessage('error', "Error: Something went wrong with  AJAX POST");
	});
}

function deleteBooking(id) {
	$.post("./php/directbooking/deletebooking.php", {
		bookingID: id
	}, function(response) {
		if (response === 'success') {
			displayMessage('info', 'Booking has been deleted');
			location.reload();
		} else {
			displayMessage('error', 'There was a problem deleting the booking. The web admin has been notified and will fix the problem as soon as possible');
		}
	}).fail(function (request, textStatus, errorThrown) {
		//displayMessage('error', "Error: Something went wrong with  AJAX POST");
	});
}

function isDirectBooking() {
	return $("nav .directBooking").hasClass("active");
}

function setDateInput(date) {
	$("#directBookingCalendarContainer").hide();
	if(currentDateInput === 'checkin') {
		$("#directBookingAddCheckIn").val(moment(date).format("ddd Do MMM YYYY"));
		$("#directBookingAddCheckIn").prop('date', date);
	} else {
		$("#directBookingAddCheckOut").val(moment(date).format("ddd Do MMM YYYY"));
		$("#directBookingAddCheckOut").prop('date', date);
	}
}

function admin() {
	$("div#headerTitle").text("Admin");
	$("nav .active").removeClass("active");
	$("nav .admin").addClass("active");

	// Empty table to prevents duplicates
	$("#userTable tbody").empty();

	userList.forEach(function(value, index, array) {
		var propertyString = '';
		value.properties.forEach(function (propName) {
			propertyString += propName.name + "<br />";
		});

		var html = '';

		html += "<tr>";
		html += "    <td>" + value.firstName + " " + value.lastName + "</td>";
		html += "    <td>" + value.username + "</td>";
		html += "    <td>" + propertyString + "</td>";
		html += "    <td>" + (value.lastLogin !== '' ? moment(value.lastLogin).format("ddd Do MMM YYYY h:mm a") : "Hasn't logged in yet") + "</td>";
		html += "    <td>";
		html += "        <select class='status " + value.customerID + "'>";
		html += "            <option value='active'>Active</option>";
		html += "            <option value='retired'>Retired</option>";
		html += "            <option value='proposal'>Proposal</option>";
		html += "        </select>";
		html += "    </td>";
		html += "    <td><img class='sendWelcomeEmail' src='images/envelope.png' alt='' onclick='sendWelcomeEmail(\"" + value.username + "\")' /></td>";
		html += "    <td><img class='deleteCustomer' src='images/delete.png' alt='' onclick='deleteCustomer(\"" + value.username + "\")' /></td>";
		html += "</tr>";

		$("#userTable tbody").append(html);

		$(".status." + value.customerID).val(value.status);
	});

	$("select.status").on({
		change: function () {
			$.post("./php/customer/changestatus.php", {
				customerID: this.classList[1],
				status: $(this).val()
			}, function(response) {
				if (response === 'success') {
					displayMessage('info', 'Status has been changed');
				} else {
					displayMessage('error', 'Error creating changing the users status. The web admin has been notified and will fix the problem as soon as possible.');
				}
			}).fail(function (request, textStatus, errorThrown) {
				//displayMessage('error', "Error: Something went wrong with  AJAX POST");
			});
		}
	});

	$.get("./php/contractexpiryemailtime/getcontractexpiryemailtime.php", {
	}, function(response) {
		$("#adminContractExpiryEmailTime").val(response);
	});

	hideAllContainers();
	$("div#admin").show();
}

function sendWelcomeEmail(username) {
	$.post("./php/customer/sendwelcomeemail.php", {
		username: username
	}, function(response) {
		if (response === 'success') {
			displayMessage('info', 'The welcome email has been sent');
		} else {
			displayMessage('error', 'Error sending the welcome email. The web admin has been notified and will fix the problem as soon as possible.');
		}
	}).fail(function (request, textStatus, errorThrown) {
		//displayMessage('error', "Error: Something went wrong with  AJAX POST");
	});
}

function deleteCustomer(username) {
	if (confirm("Click Ok to delete the customer")) {
		$.post("./php/customer/deletecustomer.php", {
			username: username
		}, function(response) {
			if (response === 'success') {
				displayMessage('info', 'The client has been deleted');
				location.reload();
			} else {
				displayMessage('error', 'Error deleting the client. The web admin has been notified and will fix the problem as soon as possible.');
			}
		}).fail(function (request, textStatus, errorThrown) {
			//displayMessage('error', "Error: Something went wrong with  AJAX POST");
		});
	}
}

function saveContractExpiryEmailTime() {
	$.post("./php/contractexpiryemailtime/savecontractexpiryemailtime.php", {
		months: $("#adminContractExpiryEmailTime").val()
	}, function(response) {
		if (response == 'success') {
			displayMessage('info', 'Saved successfully');
		} else {
			displayMessage('error', 'Error saving');
		}
	}).fail(function (request, textStatus, errorThrown) {
		//displayMessage('error', "Error: Something went wrong with  AJAX POST");
	});
}

// Add click event to display property subpage
function addPropertySubpageEvent() {
	$("#properties #propertyTable tr, #properties #propertyMobile tr").on({
		click: function () {
			var propertyID = $(this).children("td:nth-child(2)").text();
			sessionStorage.propertySubpage = propertyID;
			// get property name from users property list and chnage hash to property name
			userPropertyList.forEach(function (value) {
				if (value.propertyID === propertyID) {
					window.location.hash = "#" + value.name;
				}
			});
		}
	});
}

// Add property image change event to image on property subpage
function addPropertySubpageImageEvent() {
	// Remove click event added above so the subpage isn't loaded when img is clicked then add show modal to allow users to choose another image.
	$("#propertySubpage table#propertySubpageImageAndButton img.propertyImage").on({
		click: function () {
			currentProperty = $("#propertySubpage #propertySubpagePropertyID").text();

			$("#newPropertyImageContainer").modal({
				fadeDuration: 250
			});

			$("#newPropertyImageButton").on({
				click: function () {
					var imageURL = "http://owners.hostkeep.com.au/images/properties/" + $("#newPropertyImageDropzone").find(".dz-filename:first > *").text();

					$.modal.close();
					$.post("./php/properties/changepropertyimage.php", {
						propertyID: currentProperty,
						imageURL: imageURL
					}, function(response) {
						if (response === 'success') {
							displayMessage('info', 'The property image has been changed');
							$("#propertySubpage table#propertySubpageImageAndButton img.propertyImage").prop("src", imageURL);
						} else {
							displayMessage('error', 'There was a problem changing the property image.');
						}
					}).fail(function (request, textStatus, errorThrown) {
						//displayMessage('error', "Error: Something went wrong with  AJAX POST");
					});
				}
			});
		}
	});
}

// Add focus and blur events to the contenteditable elements
function addDocumentChangeEvent() {
	$("#documents [contenteditable=true]").on({
		blur: function() {
			if ($(this).text !== sessionStorage.contenteditable) {
				var column = this.classList[0];
				$.post("./php/documents/changedocumentinfo.php", {
					documentID: $(this).parent().children(':nth-child(7)').text(),
					column: column,
					value: $(this).text()
				}, function(response) {
					if (response === 'success') {
						displayMessage('info', 'The document ' + column + ' has been updated');
					} else {
						displayMessage('error', 'Something went wrong updating the document ' + column);
					}
				}).fail(function (request, textStatus, errorThrown) {
					//displayMessage('error', "Error: Something went wrong with  AJAX POST");
				});
			}
		},

		focus: function () {
			sessionStorage.contenteditable = $(this).text();
		}
	});

}

// Create a new customer when admin clicks 'Create New Customer' button in admin section
function createNewUser() {
	$.post("./php/customer/createnewcustomeradmin.php", {
		username: $("#adminNewCustomerUsername").val(),
		firstName: $("#adminNewCustomerFirstName").val(),
		lastName: $("#adminNewCustomerLastName").val()
	}, function(response) {
		if (response.substr(0, 7) === 'success') {
			displayMessage('info', 'Customer has been created');

			// Add new client to table
			var html = '';

			html += "<tr>";
			html += "    <td>" + $("#adminNewCustomerFirstName").val() + " " + $("#adminNewCustomerLastName").val() + "</td>";
			html += "    <td>" + $("#adminNewCustomerUsername").val() + "</td>";
			html += "    <td></td>";
			html += "    <td>Hasn't logged in yet</td>";
			html += "    <td>";
			html += "        <select class='status " + response.substr(7) + "'>";
			html += "            <option value='active'>Active</option>";
			html += "            <option value='retired'>Retired</option>";
			html += "            <option value='proposal' selected>Proposal</option>";
			html += "        </select>";
			html += "    </td>";
			html += "    <td><img class='sendWelcomeEmail' src='images/envelope.png' alt='' onclick='sendWelcomeEmail(\"" + $("#adminNewCustomerUsername").val() + "\")' /></td>";
			html += "    <td><img class='deleteCustomer' src='images/delete.png' alt='' onclick='deleteCustomer(\"" + $("#adminNewCustomerUsername").val() + "\")' /></td>";
			html += "</tr>";

			$("#userTable tbody").append(html);
		} else if (response === 'alreadyexists') {
			displayMessage('error', 'There is already a customer with that email');
		} else {
			displayMessage('error', 'Error creating new customer. The web admin has been notified and will fix the problem as soon as possible.');
		}
	}).fail(function (request, textStatus, errorThrown) {
		//displayMessage('error', "Error: Something went wrong with  AJAX POST");
	});
}

function createCalendar() {
	var daysInMonth = [31, currentYear % 4 === 0 ? 29 : 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31];
	var monthNames = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];
	var shortMonthNames = ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];
	var weekDay = ["Sun", "Mon", "Tues", "Wed", "Thu", "Fri", "Sat"];

	var currentDate = moment({day: 1, month: currentMonth, year: currentYear}).subtract(moment({day: 1, month: currentMonth, year: currentYear}).day(), 'days');
	var lastOfMonth = moment({day: 1, month: (currentMonth + 1) % 12, year: currentYear + (currentMonth === 11 ? 1 : 0)}).subtract(1, 'days');
	var monthName = monthNames[currentMonth];

	var numDays = 0;

	var html = "";
	html += "<div>";
	html += "    <table id='bookingCalendar'>";
	html += "        <tr>";
	html += "            <th colspan='7'>";
	html += "                <img src='./images/calendar-previous.png' alt='Previous Month' onclick='showPreviousMonth()'>";
	html += "                <span>    " + monthName + " " + currentYear + "    </span>";
	html += "                <img src='./images/calendar-next.png' alt='Next Month' onclick='showNextMonth()'>";
	html += "            </th>";
	html += "        </tr>";
	html += "    </table>";
	html += "    <ul class='reservationCalendar'>";

	weekDay.forEach(function (day) {
		html += "    <li class='tile dayContainer'>";
		html += "        <div class='days'>" + day + "</div>";
		html += "    </li>";
	});

	while (lastOfMonth.diff(currentDate) >= 0 || (numDays % 7 !== 0 && lastOfMonth.diff(currentDate) < 0)) {
		html += "    <li class='tile dateContainer " + currentDate.format('YYYY-MM-DD') + "'>";
		html += "        <div class='date'>" + (currentDate.date() === 1 ? shortMonthNames[currentDate.month()] + " " : "") + currentDate.date() + "</div>";
		html += "    </li>";

		currentDate.add(1, 'days');
		numDays += 1;
	}
	html += "    </ul>";
	html += "</div>";

	$(calendarContainer).html(html);

	if (currentAirbnbID !== '') {
		addInfoToCalendar();
	}
}

function addInfoToCalendar() {
	$.get("./php/bookings/getreservations.php", {
		airbnbID: currentAirbnbID,
		propertyID: currentPropertyID
	}, function(response) {
		if (response.substr(0, 4) !== 'fail') {
			var endMarker = "<div class='reservationMarker reservationEndMarker'></div>";

			response = JSON.parse(response);
			var reservations = response.reservations;
			var bookings = response.bookings;
			var directBookings = response.directBookings;
			reservations.forEach(function (res) {
				var startMarker = "";
				startMarker += "<div class='reservationMarker reservationStartMarker'>";
				startMarker += "    <img src='" + res.guestThumbnail + "' alt='image of the guest' class='reservationGuestThumbnail'>";
				startMarker += "</div>";

				var currentDate = moment(res.startDate);
				var endDate = moment(res.endDate);
				var df = "YYYY-MM-DD";

				while (currentDate.diff(endDate) <= 0) {
					// If the start of the reservation starts on a Saturday, put the
					// image and text on the next line.
					if (currentDate.diff(moment(res.startDate)) === 0) {
						// Start
						$('.' + currentDate.format(df)).append(startMarker);
					} else if (currentDate.diff(endDate) === 0) {
						// End
						$('.' + currentDate.format(df)).append(endMarker);
					} else {
						// Middle
						var middleMarker = "";
						middleMarker += "<div class='reservationMarker reservationMiddleMarker'>";
						// If yesterday was the checkin day, add the guests name
						// to marker
						if (moment(currentDate).subtract(1, 'days').diff(moment(res.startDate)) === 0) {
							middleMarker += "<div class='guestName'>" + res.guestFirstName + " - $" + res.netCost + "</div>";
						}
						middleMarker += "</div>";
						$('.' + currentDate.format(df)).append(middleMarker);
					}

					currentDate.add(1, 'days');
				}
			});

			// var middleBookingMarker = "<div class='reservationMarker reservationMiddleMarker blockedDate'></div>";
			var endBookingMarker = "<div class='reservationMarker reservationEndMarker blockedDate'></div>";
			var db = isDirectBooking();

			directBookings.forEach(function (booking, index) {
				var startBookingMarker = "";
				startBookingMarker += "<div class='reservationMarker reservationStartMarker blockedDate'>";
				startBookingMarker += "    <div class='reservationGuestInfo'>";
				startBookingMarker += "        <img src='./images/booking-calendar.png' alt='calendar icon' class='reservationGuestThumbnail'>";
				startBookingMarker += "    </div>";
				startBookingMarker += "</div>";

				var startDate = moment(booking.guestCheckIn);
				var currentDate = moment(booking.guestCheckIn);
				var endDate = moment(booking.guestCheckOut);

				while (endDate.diff(currentDate) >= 0) {
					var date = currentDate.format("YYYY-MM-DD");
					if (startDate.diff(currentDate) === 0) {
						$('.' + date).append(startBookingMarker);
					} else if (endDate.diff(currentDate) === 0) {
						$('.' + date).append(endBookingMarker);
					} else {
						var middleBookingMarker = "";
						middleBookingMarker += "<div class='reservationMarker reservationMiddleMarker blockedDate'>";
						// If yesterday was the checkin day, add the guests name
						// to marker
						if (moment(currentDate).subtract(1, 'days').diff(moment(startDate)) === 0) {
							middleBookingMarker += "<span>Direct</span>";
						}
						middleBookingMarker += "</div>";
						$('.' + date).append(middleBookingMarker);
					}

					currentDate.add(1, 'day');
				}


				// if (booking.type === 'busy') {
				// if (index === bookings.length - 1) {
				// 	// end
				// 	$('.' + booking.date).append(endBookingMarker);
				// } else if (index === 0 || (bookings[index - 1].type !== 'busy' && bookings[index + 1].type === 'busy')) {
				// 	// start
				// 	$('.' + booking.date).append(startBookingMarker);
				// } else if (bookings[index - 1].type === 'busy') {
				// 	// middle
				// 	$('.' + booking.date).append(middleBookingMarker);
				// }
				// } else if (index !== 0 && bookings[index - 1].type === 'busy') {
				// 	$('.' + booking.date).append(endBookingMarker);
				// }
			});

			bookings.forEach(function (booking, index) {
				if ($("." + booking.date + " .reservationMiddleMarker").length === 0) {
					if ($("." + booking.date + " .reservationStartMarker").length === 0) {
						var html = "";
						html += "<div class='bookingPrices'>";
						html += "    <span>$" + booking.price + "</span>";
						html += "</div>";
						$('.' + booking.date).append(html);
					}

					if (db) {
						var date = booking.date;
						$('.' + booking.date).on({
							click: function () {
								setDateInput(date);
							}
						});
					}
				}
			});
		} else {
			displayMessage('error', 'Error getting the bookings for this property');
		}
	}).fail(function (request, textStatus, errorThrown) {
		//displayMessage('error', "Error: Something went wrong with  AJAX GET");
	});
}

function showPreviousMonth() {
	if (currentMonth === 0) {
		currentMonth = 11;
		currentYear -= 1;
	} else {
		currentMonth -= 1;
	}

	createCalendar();
}

function showNextMonth() {
	if (currentMonth === 11) {
		currentMonth = 0;
		currentYear += 1;
	} else {
		currentMonth += 1;
	}

	createCalendar();
}
