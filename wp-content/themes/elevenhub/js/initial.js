var loading = "<div id='loader' class='animated rubberBand infinite'></div>";

jQuery( document ).ready(function(){
	jQuery( "#login-form-controller" ).on("click", function(){
		if ( jQuery( "#login-form-holder" ).length ) { destroyLoginForm(); }
		else { buildLoginForm(); }
	});
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
			</div>\
			<div class='popup-separator'>\
				or register\
			</div>\
			<div id='register' class='mb-1em'>\
				<input type='text' placeholder='First name' id='first-name' class='small-fat' onkeydown='keyPressedForms(event, 1);'>\
				<input type='text' placeholder='Last name' id='last-name' class='small-fat' onkeydown='keyPressedForms(event, 1);'>\
				<input type='email' placeholder='Email' id='email-registration' class='wide-fat' onkeydown='keyPressedForms(event, 1);'>\
				<input type='password' placeholder='Password' id='password-registration' class='wide-fat' onkeydown='keyPressedForms(event, 1);'>\
				<button id='register-controller' class='green-bold-button form-button' onclick='registerUser();' onkeydown='keyPressedForms(event, 1);'>Register</button>\
			</div>\
		</div>\
	</div>\
	";

	buildElement( build_ );

	jQuery( "#login-form-holder" ).on("click", function( e ){ if( e.target == this ){ destroyLoginForm(); } });
}
function destroyLoginForm() {
	jQuery( "#login-form" ).removeClass( "bounceIn" ).addClass( "bounceOut" );
	jQuery( "#login-form-holder" ).removeClass( "fadeIn" ).addClass( "fadeOut" );
	setTimeout(function(){ jQuery( "#login-form-holder" ).remove(); }, 750);
}


function buildElement( markup ) { jQuery( "body" ).append( markup ); }


function registerUser() {
	//Add loading sign
	jQuery( "#login-form-holder #register" ).append( loading );

	first_name = jQuery( "#first-name" ).val().trim();
	last_name = jQuery( "#last-name" ).val().trim();
	email = jQuery( "#email-registration" ).val().trim();
	password = jQuery( "#password-registration" ).val().trim();

	jQuery.ajax({
		url : ajax_url,
		type : 'post',
		data : {
			action : "register_user",
			first_name : first_name,
			last_name : last_name,
			email : email,
			password : password
		},
		success : function( response ) {
			//Remove loading sign
			jQuery( "#login-form-holder #register #loader" ).remove();

			response = response == "" ? "Welcome to the <span style='color: #e74c3c;'>hub</span>!<br/>You can now login :-)" : response;
			alert_box = "<div id='alert-box' class='animated bounceInDown'>"+ response +"<button id='close-popup-button' onclick='removeAlertBox();'>Close</button></div>";
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
			} else { window.location.reload( true ); }
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
