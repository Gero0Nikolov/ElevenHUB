/*
*	Method used to initialize buttons on document load.
*/
jQuery( document ).ready(function(){
	jQuery( "[rel='logout']" ).each(function(){ jQuery( this ).on("click", function(){ logOutUser(); }); });

	if ( jQuery( "#menu-controller" ).length ) {
		jQuery( "#menu-controller" ).on("click", function(){
			if ( jQuery( "#mobile-menu-holder" ).hasClass( "slideInDown" ) ) { jQuery( "#mobile-menu-holder" ).removeClass( "slideInDown" ).addClass( "slideOutUp" ); }
			else { jQuery( "#mobile-menu-holder" ).removeClass( "slideOutUp" ).addClass( "slideInDown" ); }
		});
	}

	if ( jQuery( "#follow-controller" ).length ) { jQuery( "#follow-controller" ).on("click", function(){ buildUserRelation( jQuery( this ) ); }); }

	/*** HEADER CONTROLLERS ***/
	jQuery( "#notifications-controller" ).on("click", function(){
		if ( jQuery( "#notifications-holder" ).hasClass( "fadeInDown" ) ) { jQuery( "#notifications-holder" ).css( "transform", "initial" ).removeClass( "fadeInDown" ).addClass( "fadeOutUp" ); }
		else { jQuery( "#notifications-holder" ).removeClass( "fadeOutUp" ).addClass( "fadeInDown" ) }
	});

	/** BUILD NOTIFICATIONS & START TO LIVE PULL NOTIFICATIONS **/
	buildAndPullUserNotifications( jQuery( "#notifications-holder" ) );
});

/*
*	Method used to control user images uploading from TYPE: Profile media [ Banner IMG, Avatar IMG ]
*/
function openProfileImages() {
	mediaController = new UserMedia();
	mediaController.BuildMediaView( true );
}

/*
*	Method used to build user relations from TYPE: FOLLOW or UNFOLLOW
*/
function buildUserRelation( container ) {
	relationsController = new UserRelations( vUserID );
	relationsController.followOrUnfollowRelation( vUserID, true, function( response ){
		response = JSON.parse( response );

		if (  typeof( response ) === 'object' ) {
			actionResult = response.action_result;
			followersText = response.followers.length == 1 ? response.followers.length + " follower" : response.followers.length + " followers" ;

			if ( jQuery( "#user-container .user-meta .followers" ).length ) { jQuery( "#user-container .user-meta .followers" ).html( followersText ); }
		} else { actionResult = response };

		if ( actionResult == "followed" ) {
			container.removeClass( "follow-button" ).addClass( "unfollow-button" ).html( "Unfollow" );
		}
		else if ( actionResult == "unfollowed" ) {
			container.removeClass( "unfollow-button" ).addClass( "follow-button" ).html( "Follow" );
		}
	} );
}

/*
*	Method used to first pull user notifications & listed for new notifications during the browsing.
*/
function buildAndPullUserNotifications( container ) {
	notificationsController = new UserNotifications();
	notificationsController.getUserNotifications( "", function( response ) {
		for ( notification_key in response ) {
			notification = response[ notification_key ];

			notification_not_viewed_class = notification.notification_viewed == 0 ? "unopened-notification" : "";

			build = "\
			<a href='"+ notification.notification_body.notification_link +"' id='notification-anchor-"+ notification.row_id +"' class='notification-anchor "+ notification_not_viewed_class +"'>\
				<div class='notification-holder'>\
					<div class='user-avatar' style='background-image: url(\""+ notification.notification_body.notifier_avatar_url +"\");'></div>\
					<div class='notification-content'>"+ notification.notification_body.notification_text +"</div>\
					<div class='notification-meta'>\
						<span class='notification-icon fa "+ notification.notification_body.notification_icon +"' style='background-color: "+ notification.notification_body.notification_icon_background +";'></span>\
						<span class='notification-date'>"+ notification.notification_date +"</span>\
					</div>\
				</div>\
			</a>\
			";

			container.prepend( build );
		}
	} );
}
