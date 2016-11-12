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
				<input type='email' placeholder='Email' id='email' class='wide-fat'>\
				<input type='password' placeholder='Password' id='password' class='wide-fat'>\
				<button id='login-controller' class='red-bold-button form-button'>Login</button>\
			</div>\
			<div class='popup-separator'>\
				or register\
			</div>\
			<div id='register' class='mb-1em'>\
				<input type='text' placeholder='First name' id='first-name' class='small-fat'>\
				<input type='text' placeholder='Last name' id='last-name' class='small-fat'>\
				<input type='email' placeholder='Email' id='email-registration' class='wide-fat'>\
				<input type='password' placeholder='Password' id='password-registration' class='wide-fat'>\
				<button id='login-controller' class='green-bold-button form-button' onclick='registerUser();'>Register</button>\
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
			console.log( response );
		}
	});
}
