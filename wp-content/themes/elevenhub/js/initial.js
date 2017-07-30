var loading = "<div id='loader' class='animated rubberBand infinite'></div>";

jQuery( document ).ready(function(){
	jQuery( "#login-form-controller" ).on("click", function(){
		if ( jQuery( "#login-form-holder" ).length ) { destroyLoginForm(); }
		else { buildLoginForm(); }
	});

	jQuery( ".login-anchor" ).each( function(){
		jQuery( this ).on( "click", function(){
			buildLoginForm();
		} );
	} );

	if ( jQuery( "#login-form-controller" ).length > 0 && window.location.href.indexOf( "#login" ) > -1 ) { buildLoginForm(); }
});

function buildLoginForm() {
	build_ = "\
	<div id='login-form-holder' class='popup-holder animated fadeIn'>\
		<div id='login-form' class='popup-inner animated bounceIn'>\
			<button id='close-button' class='close-button fa fa-close' onclick='destroyLoginForm();'></button>\
			<div id='login'>\
				<input type='email' placeholder='Email' id='email-login' class='wide-fat' onkeydown='keyPressedForms(event, 0);'>\
				<input type='password' placeholder='Password' id='password-login' class='wide-fat' onkeydown='keyPressedForms(event, 0);'>\
				<button id='login-controller' class='red-bold-button form-button' onclick='signOnUser();' onkeydown='keyPressedForms(event, 0);'>Login</button>\
				<button id='forgotten-password-controller' class='simple-button' onclick='askForEmail();'>Forgotten password?</button>\
			</div>\
			<div class='popup-separator'>\
				or register\
			</div>\
			<div id='register' class='mb-1em'>\
				<input type='text' placeholder='First name' id='first-name' class='small-fat' onkeydown='keyPressedForms(event, 1);'>\
				<input type='text' placeholder='Last name' id='last-name' class='small-fat' onkeydown='keyPressedForms(event, 1);'>\
				<input type='email' placeholder='Email' id='email-registration' class='wide-fat' onkeydown='keyPressedForms(event, 1);'>\
				<input type='password' placeholder='Password' id='password-registration' class='wide-fat' onkeydown='keyPressedForms(event, 1);'>\
				<div id='login-captcha' class='g-recaptcha' data-sitekey='6LcbrioUAAAAAEgA9LAbeaK_TQHOKVWhd8QZeXrI'></div>\
				<button id='register-controller' class='green-bold-button form-button' onclick='registerUser();' onkeydown='keyPressedForms(event, 1);'>Register</button>\
			</div>\
		</div>\
	</div>\
	";

	buildElement( build_ );

	// Render the reCaptcha
	grecaptcha.render(
		"login-captcha",
		{
			"sitekey" : "6LcbrioUAAAAAEgA9LAbeaK_TQHOKVWhd8QZeXrI",
			"theme" : "light"
		}
	);

	jQuery( "#login-form-holder" ).on("click", function( e ){ if( e.target == this ){ destroyLoginForm(); } });
}
function destroyLoginForm() {
	jQuery( "#login-form" ).removeClass( "bounceIn" ).addClass( "bounceOut" );
	jQuery( "#login-form-holder" ).removeClass( "fadeIn" ).addClass( "fadeOut" );
	setTimeout(function(){ jQuery( "#login-form-holder" ).remove(); }, 750);
}

function buildElement( markup ) { jQuery( "body" ).append( markup ); }

function askForEmail() {
	alert_box = "\
	<div id='alert-box' class='animated bounceInDown'>\
		<button id='close-button' class='close-button fa fa-close' onclick='removeAlertBox();'></button>\
		<label for='email'>Enter your email</label>\
		<input id='email' type='email' onkeydown='keyPressedForms(event, 2);'>\
		<button id='submit-button' class='green-bold-button'>Save</button>\
	</div>";
	jQuery( "#login-form-holder" ).append( alert_box );
	jQuery( "#login-form-holder #alert-box #submit-button" ).on("click", function(){ resetUserPassword(); });
}

function resetUserPassword() {
	jQuery( "#login-form-holder #alert-box" ).append( loading );

	email = jQuery( "#login-form-holder #alert-box #email" ).val().trim();

	jQuery.ajax({
		url : ajax_url,
		type : 'post',
		data : {
			action : "reset_user_password",
			email : email
		},
		success : function ( response ) {
			jQuery( "#login-form-holder #alert-box #loader" ).remove();

			if ( response == "READY" ) {
				alert_box = "<div id='alert-box' class='animated bounceInDown'>Check your email!<button id='close-popup-button' onclick='removeAlertBox();'>Close</button></div>";
				jQuery( "#login-form-holder" ).append( alert_box );
			}
			else if ( response == "There aren't users with that email." ) {
				alert_box = "<div id='alert-box' class='animated bounceInDown'>"+ response +"<button id='close-popup-button' onclick='removeAlertBox();'>Close</button></div>";
				jQuery( "#login-form-holder" ).append( alert_box );
			}
			else { console.log( response ); }
		}
	});
}

function registerUser() {
	//Add loading sign
	jQuery( "#login-form-holder #register" ).append( loading );

	first_name = jQuery( "#first-name" ).val().trim();
	last_name = jQuery( "#last-name" ).val().trim();
	email = jQuery( "#email-registration" ).val().trim();
	password = jQuery( "#password-registration" ).val().trim();
	captcha = grecaptcha.getResponse();

	jQuery.ajax({
		url : ajax_url,
		type : 'post',
		data : {
			action : "register_user",
			first_name : first_name,
			last_name : last_name,
			email : email,
			password : password,
			captcha : captcha
		},
		success : function( response ) {
			//Remove loading sign
			jQuery( "#login-form-holder #register #loader" ).remove();

			buttonEvent = "removeAlertBox();";
			buttonText = "Close";

			if ( jQuery( "body" ).hasClass( "page-template-page-join" ) ) {
				buttonEvent = response == "" ? "window.location=\""+ window.location.origin +"/#login\"" : "removeAlertBox();";
				buttonText = response == "" ? "Login" : "Close";

			}

			response = response == "" ? "Welcome to the <span style='color: #e74c3c;'>hub</span>!<br/>You can now login :-)" : response;
			alert_box = "<div id='alert-box' class='animated bounceInDown'>"+ response +"<button id='close-popup-button' onclick='"+ buttonEvent +"'>"+ buttonText +"</button></div>";
			jQuery( "#login-form-holder" ).append( alert_box );
		}
	});
}

function signOnUser() {
	//Add loading sign
	jQuery( "#login-form-holder #login" ).append( loading );

	email = jQuery( "#email-login" ).val().trim();
	password = jQuery( "#password-login" ).val().trim();

	jQuery.ajax({
		url : ajax_url,
		type : 'post',
		data : {
			action : "login_user",
			email : email,
			password : password
		},
		success : function( response ) {
			//Remove loading sign
			jQuery( "#login-form-holder #login #loader" ).remove();

			if ( response != "" ) {
				alert_box = "<div id='alert-box' class='animated bounceInDown'>"+ response +"<button id='close-popup-button' onclick='removeAlertBox();'>Close</button></div>";
				jQuery( "#login-form-holder" ).append( alert_box );
			} else { window.location = window.location.origin; }
		}
	});
}

function logOutUser() {
	jQuery.ajax({
		url : ajax_url,
		type : 'post',
		data : {
			action : "logout_user"
		},
		success : function( response ) {
			if ( response != "" ) { console.log( response ); } else { window.location.href = "http://"+ window.location.hostname; }
		}
	});
}

function removeAlertBox() {
	jQuery( "#alert-box" ).removeClass( "bounceInUp" ).addClass( "bounceOutUp" );
	setTimeout( function(){ jQuery( "#alert-box" ).remove(); }, 750 );
}

function keyPressedForms( e, form ) {
	if ( e.keyCode == 13 ) {
		if ( form == 0 ) { signOnUser(); }
		else if ( form == 1 ) { registerUser(); }
		else if ( form == 2 ) { resetUserPassword(); }
	}
	else if ( e.keyCode == 27 ) { destroyLoginForm(); }
}


function chooseProfileAssociation( type ) {
	jQuery.ajax({
		url : ajax_url,
		type : 'post',
		data : {
			action : "add_profile_association",
			type : type
		},
		success : function( response ) {
			window.location.reload( true );
		}
	});
}
