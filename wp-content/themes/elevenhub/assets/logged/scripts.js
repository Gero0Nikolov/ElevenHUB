jQuery( document ).ready(function(){
	jQuery( "[rel='logout']" ).each(function(){ jQuery( this ).on("click", function(){ logOutUser(); }); });

	jQuery( "#menu-controller" ).on("click", function(){
		if ( jQuery( "#mobile-menu-holder" ).hasClass( "slideInDown" ) ) { jQuery( "#mobile-menu-holder" ).removeClass( "slideInDown" ).addClass( "slideOutUp" ); }
		else { jQuery( "#mobile-menu-holder" ).removeClass( "slideOutUp" ).addClass( "slideInDown" ); }
	});
});

function openProfileImages() {
	mediaController = new UserMedia();
	mediaController.BuildMediaView( true );
}
