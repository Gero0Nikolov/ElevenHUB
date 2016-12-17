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

	if ( jQuery( "#followers-controller" ).length ) { jQuery( "#followers-controller" ).on("click", function(){ openUserRelationStatistics(); }); }
	if ( jQuery( "#follow-controller" ).length ) { jQuery( "#follow-controller" ).on("click", function(){ buildUserRelation( jQuery( this ) ); }); }

	/*** HEADER CONTROLLERS ***/
	jQuery( "#notifications-controller" ).on("click", function(){
		if ( jQuery( "#notifications-holder" ).hasClass( "fadeInDown" ) ) { jQuery( "#notifications-holder" ).css( "transform", "initial" ).removeClass( "fadeInDown" ).addClass( "fadeOutUp" ); }
		else { jQuery( "#notifications-holder" ).removeClass( "fadeOutUp" ).addClass( "fadeInDown" ) }
	});

	/** BUILD NOTIFICATIONS & START TO LIVE PULL NOTIFICATIONS **/
	buildAndPullUserNotifications( jQuery( "#notifications-holder" ) );

	/* BODY CONTROLLERS */
	if ( jQuery( "#user-meta-container" ).length ) {
		jQuery( "#user-meta-container #save-user-meta" ).on("click", function(){
			password_promt_view = "\
			<div id='media-popup-container' class='popup-container animated fadeIn'>\
				<div id='media-popup-fields' class='popup-inner-container'>\
					<button id='close-button' class='close-button fa fa-close'></button>\
					<label for='password'>Enter your current password</label>\
					<input id='current-password' type='password' onkeydown='keyPressedForms(event, 2);'>\
					<button id='submit-button' class='green-bold-button'>Save</button>\
				</div>\
			</div>\
			";

			jQuery( "body" ).append( password_promt_view );

			jQuery( "#media-popup-container" ).on("click", function( e ){ if( e.target == this ){ controller = new UserMedia(); controller.destroyMediaPopup(); } });
			jQuery( "#media-popup-container #media-popup-fields #close-button" ).on("click", function(){ controller = new UserMedia(); controller.destroyMediaPopup(); });

			jQuery( "#media-popup-container #media-popup-fields #submit-button" ).on("click", function(){ updateUserMetaSubmit(); });
		});

		jQuery( "#user-meta-container #save-company-meta" ).on("click", function(){
			password_promt_view = "\
			<div id='media-popup-container' class='popup-container animated fadeIn'>\
				<div id='media-popup-fields' class='popup-inner-container'>\
					<button id='close-button' class='close-button fa fa-close'></button>\
					<label for='password'>Enter your current password</label>\
					<input id='current-password' type='password' onkeydown='keyPressedForms(event, 3);'>\
					<button id='submit-button' class='green-bold-button'>Save</button>\
				</div>\
			</div>\
			";

			jQuery( "body" ).append( password_promt_view );

			jQuery( "#media-popup-container" ).on("click", function( e ){ if( e.target == this ){ controller = new UserMedia(); controller.destroyMediaPopup(); } });
			jQuery( "#media-popup-container #media-popup-fields #close-button" ).on("click", function(){ controller = new UserMedia(); controller.destroyMediaPopup(); });

			jQuery( "#media-popup-container #media-popup-fields #submit-button" ).on("click", function(){ updateCompanyMetaSubmit(); });
		});
	}

	if ( jQuery( "body" ).hasClass( "author" ) ) {
		if ( jQuery( "#company-container" ).length ) {
			if ( jQuery( "#company-controls" ).length ) {
				jQuery( "#composer-controller" ).on("click", function(){ openComposer(); });
				jQuery( "#events-controller" ).on("click", function(){});
				jQuery( "#requests-controller" ).on("click", function(){});
			}
		}
	}

	/* MEDIA CONTROLLERS */
	if ( jQuery( "#media-uploader-opener" ).length ) {
		jQuery( "#media-uploader-opener" ).on("click", function(){
			jQuery( "#media-uploader #file-holder" ).trigger( "click" );
		});
		jQuery( "#media-uploader #file-holder" ).on("change", function(){ jQuery( "#media-uploader" ).submit(); });
	}

	if ( jQuery( "#medias-container" ).length ) {
		jQuery( "#medias-container .media-container" ).each(function(){
			jQuery( this ).on("click", function( e ){
				if( e.target == this ) { openMediaHandler( jQuery( this ) ); }
			});

			jQuery( this ).children( "#marker" ).on("click", function( e ){
				if ( e.target == this ) {
					if ( jQuery( this ).hasClass( "marked" ) ) {
						selectedElements.splice( selectedElements.indexOf( jQuery( this ).parent().attr( "id" ) ), 1 );
						jQuery( this ).removeClass( "marked" );
					} else {
						selectedElements.push( jQuery( this ).parent().attr( "id" ) );
						jQuery( this ).addClass( "marked" );
					}
				}
			});
		});
	}

	if ( jQuery( "#load-more-controller" ).length ) {
		jQuery( "#load-more-controller" ).on("click", function(){
			jQuery( "#medias-container" ).append( loading );

			mediaController = new UserMedia();
			mediaController.getUserMedia( companyID, mediaOffset, function( response ){
				jQuery( "#medias-container #loader" ).remove();

				response = JSON.parse( JSON.parse( response ) );

				if ( response != "You don't have any media." ) {
					mediaOffset += 20;
					for ( count = 0; count < response.length; count++ ) {
						if ( response[ count ].TYPE.split( "/" )[0] == "image" ) { view_ = "<div id='media-"+ response[ count ].ID +"' class='media-container animated bounceIn new' style='background-image: url("+ response[ count ].URL +");' media-type='"+ response[ count ].TYPE +"'><button id='marker'></button></div>"; }
						else if ( response[ count ].TYPE.split( "/" )[0] == "video" ) { view_ = "<div id='media-"+ response[ count ].ID +"' class='media-container animated bounceIn new' media-type='"+ response[ count ].TYPE +"'><button id='marker'></button><video autoplay='true' muted='true' loop='true'><source src='"+ response[ count ].URL +"' type='"+ response[ count ].TYPE +"'></video><div class='overlay'></div></div>"; }

						jQuery( "#medias-container" ).append( view_ );
					}

					jQuery( "#medias-container .new" ).each(function(){
						jQuery( this ).on("click", function( e ){
							if( e.target == this ) { openMediaHandler( jQuery( this ) ); }
						});

						jQuery( this ).children( "#marker" ).on("click", function( e ){
							if ( e.target == this ) {
								if ( jQuery( this ).hasClass( "marked" ) ) {
									selectedElements.splice( selectedElements.indexOf( jQuery( this ).parent().attr( "id" ) ), 1 );
									jQuery( this ).removeClass( "marked" );
								} else {
									selectedElements.push( jQuery( this ).parent().attr( "id" ) );
									jQuery( this ).addClass( "marked" );
								}
							}
						});

						jQuery( this ).removeClass( "new" );
					});
				} else {
					jQuery( "#load-more-controller" ).remove();
				}
			} );
		});
	}
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
*	Method used to show user relation statistics
*/
function openUserRelationStatistics() {
	view_ = "\
	<div id='media-popup-container' class='popup-container animated fadeIn'>\
		<div id='media-popup-fields' class='popup-inner-container'>\
			<button id='close-button' class='close-button fa fa-close'></button>\
			"+ loading +"\
		</div>\
	</div>\
	";

	jQuery( "body" ).append( view_ );
	jQuery( "#media-popup-container" ).on("click", function( e ){ if( e.target == this ){ controller = new UserMedia(); controller.destroyMediaPopup(); } });
	jQuery( "#media-popup-container #media-popup-fields #close-button" ).on("click", function(){ controller = new UserMedia(); controller.destroyMediaPopup(); });

	relationsController = new UserRelations( -1 );
	relationsController.getUserRelations( "", function( response ){
		var followers = response.followers;
		var follows = response.follows;

		var count_followers = 0;
		var count_follows = 0;

		var followers_container = "";
		for ( follower_key in followers ) {
			var follower = followers[ follower_key ];

			count_followers += 1;

			followers_container += "\
			<a href="+ follower.user_follower_body.user_url +" id='follower-anchor-"+ follower.row_id +"' class='relation-anchor'>\
				<div class='relation-container'>\
					<div class='user-avatar' style='background-image: url(\""+ follower.user_follower_body.user_avatar_url +"\");'></div>\
					<h1 class='user-names'>"+ follower.user_follower_body.user_first_name +" "+ follower.user_follower_body.user_last_name +"</h1>\
				</div>\
			</a>\
			";
		}

		var follows_container = "";
		for ( follow_key in follows ) {
			var follow = follows[ follow_key ];

			count_follows += 1;

			follows_container += "\
			<a href="+ follow.user_followed_body.user_url +" id='follow-anchor-"+ follow.row_id +"' class='relation-anchor'>\
				<div class='relation-container'>\
					<div class='user-avatar' style='background-image: url(\""+ follow.user_followed_body.user_avatar_url +"\");'></div>\
					<h1 class='user-names'>"+ follow.user_followed_body.user_first_name +" "+ follow.user_followed_body.user_last_name +"</h1>\
				</div>\
			</a>\
			";
		}

		followers_button_text = count_followers != 1 ? count_followers +" followers" : count_followers +" follower";
		follows_button_text = count_follows != 1 ? count_follows +" follows" : count_follows +" followed";

		view_header = "\
		<div id='user-relations-header' class='user-relations-header'>\
			<button id='followers-anchor-controller' class='active relation-anchor-controller peter-river'>"+ followers_button_text +"</button>\
			<span class='bull-separator'>&bull;</span>\
			<button id='follows-anchor-controller' class='relation-anchor-controller turquoise'>"+ follows_button_text +"</button>\
		</div>\
		";

		view_body = "\
		<div id='user-relations-body' class='user-relations-body'>\
			<div id='user-followers-container' class='active user-list'>"+ followers_container +"</div>\
			<div id='user-follows-container' class='user-list'>"+ follows_container +"</div>\
		</div>\
		";

		jQuery( "#media-popup-container #media-popup-fields #loader" ).remove();
		jQuery( "#media-popup-container #media-popup-fields" ).append( view_header ).append( view_body );

		// Add controlls
		jQuery( "#followers-anchor-controller" ).on("click", function(){
			if ( !jQuery( "#user-relations-body #user-followers-container" ).hasClass( "active" ) ) {
				jQuery( "#user-relations-header .active" ).removeClass( "active" ); jQuery( "#user-relations-header #followers-anchor-controller" ).addClass( "active" );
				jQuery( "#user-relations-body .active" ).removeClass( "active" ); jQuery( "#user-relations-body #user-followers-container" ).addClass( "active" );
			}
		});

		jQuery( "#follows-anchor-controller" ).on("click", function(){
			if ( !jQuery( "#user-relations-body #user-follows-container" ).hasClass( "active" ) ) {
				jQuery( "#user-relations-header .active" ).removeClass( "active" ); jQuery( "#user-relations-header #follows-anchor-controller" ).addClass( "active" );
				jQuery( "#user-relations-body .active" ).removeClass( "active" ); jQuery( "#user-relations-body #user-follows-container" ).addClass( "active" );
			}
		});
	} );
}

/*
*	Method used to first pull user notifications & listed for new notifications during the browsing.
*/
function buildAndPullUserNotifications( container ) {
	notificationsController = new UserNotifications();
	notificationsController.getUserNotifications( "", function( response ) {
		var listed_notifications_ids = [];
		var unseen_notifications = 0;

		for ( notification_key in response ) {
			var notification = response[ notification_key ];

			listed_notifications_ids.push( notification.row_id );

			notification_not_viewed_class = "";
			if ( notification.notification_viewed == 0 ) {
				notification_not_viewed_class = "unopened-notification";
				unseen_notifications += 1;

				notification.notification_body.notification_link += "?read_notification="+ notification.row_id;
			}

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

		jQuery( "#notifications-controller .notifications-counter" ).html( unseen_notifications = unseen_notifications > 0 ? "<span class='inline-content'>"+ unseen_notifications +"</span>" : "" );

		listenForNewUserNotifications( "", listed_notifications_ids, container );
	} );
}

/*
*	This method is used to listed for new unseen user notifications & list them.
*/
function listenForNewUserNotifications( userID = "", listed_notifications_ids = [], container ) {
	notificationsController.getUserUnseenNotifications( userID, listed_notifications_ids, function ( response ) {
		var unseen_notifications = 0;

		for ( notification_key in response ) {
			var notification = response[ notification_key ];

			listed_notifications_ids.push( notification.row_id );

			notification_not_viewed_class = "";
			if ( notification.notification_viewed == 0 ) {
				notification_not_viewed_class = "unopened-notification";
				unseen_notifications += 1;

				notification.notification_body.notification_link += "?read_notification="+ notification.row_id;
			}

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

		jQuery( "#notifications-controller .notifications-counter" ).html( unseen_notifications = unseen_notifications > 0 ? "<span class='inline-content'>"+ parseInt( parseInt( unseen_notifications ) + parseInt( current_notifications = isNaN( jQuery( "#notifications-controller .notifications-counter .inline-content" ).html() ) ? 0 : parseInt( jQuery( "#notifications-controller .notifications-counter .inline-content" ).html() ) ) ) +"</span>" : jQuery( "#notifications-controller .notifications-counter" ).html() );

		setTimeout(function(){ listenForNewUserNotifications( userID, listed_notifications_ids, container ); }, 10000);
	} );
}

/*
*	THIS METHOD IS USED TO SUBMIT USER META DATA FROM THE USER SETTINGS PAGE
*/
function updateUserMetaSubmit() {
	jQuery( "#media-popup-fields" ).append( loading );

	userProfileController = new UserMeta();
	userProfileController.updateUserMeta(
		"",
		"#user-meta-container",
		"#media-popup-container #media-popup-fields",
		function( response ) {
			console.log( response );
			if ( response == "updated" ) { window.location.reload( true ); }
			else {
				jQuery( "#media-popup-fields #loader" ).remove();

				alert_box = "<div id='alert-box' class='animated bounceInDown'>"+ response +"<button id='close-popup-button' onclick='removeAlertBox();'>Close</button></div>";
				jQuery( "#media-popup-container" ).append( alert_box );
			}
		}
	);
}

function updateCompanyMetaSubmit() {
	jQuery( "#media-popup-fields" ).append( loading );

	userProfileController = new UserMeta();
	userProfileController.updateCompanyMeta(
		"",
		"#user-meta-container",
		"#media-popup-container #media-popup-fields",
		function( response ) {
			console.log( response );
			if ( response == "updated" ) { window.location.reload( true ); }
			else {
				jQuery( "#media-popup-fields #loader" ).remove();

				alert_box = "<div id='alert-box' class='animated bounceInDown'>"+ response +"<button id='close-popup-button' onclick='removeAlertBox();'>Close</button></div>";
				jQuery( "#media-popup-container" ).append( alert_box );
			}
		}
	);
}

function openMediaHandler( controller ) {
	mediaController = new UserMedia;
	mediaController.buildAttachmentController( controller.attr( "id" ), controller.attr( "media-type" ) );
}


function keyPressedForms( e, form ) {
	if ( e.keyCode == 13 ) {
		if ( form == 2 ) { updateUserMetaSubmit(); }
		else if ( form == 3 ) { updateCompanyMetaSubmit(); }
	}
	else if ( e.keyCode == 27 ) { controller = new UserMedia(); controller.destroyMediaPopup(); }
}

function openComposer() {
	userComposerController = new UserStory();
	userComposerController.buildComposer();
}
