var loading = "<div id='loader' class='animated rubberBand infinite'></div>";

/*
*	Class name: UserMedia
*	Class arguments: userID [ INT ] (optional)
*	Class purpose: This class is used to control all the media events and methods in the HUB project.
*/
var UserMedia = function( userID = "" ) {
	var classHolder = this;

	/*
	*	Function name: BuildMediaView
	*	Function arguments: loadProfilePictures [ BOOLEAN ], markupToBuild [ STRING ]
	*	Function purpose:
	*	This function generates popup view for the current user.
	*	If the loadProfilePictures variable is true it will load the media view for profile pictures.
	* 	Also if the markupToBuild variable is not empty its content will be created inside the popup.
	*/
	this.BuildMediaView = function( loadProfilePictures = false, markupToBuild = "" ) {
		view_ = "\
		<div id='media-popup-container' class='popup-container animated fadeIn'>\
			<div id='media-popup-fields' class='popup-inner-container'>\
				<button id='close-button' class='close-button fa fa-close'></button>\
				"+ markupToBuild.toString().replace( /(?:\r\n|\r|\n)/g, "" ) +"\
			</div>\
		</div>\
		";

		jQuery( "body" ).append( view_ );
		jQuery( "#media-popup-container" ).on("click", function( e ){ if( e.target == this ){ classHolder.destroyMediaPopup(); } });
		jQuery( "#media-popup-container #media-popup-fields #close-button" ).on("click", function(){ classHolder.destroyMediaPopup(); });

		//Load images if needed
		if ( loadProfilePictures == true ) {
			build = "\
				<div id='profile-images-holder'>\
					<form action='"+ admin_post_url +"' method='POST' enctype='multipart/form-data' id='profile-media-uploader'>\
						<div id='avatar' class='avatar'></div>\
						<input type='file' id='avatar-picker' class='file-picker' name='avatar-picker'>\
						<div id='banner' class='banner'></div>\
						<input type='file' id='banner-picker' class='file-picker' name='banner-picker'>\
						<button id='save-user-pictures-button' class='green-bold-button'>Save</button>\
						<input type='hidden' name='action' value='upload_profile_media'>\
					</form>\
				</div>\
			";

			jQuery( "#media-popup-fields" ).append( build );

			jQuery( "#media-popup-container #media-popup-fields #save-user-pictures-button" ).on("click", function(){ jQuery( "#profile-images-holder" ).append( loading ); jQuery( "#profile-media-uploader" ).submit(); });

			this.getUserAvatarURL( "", function( response ){ jQuery( "#media-popup-fields #profile-images-holder #avatar" ).attr( "style", "background-image: url("+ response +")" ) } );
			this.getUserBannerURL( "", function( response ){ jQuery( "#media-popup-fields #profile-images-holder #banner" ).attr( "style", "background-image: url("+ response +")" ) } );
		}
	}

	/*
	*	Function name: destroyMediaPopup
	*	Function arguments: NONE
	*	Function purpose:
	*	This function will destroy the popup view generated by BuildMediaView function.
	*/
	this.destroyMediaPopup = function() { jQuery( "#media-popup-container" ).removeClass( "fadeIn" ).addClass( "fadeOut" ); setTimeout(function(){ jQuery( "#media-popup-container" ).remove(); }, 750); }

	/*
	*	Function name: getUserAvatarURL
	*	Function arguments: userID [ INT ] (optional), onSuccess [ FUNCTION ] (required) tells the method what to do after the response.
	*	Function purpose: This function is used to retrieve and handle the user avatar url via custom functions provided by the onSuccess argument.
	*/
	this.getUserAvatarURL = function( userID = "", onSuccess ) {
		generateAJAX( { functionName : "get_user_avatar_url", arguments : userID }, function( response ){ onSuccess( response ); } );
	}

	/*
	*	Function name: getUserBannerURL
	*	Function arguments: userID [ INT ] (optional), onSuccess [ FUNCTION ] (required) tells the method what to do after the response.
	*	Function purpose: This function is used to retrieve and handle the user banner url via custom functions provided by the onSuccess argument.
	*/
	this.getUserBannerURL = function( userID = "", onSuccess ) {
		generateAJAX( { functionName : "get_user_banner_url", arguments : userID }, function( response ){ onSuccess( response ); } );
	}

	/*
	*	Function name: buildAttachmentController
	*	Function arguments: attachmentID [ INT ] (required) (the ID of the file in the HUB DB), attachmentTYPE [ STRING ] (required) (the TYPE of the file)
	*	Function purpose: This function is used to build the Front-end controls attached with the specific media attachment.
	*/
	this.buildAttachmentController = function( attachmentID, attachmentTYPE ) {
		buttons_ = "\
			<button id='get-link' class='media-button'>Get link</button>\
			<button id='delete' class='media-button red'>Delete</button>\
		";

		if (
			attachmentTYPE.split( "/" )[0] == "image" ||
			attachmentTYPE.split( "/" )[0] == "video"
		) { buttons_ = "<button id='open' class='media-button'>Open</button>"+ buttons_; }

		view_ = "\
		<div id='media-popup-container' class='popup-container animated fadeIn'>\
			<div id='media-popup-fields' class='popup-inner-container'>\
				<button id='close-button' class='close-button fa fa-close'></button>\
				<div id='media-controls'>"+ buttons_ +"</div>\
			</div>\
		</div>\
		";

		jQuery( "body" ).append( view_ );
		jQuery( "#media-popup-container" ).on("click", function( e ){ if( e.target == this ){ classHolder.destroyMediaPopup(); } });
		jQuery( "#media-popup-container #media-popup-fields #close-button" ).on("click", function(){ classHolder.destroyMediaPopup(); });

		// Set media controls
		if ( jQuery( "#media-popup-container #media-controls #open" ).length ) {
			jQuery( "#media-popup-container #media-controls #open" ).on("click", function(){
				view_ = "<div id='media-preview' class='preview-popup animated fadeIn'>"+ loading +"</div>";
				jQuery( "body" ).append( view_ );

				generateAJAX({
						functionName : "get_attachment_url",
						arguments : attachmentID.split( "-" )[1]
					}, function ( response ) {
						response = JSON.parse( response );
						if ( response != "false" ) {
							jQuery( "#media-preview #loader" ).remove();

							view_ = "";
							switch ( attachmentTYPE.split( "/" )[0] ) {
								case "image":
									view_ = "<img src='"+ response +"' id='"+ attachmentID +"' class='picture-preview animated flipInX'/>";
									break;
								case "video":
									view_ = "<video id='"+ attachmentID +"' class='video-preview animated flipInX' controls loop autoplay><source src='"+ response +"' type='"+ attachmentTYPE +"'></video>";
									break;

								default:

							}


							jQuery( "#media-preview" ).append( view_ );

							// Add close event
							jQuery( "#media-preview" ).on("click", function(){
								jQuery( "#media-preview" ).removeClass( "fadeIn" ).addClass( "fadeOut" );
								setTimeout(function(){ jQuery( "#media-preview" ).remove(); }, 750);
							});
						}
					}
				);
			});
		}

		if ( jQuery( "#media-popup-container #media-controls #get-link" ).length ) {
			jQuery( "#media-popup-container #media-controls #get-link" ).on("click", function(){
				view_ = "<div id='attachment-url-holder' class='inline-popup-holder animated bounceInDown'>"+ loading +"</div>";
				jQuery( "#media-popup-container #media-controls" ).append( view_ );

				generateAJAX({
						functionName : "get_attachment_url",
						arguments : attachmentID.split( "-" )[1]
					}, function ( response ) {
						response = JSON.parse( response );
						if ( response != "false" ) {
							jQuery( "#media-popup-container #media-controls #attachment-url-holder" ).html( "<input type='text' placeholder='Attachment link...' id='copy-text-holder' value='"+ response +"'>" );
							jQuery( "#media-popup-container #media-controls #attachment-url-holder #copy-text-holder" ).on("blur", function(){
								jQuery( "#media-popup-container #media-controls #attachment-url-holder" ).removeClass( "bounceInDown" ).addClass( "bounceOutUp" );
								setTimeout(function(){ jQuery( "#media-popup-container #media-controls #attachment-url-holder" ).remove(); }, 750);
							});
						}
					}
				);
			});
		}

		if ( jQuery( "#media-popup-container #media-controls #delete" ).length ) {
			jQuery( "#media-popup-container #media-controls #delete" ).on("click", function(){
				jQuery( "#media-popup-container #media-controls" ).append( loading );

				if ( selectedElements.length <= 0 ) {
					classHolder.deleteAttachment( attachmentID.split( "-" )[1], function( response ){
						jQuery( "#media-popup-container #media-controls #loader" ).remove();

						if ( response == "true" ) {
							classHolder.destroyMediaPopup();
							jQuery( "#"+ attachmentID ).addClass( "fadeOutUp" );
							setTimeout( function(){ jQuery( "#"+ attachmentID ).remove(); }, 750 );
						} else { console.log( response ); }
					} );
				} else {
					for ( var i = 0; i < selectedElements.length; i++ ) {
						classHolder.deleteAttachment( selectedElements[i].split( '-' )[1], function( response ){
							if ( response == "true" ) {
								window.location.reload( true );
							} else { console.console.log( response ); }
						} );
					}
				}
			});
		}
	}

	/*
	*	Function name: daleteAttachment
	*	Function arguments: attachmentID [ INT ] (required) (the ID of the file in the HUB DB), onSuccess [ FUNCTION ] (required) tells the method what to do after the response.
	*	Function purpose: This function is used to delete file & its meta from the HUB DB and HDD (Hard Drive Disk).
	*/
	this.deleteAttachment = function( attachmentID, onSuccess ) {
		generateAJAX({
				functionName : "delete_user_media",
				arguments : {
					user_id: companyID,
					attachment_id: attachmentID
				}
			},
			function ( response ) {
				onSuccess( response );
			}
		);
	}

	/*
	*	Function name: getUserMedia
	*	Function arguments: userID [ INT ] (required) (the ID of the user which media should be revealed), offset [ INT ] (optional) (the number of elements which should be skipped on the request), onSuccess [ FUNCTION ] (required) tells the method what to do after the response.
	*	Function purpose: This function is used to get media files associated with the userID.
	*/
	this.getUserMedia = function( userID, offset = 0, onSuccess ) {
		generateAJAX({
				functionName : "get_user_media",
				arguments : {
					user_id: companyID,
					is_ajax: true,
					offset: offset
				}
			}, function( response ) {
				onSuccess( response );
			}
		);
	}
}

/*
*	Class name: UserRelations
*	Class arguments: vUserID [ INT ] (required) (the user of the visited / wanted user), onSuccess [ FUNCTION ] (required) tells the method what to do after the response, userID [ INT ] (optional) (the user ID of the currently logged in user).
*	Class purpose: This class is used to control & handle all the relations methods between the different users in the HUB.
*/
var UserRelations = function( vUserID, userID = "" ) {

	/*
	*	Function name: followOrUnfollowRelation
	*	Function arguments: vUserID [ INT ] (required) (the VisitedUserID), recalculateFollower [ BOOL ] (optional) (tells the function if you need an array of the followers to be returned), onSuccess [ FUNCTION ] (required) tells the method what to do after the response, userID [ INT ] (optional) (the user ID of the currently logged in user).
	*	Function purpose: This function is used to generate user relation from TYPE: Follow or Unfollow.
	*/
	this.followOrUnfollowRelation = function( vUserID, recalculateFollowers = false, onSuccess, userID = "" ) {
		generateAJAX( { functionName : "follow_or_unfollow_relation", arguments : { v_user_id: vUserID, user_id: userID, recalculate_followers: recalculateFollowers } }, function ( response ) { onSuccess( response ); } );
	}

	/*
	*	Function name: getUserRelationStatistics
	*	Function arguments: userID [ INT ] (optional) (the ID of the current logged user), onSuccess [ FUNCTION ] (required) tells the method what to do after the response, userID [ INT ] (optional) (the user ID of the currently logged in user).
	*	Function purpse: This function is used to return JSON objects for the user with userID with his/hers followers & follows.
	*/
	this.getUserRelations = function( userID = "", onSuccess ) {
		generateAJAX( { functionName : "get_user_relations", arguments : userID }, function ( response ) { onSuccess( JSON.parse( JSON.parse( response ) ) ); } );
	}
}

/*
*	Class name: UserNotifications
*	Class arguments: userID [ INT ] (optional) (the ID of the desired user notifications)
*	Class purpose: This class is used to control & handle all the methods for the user notifications.
*/
var UserNotifications = function( userID = "" ) {

	/*
	*	Function name: getUserNotifications
	*	Function arguments: userID [ INT ] (optional) (the ID of the desired user notifications), onSuccess [ FUNCTION ] (required) tells the method what to do after the response, userID [ INT ] (optional) (the user ID of the currently logged in user).
	*	Function purpose: This function returns and JSON object of the all last 100 notifications ordered by Notifications_ID
	*/
	this.getUserNotifications = function( userID = "", onSuccess ) {
		generateAJAX( { functionName : "get_user_notifications", arguments : userID }, function( response ) { onSuccess( JSON.parse( JSON.parse( response ) ) ); } );
	}

	/*
	*	Function name: getUserUnseenNotifications
	*	Function arguments: userID [ INT ] (optional) (the ID of the desired user notifications), listedNotificationsIDs [ INT_ARRAY ] (optional) (used to tell the back-end algorithm which notifications to skip), onSuccess [ FUNCTION ] (required) tells the method what to do after the response.
	*	Function purpose: This function returns only the latest unseen notifications of the desired user.
	*/
	this.getUserUnseenNotifications = function( userID = "", listedNotificationsIDs = [], onSuccess ) {
		generateAJAX( { functionName : "get_user_unseen_notifications", arguments : { user_id: userID, listed_notifications: listedNotificationsIDs } }, function ( response ) { onSuccess( JSON.parse( JSON.parse( response ) ) ); } )
	}

}

/*
*	Class name: UserMeta
*	Class arguments: userID [ INT ] (optional) (the ID of the desired user)
*	Class purpose: Used to controll all methods bind with the meta information of the user.
*/
var UserMeta = function( userID = "" ) {

	/*
	*	Function name: updateUserMeta
	*	Function arguments: userID [ INT ] (optional) (the ID of the desired user), formID [ STRING ] (the selector to the form which contains First / Last name, Password & Biography), passwordPromptID [ STRING ] (the selector to the popup form which prompts the user to insert their current password), onSuccess [ FUNCTION ] (required) (tells the method what to do after the response)
	*	Function purpose: This function is used to generate AJAX request to the back-end Brother.PHP update_user_meta( $data ) and update the user meta information from there.
	*/
	this.updateUserMeta = function( userID = "", formID, passwordPromptID, onSuccess ) {
		first_name = jQuery( formID ).find( "#first-name" ).val().trim();
		last_name = jQuery( formID ).find( "#last-name" ).val().trim();
		new_password = jQuery( formID ).find( "#user-password" ).val().trim();
		biography = jQuery( formID ).find( "#biography" ).val().trim();

		current_password = jQuery( passwordPromptID ).find( "#current-password" ).val().trim();

		generateAJAX({
				functionName : "update_user_meta",
				arguments : {
					user_id: userID,
					first_name: first_name,
					last_name: last_name,
					new_password: new_password,
					biography: biography,
					current_password: current_password
				}
			}, function( response ) { onSuccess( JSON.parse( response ) ); }
		);
	}

	this.updateCompanyMeta = function( userID = "", formID, passwordPromptID, onSuccess ) {
		first_name = jQuery( formID ).find( "#first-name" ).val().trim();
		last_name = jQuery( formID ).find( "#last-name" ).val().trim();
		short_name = jQuery( formID ).find( "#short-name" ).val().trim();
		new_password = jQuery( formID ).find( "#user-password" ).val().trim();

		company_type = jQuery( formID ).find( "#company-type option:selected" ).val().trim();
		company_writing_permissions = jQuery( formID ).find( "#company-writing-permissions option:selected" ).val().trim();
		company_publications_communication_permissions = jQuery( formID ).find( "#company-publications-communication-permissions option:selected" ).val().trim();
		company_media_uploads_permissions = jQuery( formID ).find( "#company-media-uploads-permissions option:selected" ).val().trim();

		current_password = jQuery( passwordPromptID ).find( "#current-password" ).val().trim();

		generateAJAX({
				functionName : "update_company_meta",
				arguments : {
					user_id: userID,
					first_name: first_name,
					last_name: last_name,
					short_name: short_name,
					new_password: new_password,
					company_type: company_type,
					company_writing_permissions: company_writing_permissions,
					company_publications_communication_permissions: company_publications_communication_permissions,
					company_media_uploads_permissions: company_media_uploads_permissions,
					current_password: current_password
				}
			}, function( response ) { onSuccess( JSON.parse( response ) ); }
		);
	}
}

/*
*	Class name: UserStory
*	Class arguments: userID [ INT ] (optional) (the ID of the desired user)
*	Class purpose: This class is used as a controller for all Company & User stories over the HUB.
*/
var UserStory = function( userID = "" ) {
	var classHolder = this;

	/*
	*	Function name: buildComposer
	*	Function argumnets: NONE
	*	Function purpose: This function builds & opens the story composer.
	*/
	this.buildComposer = function() {
		var composer_controls = "";
		if ( !isMobile() ) {
			composer_controls = "\
			<div id='story-controls'>\
				<button id='publish-controller' class='blue-bold-button'>Publish</button>\
				<span class='bull-separator'>&bull;</span>\
				<button id='drafts-controller' class='skeleton-bold-button'>Drafts</button>\
				<span class='bull-separator'>&bull;</span>\
				<button id='close-controller' class='skeleton-bold-button'>Close</button>\
			</div>\
			";
		} else {
			composer_controls = "\
			<div id='story-controls'>\
				<button id='publish-controller' class='blue-bold-button'><span class='fa fa-pencil'></span></button>\
				<span class='bull-separator'>&bull;</span>\
				<button id='drafts-controller' class='skeleton-bold-button'><span class='fa fa-archive'></span></button>\
				<span class='bull-separator'>&bull;</span>\
				<button id='close-controller' class='skeleton-bold-button'><span class='fa fa-close'></span></button>\
			</div>\
			";
		}

		floating_controls = "\
		<div id='story-floating-controls'>\
			<button id='add-media-controller' class='hvr-underline-from-center'>Add media</button>\
			<span class='bull-separator'>&bull;</span>\
			<button id='add-mention-controller' class='hvr-underline-from-center'>Mention</button>\
		</div>\
		";

		composer = "\
		<div id='story-composer' class='animated slideInUp'>\
			"+ composer_controls +"\
			<div id='story-featured-image' class='story-banner'><button id='featured-image-controller' class='fa fa-pencil'></button></div>\
			<h1 id='story-header' class='story-header' contenteditable='true' placeholder='Story title'></h1>\
			<div id='story-content' class='story-content' contenteditable='true'></div>\
			"+ floating_controls +"\
		</div>\
		";

		jQuery( "body" ).append( composer );

		//Initialize Tiny-MCE
		tinymce.init({
    		selector: '#story-content',
			theme: "inlite",
			inline: true,
			browser_spellcheck: true,
			selection_toolbar: 'bold italic quicklink h2 blockquote',
			insert_toolbar: '',
			file_picker_types: 'image',
		  	plugins: 'wordcount',
			setup:
			function(editor) {
				editor.on('change', function(e) {
					classHolder.convertLinksToImageVideo( tinyMCE.activeEditor.getContent({ format: "text" }) )
				});
			}
  		});

		//Set autosave
		autoSaveInterval = classHolder.autoSave();

		//Composer controlls
		jQuery( "#story-header" ).on("keydown", function(){
			window.clearInterval( autoSaveInterval );
			autoSaveInterval = classHolder.autoSave();
		});
		jQuery( "#story-content" ).on("keydown", function(){
			window.clearInterval( autoSaveInterval );
			autoSaveInterval = classHolder.autoSave();
		});

		jQuery( "#featured-image-controller" ).on("click", function(){
			build = "\
			<div id='media-popup-container' class='popup-container animated fadeIn'>\
				<div id='media-popup-fields' class='popup-inner-container'>\
					<button id='close-button' class='close-button fa fa-close'></button>\
					<div id='media-list' class='mt-2em mb-2em mh-auto text-align-center line-height-zero'>"+ loading +"</div>\
				</div>\
			</div>\
			";

			jQuery( "body" ).append( build );
			jQuery( "#media-popup-container" ).on("click", function( e ){ if( e.target == this ){ jQuery( "#media-popup-container" ).removeClass( "fadeIn" ).addClass( "fadeOut" ); setTimeout(function(){ jQuery( "#media-popup-container" ).remove(); }, 750); } });
			jQuery( "#media-popup-container #media-popup-fields #close-button" ).on("click", function(){ jQuery( "#media-popup-container" ).removeClass( "fadeIn" ).addClass( "fadeOut" ); setTimeout(function(){ jQuery( "#media-popup-container" ).remove(); }, 750); });

			generateAJAX({
					functionName : "get_user_media",
					arguments : {
						user_id: companyID,
						is_ajax: true
					}
				}, function( response ) {
					jQuery( "#media-popup-container #media-popup-fields #media-list #loader" ).remove();

					medias = JSON.parse( JSON.parse( response ) );
					for ( count = 0; count < medias.length; count++ ) {
						if ( medias[ count ].TYPE.split( "/" )[0] == "image" ) {
							view_ = "<div id='attachment-"+ medias[ count ].ID +"' class='inline-media animated fadeIn' attachment_src='"+ medias[ count ].URL +"' attachment_type='"+ medias[ count ].TYPE +"' style='background-image: url("+ medias[ count ].URL +");'><div>";
							jQuery( "#media-popup-container #media-popup-fields #media-list" ).append( view_ );
						}
					}

					// Set controls
					jQuery( "#media-popup-container #media-popup-fields #media-list .inline-media" ).each(function(){
						jQuery( this ).on("click", function(){
							jQuery( "#story-featured-image" ).css( "background-image", "url("+ jQuery( this ).attr( "attachment_src" ) +")" );
						});
					});

					var mediaOffset = 20;

					// Load more view
					view_ = "<div id='load-more-controller' class='inline-media animated fadeIn'><span>More</span></div>";
					jQuery( "#media-popup-container #media-popup-fields #media-list" ).append( view_ );
					jQuery( "#media-popup-container #media-popup-fields #media-list #load-more-controller" ).on("click", function(){
						generateAJAX({
								functionName : "get_user_media",
								arguments : {
									user_id: companyID,
									is_ajax: true,
									offset: mediaOffset
								}
							}, function( response ) {
								response = JSON.parse( JSON.parse( response ) );

								if ( response != "You don't have any media." ) {
									mediaOffset += 20;
									for ( count = 0; count < response.length; count++ ) {
										if ( response[ count ].TYPE.split( "/" )[0] == "image" ) {
											view_ = "<div id='attachment-"+ response[ count ].ID +"' class='inline-media new animated fadeIn' attachment_src='"+ response[ count ].URL +"' attachment_type='"+ response[ count ].TYPE +"' style='background-image: url("+ response[ count ].URL +");'><div>";
											jQuery( view_ ).insertBefore( "#media-popup-container #media-popup-fields #media-list #load-more-controller" );
										}
									}

									// Set controls
									jQuery( "#media-popup-container #media-popup-fields #media-list .new" ).each(function(){
										jQuery( this ).on("click", function(){
											jQuery( "#story-featured-image" ).css( "background-image", "url("+ jQuery( this ).attr( "attachment_src" ) +")" );
										});

										jQuery( this ).removeClass( "new" );
									});
								} else {
									jQuery( "#media-popup-container #media-popup-fields #media-list #load-more-controller" ).remove();
								}
							}
						);
					});
				}
			);
		});

		jQuery( "#add-media-controller" ).on("click", function(){
			build = "\
			<div id='media-popup-container' class='popup-container animated fadeIn'>\
				<div id='media-popup-fields' class='popup-inner-container'>\
					<button id='close-button' class='close-button fa fa-close'></button>\
					<div id='media-list' class='mt-2em mb-2em mh-auto text-align-center line-height-zero'>"+ loading +"</div>\
				</div>\
			</div>\
			";

			jQuery( "body" ).append( build );
			jQuery( "#media-popup-container" ).on("click", function( e ){ if( e.target == this ){ jQuery( "#media-popup-container" ).removeClass( "fadeIn" ).addClass( "fadeOut" ); setTimeout(function(){ jQuery( "#media-popup-container" ).remove(); }, 750); } });
			jQuery( "#media-popup-container #media-popup-fields #close-button" ).on("click", function(){ jQuery( "#media-popup-container" ).removeClass( "fadeIn" ).addClass( "fadeOut" ); setTimeout(function(){ jQuery( "#media-popup-container" ).remove(); }, 750); });

			generateAJAX({
					functionName : "get_user_media",
					arguments : {
						user_id: companyID,
						is_ajax: true
					}
				}, function( response ) {
					jQuery( "#media-popup-container #media-popup-fields #media-list #loader" ).remove();

					medias = JSON.parse( JSON.parse( response ) );
					for ( count = 0; count < medias.length; count++ ) {
						if ( medias[ count ].TYPE.split( "/" )[0] == "image" ) {
							view_ = "<div id='attachment-"+ medias[ count ].ID +"' class='inline-media animated fadeIn' attachment_src='"+ medias[ count ].URL +"' attachment_type='"+ medias[ count ].TYPE +"' style='background-image: url("+ medias[ count ].URL +");'><div>";
							jQuery( "#media-popup-container #media-popup-fields #media-list" ).append( view_ );
						}
					}

					// Set controls
					jQuery( "#media-popup-container #media-popup-fields #media-list .inline-media" ).each(function(){
						jQuery( this ).on("click", function(){
							tinymce.activeEditor.execCommand( 'mceInsertContent', false, jQuery( this ).attr( "attachment_src" ) );
						});
					});

					var mediaOffset = 20;

					// Load more view
					view_ = "<div id='load-more-controller' class='inline-media animated fadeIn'><span>More</span></div>";
					jQuery( "#media-popup-container #media-popup-fields #media-list" ).append( view_ );
					jQuery( "#media-popup-container #media-popup-fields #media-list #load-more-controller" ).on("click", function(){
						generateAJAX({
								functionName : "get_user_media",
								arguments : {
									user_id: companyID,
									is_ajax: true,
									offset: mediaOffset
								}
							}, function( response ) {
								response = JSON.parse( JSON.parse( response ) );

								if ( response != "You don't have any media." ) {
									mediaOffset += 20;
									for ( count = 0; count < response.length; count++ ) {
										if ( response[ count ].TYPE.split( "/" )[0] == "image" ) { view_ = "<div id='attachment-"+ response[ count ].ID +"' class='inline-media new animated fadeIn' attachment_src='"+ response[ count ].URL +"' attachment_type='"+ response[ count ].TYPE +"' style='background-image: url("+ response[ count ].URL +");'><div>"; }
										else if ( response[ count ].TYPE.split( "/" )[0] == "video" ) { view_ = "<div id='attachment-"+ response[ count ].ID +"' class='inline-media new animated fadeIn' attachment_src='"+ response[ count ].URL +"' attachment_type='"+ response[ count ].TYPE +"'><video autoplay='true' muted='true' loop='true'><source src='"+ response[ count ].URL +"' type='"+ response[ count ].TYPE +"'></video><div class='overlay'></div><div>"; }

										jQuery( view_ ).insertBefore( "#media-popup-container #media-popup-fields #media-list #load-more-controller" );
									}

									// Set controls
									jQuery( "#media-popup-container #media-popup-fields #media-list .new" ).each(function(){
										jQuery( this ).on("click", function(){
											tinymce.activeEditor.execCommand( 'mceInsertContent', false, jQuery( this ).attr( "attachment_src" ) );
										});

										jQuery( this ).removeClass( "new" );
									});
								} else {
									jQuery( "#media-popup-container #media-popup-fields #media-list #load-more-controller" ).remove();
								}
							}
						);
					});
				}
			);
		});

		jQuery( "#close-controller" ).on( "click", function(){ classHolder.destroyComposer(); } );
	}

	/*
	*	Function name: destroyComposer
	*	Function arguments: NONE
	*	Function purpose: This function destroys the story composer which was created by this.buildComposer method.
	*/
	this.destroyComposer = function() {
		window.clearInterval( autoSaveInterval )
		jQuery( "#story-composer" ).removeClass( "slideInUp" ).addClass( "slideOutDown" );
		setTimeout(function(){ jQuery( "#story-composer" ).remove(); }, 750);
	}

	/*
	*	Function name: autoSave
	*	Function arguments: NONE
	*	Function purpose:
	*	This function makes sends the latest drafts over the user story to the back-end.
	*	After 3 seconds without beign edited.
	*/
	this.autoSave = function() {
		return setInterval(function(){ classHolder.draftPost(); }, 3000);
	}

	/*
	*	Function name: draftPost
	*	Function arguments: NONE
	*	Function purpose:
	*	This function sends the post info to the back-end to draft them on the server.
	*/
	this.draftPost = function() {
		title = jQuery( "#story-header" ).html().trim();
		content = tinyMCE.activeEditor.getContent();
		postID = jQuery( "#story-composer" ).attr( "post-id" );

		if ( title != "" && title !== undefined ) {
			generateAJAX({
				functionName : "draft_user_post",
				arguments : {
					post_id: postID,
					post_title: title,
					post_content: content
				}
			}, function( response ) { console.log( response ); } );
		}
	}

	/*
	*	Function name: convertLinksToImageVideo
	*	Function arguments: content [ STRING ] (required)
	*	Function purpose:
	*	This function is used to dinamicaly convert URLs to live Images or videos from supported players.
	*	List of supported players: { YouTube, Vimeo }
	*/
	this.convertLinksToImageVideo = function( content ) {
		var urlRegex = /(https?:\/\/[^\s]+)/g;

	   urls_ = content.match( urlRegex );
	   if ( urls_ !== "undefined" && urls_ != null ) {
		   for ( count = 0; count < urls_.length; count++ ) {
		   		url_ = urls_[ count ];
		   		if ( url_.indexOf( "?marked" ) < 0 ) {
		   			var markup_ = "";

		   			if ( url_.indexOf( "youtube" ) >= 0 ) {
		   				videoID = url_.split( "v=" )[1].split( "&" )[0];
		   				markup_ = "<iframe class='content-video' src='https://www.youtube.com/embed/"+ videoID +"?marked' frameborder='0' allowfullscreen></iframe>";
		   				tinyMCE.activeEditor.setContent( tinyMCE.activeEditor.getContent().replace( url_, markup_ ) );
		   			}
		   			else if ( url_.indexOf( "vimeo" ) >= 0 ) {
		   				videoID = url_.split( "vimeo.com/" )[1];
		   				markup_ = "<iframe src='https://player.vimeo.com/video/"+ videoID +"?marked' frameborder='0' webkitallowfullscreen mozallowfullscreen allowfullscreen class='content-video'></iframe>";
		   				tinyMCE.activeEditor.setContent( tinyMCE.activeEditor.getContent().replace( url_, markup_ ) );
		   			}
		   			else {
						switch ( url_.split( "." )[ url_.split( "." ).length - 1 ] ) {
							case "mp4":
									markup_ = "<video class='video-player' controls loop><source src='"+ url_ +"' type='video/"+ url_.split( "." )[ url_.split( "." ).length - 1 ] +"'></video>";
									tinyMCE.activeEditor.setContent( tinyMCE.activeEditor.getContent().replace( url_, markup_ ) );
								break;

							default:
								jQuery( "<img>", {
									src: url_,
									error: function() {},
									load: function() {
										markup_ = "<img src='"+ url_ +"?marked' class='content-image' />";
										tinyMCE.activeEditor.setContent( tinyMCE.activeEditor.getContent().replace( url_, markup_ ) );
									}
								});
						}
		   			}
		   		}
		   }
	   }

	   return content;
	}
}


/*
*	Function name: generateAJAX
*	Function arguments: args [ JSON object ] (required), onSuccess [ FUNCTION ] (required) tells the function what to do after the response.
*	Function purpose:
*	This function creates a custom AJAX call, which is send to the server. It allows the developer to choose what to happen with the response via custom functions, provided by the onSuccess attribute.
*/
function generateAJAX( args, onSuccess ) {
	return jQuery.ajax({
		url : ajax_url,
		type : 'post',
		data : {
			action : "generate_ajax_call",
			data : JSON.stringify( args )
		},
		success : function( response ) { onSuccess( response ); }
	});
}

/*
*	Function name: isMobile
*	Function arguments: none
*	Function purpose: This function is used to check if the user is viewing the HUB from a Mobile device or no.
*/
function isMobile() {
	if( navigator.userAgent.match(/Android/i)
	|| navigator.userAgent.match(/webOS/i)
	|| navigator.userAgent.match(/iPhone/i)
	|| navigator.userAgent.match(/iPad/i)
	|| navigator.userAgent.match(/iPod/i)
	|| navigator.userAgent.match(/BlackBerry/i)
	|| navigator.userAgent.match(/Windows Phone/i)
 	){
		return true;
  	} else {
    	return false;
  	}
}
