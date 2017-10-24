/*
*	Method used to initialize buttons on document load.
*/
jQuery( document ).ready(function(){
	jQuery( "[rel='logout']" ).each(function(){ jQuery( this ).on("click", function(){ logOutUser(); }); });

	jQuery( "[rel='bug-report']" ).on( "click", function(){
		bug_report_view = "\
		<div id='bug-report-popup-container' class='popup-container animated fadeIn'>\
			<div id='bug-report-popup-fields' class='popup-inner-container'>\
				<button id='close-button' class='close-button fa fa-close'></button>\
				<label for='bug-position'>Where do you find the problem?</label>\
				<input type='text' id='bug-position'>\
				<label for='bug-position'>What is the type of the problem?</label>\
				<input type='text' id='bug-type'>\
				<label for='bug-position'>Describe the problem?</label>\
				<textarea id='bug-description'></textarea>\
				<button id='submit-button' class='green-bold-button'>Send</button>\
			</div>\
		</div>\
		";

		jQuery( "body" ).append( bug_report_view );

		jQuery( "#bug-report-popup-container" ).on("click", function( e ){ if( e.target == this ){ jQuery( "#bug-report-popup-container" ).removeClass( "fadeIn" ).addClass( "fadeOut" ); setTimeout(function(){ jQuery( "#bug-report-popup-container" ).remove(); }, 750); } });
		jQuery( "#bug-report-popup-container #bug-report-popup-fields #close-button" ).on("click", function(){ jQuery( "#bug-report-popup-container" ).removeClass( "fadeIn" ).addClass( "fadeOut" ); setTimeout(function(){ jQuery( "#bug-report-popup-container" ).remove(); }, 750); });

		jQuery( "#bug-report-popup-container #bug-report-popup-fields #submit-button" ).on("click", function(){
			jQuery( "#bug-report-popup-container #bug-report-popup-fields" ).append( loading );

			position = jQuery( "#bug-report-popup-container #bug-report-popup-fields #bug-position" ).val().trim();
			type = jQuery( "#bug-report-popup-container #bug-report-popup-fields #bug-type" ).val().trim();
			description = jQuery( "#bug-report-popup-container #bug-report-popup-fields #bug-description" ).val().trim();

			jQuery.ajax( {
				url : ajax_url,
				type : "POST",
				data : {
					action : "submit_bug_report",
					args : {
						position : position,
						type : type,
						description : description
					}
				},
				success : function( response ) {
					if ( response == "" || response == null ) {
						jQuery( "#bug-report-popup-container" ).removeClass( "fadeIn" ).addClass( "fadeOut" ); setTimeout(function(){ jQuery( "#bug-report-popup-container" ).remove(); }, 750);
					}
				}
			} );
		});
	} );

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
			}
		}
	}

	if ( jQuery( "#invite-to-company-controller" ).length ) {
		jQuery( "#invite-to-company-controller" ).on("click", function(){
			relationsController = new UserRelations;
			relationsController.sendCompanyInviteRequest( vUserID, "", function( response ){
				if ( response == "" ) { window.location.reload( true ); }
			} );
		});
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

	if ( jQuery( "#medias-container" ).length ) {
		if ( jQuery( "#medias-container .media-container" ).length ) {
			jQuery( ".site-content" ).append( '<button id="load-more-controller" class="blue-skeleton-bold-button display-block mh-auto mt-1em">Load more</button>' );

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
		}
	}

	/* COMPANY PUBLIC CONTROLLERS */
	if ( jQuery( "#join-controller" ).length ) {
		jQuery( "#join-controller" ).on("click", function(){ openCompanyJoinDialog(); });
	}

	if ( jQuery( "#leave-company-controller" ).length ) {
		jQuery( "#leave-company-controller" ).on("click", function(){ openLeaveCompanyDialog(); });
	}

	if ( jQuery( "#company-followers-controller" ).length ) {
		jQuery( "#company-followers-controller" ).on("click", function(){
			openCompanyRelationStatistics( "followers" );
		});
	}

	if ( jQuery( "#company-employees-controller" ).length ) {
		jQuery( "#company-employees-controller" ).on("click", function(){
			openCompanyRelationStatistics( "employees" );
		});
	}

	/* REQUEST CONTROLLERS */
	if ( jQuery( "#request-information" ).length ) {
		jQuery( "#request-information #request-response-controller-accept" ).on("click", function(){
			requestController = new UserRelations;
			requestController.acceptUserRequest( jQuery( this ).attr( "request-id" ), requestType );
		});
		jQuery( "#request-information #request-response-controller-decline" ).on("click", function(){
			requestController = new UserRelations;
			requestController.declineUserRequest( jQuery( this ).attr( "request-id" ), requestType );
		});
	}

	/* HUBBERS PUBLIC CONTROLLERS */
	if ( jQuery( "#more-users-controller" ).length ) {
		jQuery( "#more-users-controller" ).on("click", function(){
			jQuery( "#hubbers-list" ).append( loading );

			publicListsController = new PublicLists;
			publicListsController.getMoreHubbers( usersOffset, function( response ){
				jQuery( "#hubbers-list #loader" ).remove();
				if ( response == "There aren't any users." ) { jQuery( "#more-users-controller" ).remove(); }
				else {
					var hubbers = response;
					var view_ = "";
					for ( hubber_key in hubbers ) {
						var hubber = hubbers[ hubber_key ];
						names_ = hubber.SHORT_NAME == "" ? hubber.FIRST_NAME +" "+ hubber.LAST_NAME : hubber.SHORT_NAME;

						view_ += "\
						<a href='"+ hubber.USER_URL +"' id='user-anchor-"+ hubber.ID +"' class='user-anchor'>\
							<div id='user-"+ hubber.ID +"' class='list-item animated fadeIn' style='background-image: url("+ hubber.BANNER_URL +");'>\
								<div class='overlay'>\
									<div id='user-avatar-"+ hubber.ID +"' class='avatar' style='background-image: url("+ hubber.AVATAR_URL +");'></div>\
									<h1 id='company-brand-"+ hubber.ID +"' class='company-brand'>"+ names_ +"</h1>\
								</div>\
							</div>\
						</a>\
						";
					}
				}

				jQuery( "#hubbers-list" ).append( view_ );
				usersOffset += 100;
			} );
		});
	}

	// STORY BOARDS CONTROLLS
	if ( jQuery( "#company-story-board" ).length ) {
		var lockStoriesLoad = false;
		var storiesOffset = 5;

		jQuery( window ).scroll(function(){
			if ( jQuery( window ).scrollTop() + jQuery( window ).height() > jQuery( document ).height() - 100 ) {
				if ( lockStoriesLoad == false ) {
					jQuery( "#company-story-board" ).append( loading );

					storiesController = new UserStory();
					storiesController.getStories( "", storiesOffset, companyID, function( response ) {
						jQuery( "#company-story-board #loader" ).remove();
						stories_ = response;

						for ( story_key in stories_ ) {
							story_ = stories_[ story_key ];

							story_controls = "";
							if ( story_.meta.is_author ) {
								story_controls = "\
								<div id='story-controls' class='story-controls'>\
									<button id='edit-controller' class='fa fa-pencil control'></button>\
									<button id='delete-controller' class='fa fa-trash-o control'></button>\
								</div>\
								";
							}

							like_button = !story_.meta.is_liked ? "fa-heart-o" : "fa-heart";

							view_ = "\
							<div id='story-"+ story_.ID +"' class='story-container new-story animated fadeInUp'>\
								"+ story_controls +"\
								<div id='story-banner' class='story-banner' style='background-image: url("+ story_.banner.url +");'>\
									<div class='overlay'><span class='message'>Read me!</span></div>\
								</div>\
								<div class='story-meta'>\
									<a href='"+ story_.author.author_url +"' class='story-author-anchor'>\
										<div id='author-avatar' class='story-author-avatar' style='background-image: url("+ story_.author.avatar_url +");'></div>\
									</a>\
									<div class='story-interactions'>\
										<button id='story-like-controller' class='like-button fa "+ like_button +" hvr-bounce-out' story-id='"+ story_.ID +"'><i class='numbers'>"+ story_.meta.likes_count +"</i></button>\
										<button id='story-comments-controller' class='comment-button fa fa-comment hvr-bounce-out' story-id='"+ story_.ID +"'><i class='numbers'>"+ story_.meta.comments_count +"</i></button>\
									</div>\
								</div>\
								<div class='story'>\
									<h1 class='story-title'>"+ story_.title +"</h1>\
									<div class='story-excerpt'>"+ story_.excerpt +"</div>\
								</div>\
							</div>\
							";

							jQuery( "#company-story-board" ).append( view_ );

							jQuery( "#company-story-board .new-story .story-banner" ).each(function(){
								jQuery( this ).on("click", function(){ openStoryReader( jQuery( this ).parent().attr( "id" ).split( "-" )[1] ); });
							});
							jQuery( "#company-story-board .new-story .story" ).each(function(){
								jQuery( this ).on("click", function(){ openStoryReader( jQuery( this ).parent().attr( "id" ).split( "-" )[1] ); });
							});

							initializeStoryControls();
						}

						storiesOffset += 5;
						if ( stories_.length > 0 ) { lockStoriesLoad = false; }
					} );
				}
				lockStoriesLoad = true;
			}
		});

		jQuery( "#company-story-board .story-container .story-banner" ).each(function(){
			jQuery( this ).on("click", function(){ openStoryReader( jQuery( this ).parent().attr( "id" ).split( "-" )[1] ); });
		});
		jQuery( "#company-story-board .story-container .story" ).each(function(){
			jQuery( this ).on("click", function(){ openStoryReader( jQuery( this ).parent().attr( "id" ).split( "-" )[1] ); });
		});
	}

	// Phubber payment method
	if ( jQuery( "#phubber-page" ).length ) {
		jQuery( "#phubber-page #get-phubber" ).on( "click", function(){
			jQuery.ajax({
				url : ajax_url,
				type : 'post',
				data : {
					action : "get_paypal_settings"
				},
				success : function ( response ) {
					paypal_settings = JSON.parse( response );
					if ( paypal_settings.amount !== false ) {
						// Create the PayPal button holder
						if ( jQuery( "#phubber-page #paypal-button" ).length ) { jQuery( "#phubber-page #paypal-button" ).remove(); }
						jQuery( "#phubber-page" ).append( "<div id='paypal-button'></div>" );

						// Connect the button with PayPal
						paypal.Button.render({

					        env: paypal_settings.environment, // Optional: specify 'sandbox' environment

					        client: {
					            sandbox: paypal_settings.client_id_sandbox,
					            production: paypal_settings.client_id_production
					        },

							style: {
					            size: 'medium',
					            color: 'gold',
					            shape: 'pill',
					            label: 'checkout'
					        },

					        payment: function() {

					            var env    = this.props.env;
					            var client = this.props.client;

					            return paypal.rest.payment.create(env, client, {
					                transactions: [
					                    {
					                        amount: { total: paypal_settings.amount, currency: 'EUR' }
					                    }
					                ]
					            });
					        },

					        commit: true, // Optional: show a 'Pay Now' button in the checkout flow

					        onAuthorize: function(data, actions) {

					            // Optional: display a confirmation page here

					            return actions.payment.execute().then(function() {
					                // Show a success page to the buyer
									phubber_ = new Phubber;
									phubber_.updateUserPremium( "", data.paymentID, function( response ){
										if ( response == null || response == "" ) {
											view_ = "\
											<div id='media-popup-container' class='popup-container animated fadeIn'>\
												<div id='alert-box' class='animated bounceInDown'>Your premium account was activated!<button id='close-popup-button' onclick='removeAlertBox();'>Close</button></div>\
											</div>\
											";

											jQuery( "body" ).append( view_ );
											jQuery( "#media-popup-container" ).on("click", function( e ){ if( e.target == this ){ controller = new UserMedia(); controller.destroyMediaPopup(); } });
											jQuery( "#media-popup-container #close-popup-button" ).on("click", function(){ controller = new UserMedia(); controller.destroyMediaPopup(); });
										} else { console.log( response ); }
									} );
					            });
					        }

					    }, '#paypal-button');

						jQuery( "#phubber-page #get-phubber" ).remove();
					}
				}
			});
		} );
	}

	// Set chat controller notifications
	user_messages = new UserMessages();
	setInterval( function(){
		user_messages.getUserMessageNotifications( "", function( response ){			
			if ( response > 0 && jQuery( "#chat-controller #missed-messages" ).length > 0 && response != jQuery( "#chat-controller #missed-messages" ).html() ) {
				jQuery( "#chat-controller #missed-messages" ).html( response ).addClass( "animated fadeInRight" ).show();
			}

			if ( response == 0 ) { jQuery( "#chat-controller #missed-messages" ).hide(); }
		} );
	}, 5000 );

	// Chat controller
	jQuery( "#chat-controller" ).on( "click", function(){
		if ( jQuery( "#chat-rooms-container" ).length ) {
			jQuery( "#chat-rooms-container" ).removeClass( "fadeInUp" ).addClass( "fadeOutDown" );
			setTimeout( function(){ jQuery( "#chat-rooms-container" ).remove(); }, 750 );
		} else {
			rooms_ = "";
			if ( user_type == "employee" ) {
				rooms_ = "\
				<div id='companies' class='rooms-list'>\
					<h1 class='rooms-title'>Companies</h1>\
					<div id='list'>"+ loading +"</div>\
				</div>\
				<div id='hubbers' class='rooms-list'>\
					<h1 class='rooms-title'>Hubbers</h1>\
					<div id='list'>"+ loading +"</div>\
				</div>\
				";
			} else {
				rooms_ = "\
				<div id='employees' class='rooms-list'>\
					<h1 class='rooms-title'>Employees</h1>\
					<div id='list'>"+ loading +"</div>\
				</div>\
				<div id='followers' class='rooms-list'>\
					<h1 class='rooms-title'>Followers</h1>\
					<div id='list'>"+ loading +"</div>\
				</div>\
				";
			}

			view_ = "\
			<div id='chat-rooms-container' class='animated fadeInUp'>\
				<div class='header'>\
					<a href='"+ site_url +"/messenger' class='header-link'>\
						<span class='fa fa-commenting-o'></span>\
						Open messenger\
					</a>\
					<button id='close-chat-rooms-container' class='fa fa-close'></button>\
				</div>\
				<div id='main-rooms'>\
					"+ rooms_ +"\
				</div>\
			</div>\
			";
			jQuery( "body" ).append( view_ );

			jQuery( "#chat-rooms-container .header #close-chat-rooms-container" ).on( "click", function(){
				jQuery( "#chat-controller" ).trigger( "click" );
			} );

			user_relations = new UserRelations;

			if ( user_type == "employee" ) {
				// Pull Companies
				user_relations.getUserEmployers( "", function( response ){
					jQuery( "#chat-rooms-container #main-rooms #companies #list" ).empty();
					for ( company_key in response ) {
						company_ = response[ company_key ];

						names_ = company_.employer.short_name !== undefined && company_.employer.short_name != "" && company_.employer.short_name != null ? company_.employer.short_name : company_.employer.first_name +" "+ company_.employer.last_name;
						view_ = "\
						<button id='company-"+ company_.employer.user_id +"' class='chat-room-preview'>\
							<div class='avatar' style='background-image: url(\""+ company_.employer.avatar_url +"\");'></div>\
							<span class='names'>"+ names_ +"</span>\
						</button>\
						<div id='company-list-"+ company_.employer.user_id +"' class='list'></div>\
						";
						jQuery( "#chat-rooms-container #main-rooms #companies #list" ).append( view_ );

						jQuery( "#company-"+ company_.employer.user_id ).on( "click", function(){
							company_id = company_.employer.user_id;
							user_relations.getUserEmployees( company_id, function( response ){
								jQuery( "#chat-rooms-container #main-rooms #companies #list" ).find( ".active-company-list" ).removeClass( "active-company-list" );
								jQuery( "#company-list-"+ company_id ).empty();

								view_ = "\
								<a href='"+ site_url +"/messenger?u_id="+ company_id +"_group' class='chat-room-preview'>\
									<div class='avatar' style='background-image: url(\""+ template_url +"/assets/images/alphabet/Group_Chat.png\");'></div>\
									<span class='names'>Company group</span>\
								</a>\
								";
								jQuery( "#company-list-"+ company_id ).append( view_ );

								for ( employee_key in response ) {
									employee_ = response[ employee_key ];
									if ( employee_.user_employee_body.user_id != user_id ) {
										names_ = employee_.user_employee_body.user_shortname !== undefined && employee_.user_employee_body.user_shortname != "" && employee_.user_employee_body.user_shortname != null ? employee_.user_employee_body.user_shortname : employee_.user_employee_body.user_first_name +" "+ employee_.user_employee_body.user_last_name;
										view_ = "\
										<a href='"+ site_url +"/messenger?u_id="+ employee_.user_employee_body.user_id +"' id='employee-"+ employee_.user_employee_body.user_id +"' class='chat-room-preview'>\
											<div class='avatar' style='background-image: url(\""+ employee_.user_employee_body.user_avatar_url +"\");'></div>\
											<span class='names'>"+ names_ +"</span>\
										</a>\
										";
										jQuery( "#company-list-"+ company_id ).append( view_ );
									}
								}
								jQuery( "#company-list-"+ company_id ).addClass( "active-company-list" );
							} );
						} );
					}
				} );

				// Pull User Relations
				user_relations.getUserRelations( "", function( response ) {
					jQuery( "#chat-rooms-container #main-rooms #hubbers #list" ).empty();
					for ( following_key in response.follows ) {
						following_ = response.follows[ following_key ];

						names_ = following_.user_followed_body.user_shortname !== undefined && following_.user_followed_body.user_shortname != "" && following_.user_followed_body.user_shortname != null ? following_.user_followed_body.user_shortname : following_.user_followed_body.user_first_name +" "+ following_.user_followed_body.user_last_name;
						view_ = "\
						<a href='"+ site_url +"/messenger?u_id="+ following_.user_followed_body.user_id +"' id='employee-"+ following_.user_followed_body.user_id +"' class='chat-room-preview'>\
							<div class='avatar' style='background-image: url(\""+ following_.user_followed_body.user_avatar_url +"\");'></div>\
							<span class='names'>"+ names_ +"</span>\
						</a>\
						";
						jQuery( "#chat-rooms-container #main-rooms #hubbers #list" ).append( view_ );
					}
				} );
			} else if ( user_type == "company" ) {
				// Pull user employees
				user_relations.getUserEmployees( "", function( response ) {
					jQuery( "#chat-rooms-container #main-rooms #employees #list" ).empty();
					for ( employee_key in response ) {
						employee_ = response[ employee_key ];

						names_ = employee_.user_employee_body.user_shortname !== undefined && employee_.user_employee_body.user_shortname != "" && employee_.user_employee_body.user_shortname != null ? employee_.user_employee_body.user_shortname : employee_.user_employee_body.user_first_name +" "+ employee_.user_employee_body.user_last_name;
						view_ = "\
						<a href='"+ site_url +"/messenger?u_id="+ employee_.user_employee_body.user_id +"' id='employee-"+ employee_.user_employee_body.user_id +"' class='chat-room-preview'>\
							<div class='avatar' style='background-image: url(\""+ employee_.user_employee_body.user_avatar_url +"\");'></div>\
							<span class='names'>"+ names_ +"</span>\
						</a>\
						";
						jQuery( "#chat-rooms-container #main-rooms #employees #list" ).append( view_ );
					}
				} );

				// Pull User Relations
				user_relations.getUserRelations( "", function( response ) {
					jQuery( "#chat-rooms-container #main-rooms #followers #list" ).empty();
					for ( follower_key in response.followers ) {
						follower_ = response.followers[ follower_key ];

						names_ = follower_.user_follower_body.user_shortname !== undefined && follower_.user_follower_body.user_shortname != "" && follower_.user_follower_body.user_shortname != null ? follower_.user_follower_body.user_shortname : follower_.user_follower_body.user_first_name +" "+ follower_.user_follower_body.user_last_name;
						view_ = "\
						<a href='"+ site_url +"/messenger?u_id="+ follower_.user_follower_body.user_id +"' id='employee-"+ follower_.user_follower_body.user_id +"' class='chat-room-preview'>\
							<div class='avatar' style='background-image: url(\""+ follower_.user_follower_body.user_avatar_url +"\");'></div>\
							<span class='names'>"+ names_ +"</span>\
						</a>\
						";
						jQuery( "#chat-rooms-container #main-rooms #followers #list" ).append( view_ );
					}
				} );
			}
		}
	} );

	if ( jQuery( "body" ).hasClass( "page-template-messenger" ) ) {
		// Set loader to the chat options
		jQuery( "#messenger-body #chat-history #default-container" ).append( loading );

		// Pull the chat options
		user_relations = new UserRelations();
		user_relations.getUserChatOptions( "", function( response ){
			jQuery( "#messenger-body #chat-history #default-container" ).empty();

			if ( response != false ) {
				for ( user_key in response ) {
					user_ = response[ user_key ];

					is_group = user_.is_group ? "_group" : "";
					var names_ = user_.short_name !== undefined && user_.short_name != "" ? user_.short_name : user_.first_name +" "+ user_.last_name;

					if ( user_.is_group ) { names_ += " group"; }

					new_messages = "";
					if ( user_.new_messages > 0 ) {
						new_messages = "<span class='unopened-messages'>"+ user_.new_messages +"</span>";
					}

					view_ = "\
					<a href='"+ site_url +"/messenger?u_id="+ user_.user_id + is_group +"' class='chat-option'>\
						"+ new_messages +"\
						<div class='avatar' style='background-image: url("+ user_.user_avatar_url +");'></div>\
						"+ names_ +"\
					</a>\
					";
					jQuery( "#messenger-body #chat-history #default-container" ).append( view_ );
				}
			}
		} );

		// Set the search option
		jQuery( "#messenger-body #chat-history #search-controller" ).on( "keydown", function(){
			clearTimeout( searchRequestInterval );
		} );
		jQuery( "#messenger-body #chat-history #search-controller" ).on( "keyup", function(){
			var searchInput = jQuery( "#messenger-body #chat-history #search-controller" ).val().trim();

			if ( searchInput != "" ) {
				searchRequestInterval = setTimeout( function(){
					jQuery( "#messenger-body #chat-history #default-container" ).hide();
					jQuery( "#messenger-body #chat-history #search-container" ).empty().append( loading ).show();

					var firstName = "";
					var lastName = "";
					var name = "";
					var relations = user_type == "company" ? [ "employees", "follows" ] : ( user_type == "employee" ? [ "employers", "follows" ] : [ "follows" ] );

					if ( searchInput.indexOf( " " ) > -1 ) {
						firstName = searchInput.split( " " )[0];
						lastName = searchInput.split( " " )[1];
					} else {
						name = searchInput;
					}

					generateAJAX({
							functionName : "get_search_results",
							arguments : {
								first_name: firstName,
								last_name: lastName,
								universal_name: name,
								relations: relations
							}
						}, function( response ) {
							jQuery( "#messenger-body #chat-history #search-container" ).empty();
							response = JSON.parse( response );

							for ( user_key in response ) {
								user_ = response[ user_key ];

								if ( user_.user_body.is_company ) {
									is_group = "_group";
									var names_ = user_.user_body.short_name !== undefined && user_.user_body.short_name != "" ? user_.user_body.short_name : user_.user_body.first_name +" "+ user_.user_body.last_name;

									if ( user_.user_body.is_company ) { names_ += " group"; }

									view_ = "\
									<a href='"+ site_url +"/messenger?u_id="+ user_.user_id + is_group +"' class='chat-option'>\
										<div class='avatar' style='background-image: url("+ user_.user_body.avatar_url +");'></div>\
										"+ names_ +"\
									</a>\
									";
									jQuery( "#messenger-body #chat-history #search-container" ).append( view_ );
								}

								var names_ = user_.user_body.short_name !== undefined && user_.user_body.short_name != "" ? user_.user_body.short_name : user_.user_body.first_name +" "+ user_.user_body.last_name;

								view_ = "\
								<a href='"+ site_url +"/messenger?u_id="+ user_.user_id +"' class='chat-option'>\
									<div class='avatar' style='background-image: url("+ user_.user_body.avatar_url +");'></div>\
									"+ names_ +"\
								</a>\
								";
								jQuery( "#messenger-body #chat-history #search-container" ).append( view_ );
							}
						}
					);
				}, 1000 );
			}
		} );

		// Load emojies
		jQuery( "#emoji-container" ).append( loading );
		user_messages = new UserMessages;
		public_lists = new PublicLists;
		public_lists.getPublishedEmojies( function( response ){
			jQuery( "#emoji-container #loader" ).remove();
			for ( emojie_key in response ) {
				emojie_ = response[ emojie_key ];
				button_ = "\
				<button id='emojie-"+ emojie_.code +"' class='emojie-picker'>\
					<img src='"+ emojie_.path +"' async />\
				</dutton>\
				";
				jQuery( "#emoji-container" ).append( button_ );

				jQuery( "#emoji-container #emojie-"+ emojie_.code ).on( "click", function(){
					code_ = jQuery( this ).attr( "id" ).split( "emojie-" )[ 1 ];
					user_messages.sendMessage( "[emojie]"+ code_ +"[/emojie]", receiver_id, function( response ){} );
				} );
			}
		} );

		jQuery( "#emoji-controller" ).on( "click", function( e ){
			if ( e.target == this || jQuery( e.target ).attr( "id" ) == "emoji-icon" ) { jQuery( "#emoji-container" ).toggle(); }
		} );

		// Set send message controls
		jQuery( "#messenger-controller" ).on( "click", function(){
			message = jQuery( "#message-container" ).val();
			jQuery( "#message-container" ).val( "" );
			user_messages.sendMessage( message, receiver_id, function( response ){} );
		} );

		jQuery( "#message-container" ).on( "keyup", function( e ){
			if ( e.keyCode == 13 ) {
				jQuery( "#messenger-controller" ).trigger( "click" );
			}
		} );

		// Pull chat history
		jQuery( "#chat-room" ).append( loading );
		user_messages.getUserMessages( 0, receiver_id, 0, message_limit, function( response ){
			jQuery( "#chat-room #loader" ).remove();

			if ( response.length > 0 ) {
				for ( message_key in response ) {
					message_ = response[ message_key ];
					message_class = message_.sender_id == user_id ? "sender" : "receiver";
					view_ = "\
					<div id='message-"+ message_.id +"' class='message animated bounceInDown "+ message_class +"'>\
						<div class='message-text'>"+ message_.message +"</div>\
					</div>\
					";
					jQuery( "#chat-room" ).append( view_ );
				}

				last_message_id = response[ 0 ].id;
			}

			lock_requests = false;
		} );

		// Set load more messages on scroll
		jQuery( "#chat-room" ).scroll( function() {
		   if( jQuery( "#chat-room" ).scrollTop() + jQuery( "#chat-room" ).innerHeight() >= jQuery( "#chat-room" )[ 0 ].scrollHeight - 100 && lock_requests == false ) {
			   lock_requests = true;

			   user_messages.getUserMessages( 0, receiver_id, message_offset, message_limit, function( response ){
				   jQuery( "#chat-room #loader" ).remove();

		   			if ( response.length > 0 ) {
						console.log( response );
		   				for ( message_key in response ) {
		   					message_ = response[ message_key ];
		   					message_class = message_.sender_id == user_id ? "sender" : "receiver";
		   					view_ = "\
		   					<div id='message-"+ message_.id +"' class='message animated bounceInUp "+ message_class +"'>\
		   						<div class='message-text'>"+ message_.message +"</div>\
		   					</div>\
		   					";
		   					jQuery( "#chat-room" ).append( view_ );
		   				}

						message_offset += message_limit;
		   			}

		   			lock_requests = false;
	   			} );
		   }
		});

		// Set new messages interval
		setInterval( function(){
			if ( lock_requests == false ) {
				lock_requests = true;
				user_messages.getUserNewMessages( 0, receiver_id, last_message_id, function( response ){
					if ( response.length > 0 ) {
						messages_counter = 0;
						for ( message_key in response ) {
							message_ = response[ message_key ];
							message_class = message_.sender_id == user_id ? "sender" : "receiver";
							view_ = "\
							<div id='message-"+ message_.id +"' class='message animated bounceInDown "+ message_class +"'>\
								<div class='message-text'>"+ message_.message +"</div>\
							</div>\
							";
							jQuery( "#chat-room" ).prepend( view_ );
							messages_counter += 1;
						}

						last_message_id = response[ messages_counter - 1 ].id;
					}

					lock_requests = false;
				} );
			}
		}, 1000 );
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

		if ( typeof( response ) === 'object' ) {
			actionResult = response.action_result;
			followersText = response.followers.length == 1 ? response.followers.length + " follower" : response.followers.length + " followers" ;

			if ( jQuery( "#profile-board .user-board #followers" ).length ) { jQuery( "#profile-board .user-board #followers" ).html( "<i class='fa fa-globe icon belize-hole'></i> "+ followersText ); }
			if ( jQuery( "#company-container #company-information .overlay #company-meta" ).length ) { jQuery( "#company-container #company-information .overlay #company-meta #company-followers-controller" ).html( followersText ); }
		} else { actionResult = response };

		if ( actionResult == "followed" ) {
			if ( container.attr( "company" ) == "true" ) { container.removeClass( "green-bold-button" ).addClass( "skeleton-bold-button" ).html( "Unfollow" ); }
			else { container.removeClass( "follow-button" ).addClass( "unfollow-button" ).html( "Unfollow" ); }
		}
		else if ( actionResult == "unfollowed" ) {
			if ( container.attr( "company" ) == "true" ) { container.removeClass( "skeleton-bold-button" ).addClass( "green-bold-button" ).html( "Follow" ); }
			else { container.removeClass( "unfollow-button" ).addClass( "follow-button" ).html( "Follow" ); }
		}
	} );
}

function openCompanyRelationStatistics( statisticsTYPE ) {
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

	if ( statisticsTYPE == "followers" ) {
		relationsController = new UserRelations( -1 );
		relationsController.getUserFollowers( "", function( response ){
			var followers = response;

			var count_followers = 0;

			var followers_container = "";
			for ( follower_key in followers ) {
				var follower = followers[ follower_key ];
				var names_ = follower.user_follower_body.user_shortname == "" ? follower.user_follower_body.user_first_name +" "+ follower.user_follower_body.user_last_name : follower.user_follower_body.user_shortname;

				count_followers += 1;

				followers_container += "\
				<a href='"+ follower.user_follower_body.user_url +"' id='follower-anchor-"+ follower.row_id +"' class='relation-anchor'>\
					<div class='relation-container'>\
						<div class='user-avatar' style='background-image: url(\""+ follower.user_follower_body.user_avatar_url +"\");'></div>\
						<h1 class='user-names'>"+ names_ +"</h1>\
					</div>\
				</a>\
				";
			}

			followers_button_text = count_followers != 1 ? count_followers +" followers" : count_followers +" follower";

			view_header = "\
			<div id='user-relations-header' class='user-relations-header'>\
				<button id='followers-anchor-controller' class='active relation-anchor-controller peter-river'>"+ followers_button_text +"</button>\
			</div>\
			";

			view_body = "\
			<div id='user-relations-body' class='user-relations-body'>\
				<div id='user-followers-container' class='active user-list'>"+ followers_container +"</div>\
			</div>\
			";

			jQuery( "#media-popup-container #media-popup-fields #loader" ).remove();
			jQuery( "#media-popup-container #media-popup-fields" ).append( view_header ).append( view_body );
		} );
	}
	else if ( statisticsTYPE == "employees" ) {
		relationsController = new UserRelations( -1 );
		relationsController.getUserEmployees( "", function( response ){
			var employees = response;

			var count_employees = 0;

			var employees_container = "";
			for ( employee_key in employees ) {
				var employee = employees[ employee_key ];
				var names_ = employee.user_employee_body.user_shortname == "" ? employee.user_employee_body.user_first_name +" "+ employee.user_employee_body.user_last_name : employee.user_employee_body.user_shortname;

				count_employees += 1;

				employees_container += "\
				<div class='relation-container'>\
					<a href='"+ employee.user_employee_body.user_url +"' id='follower-anchor-"+ employee.row_id +"' class='relation-anchor'>\
						<div class='user-avatar' style='background-image: url(\""+ employee.user_employee_body.user_avatar_url +"\");'></div>\
						<h1 class='user-names'>"+ names_ +"</h1>\
					</a>\
					<div class='relation-controls'><button id='fire-user' user-id='"+ employee.user_employee_body.user_id +"' class='option red fa fa-close' title='Fire this employee.'></button></div>\
				</div>\
				";
			}

			employees_button_text = count_employees!= 1 ? count_employees +" employees" : count_employees +" employee";

			view_header = "\
			<div id='user-relations-header' class='user-relations-header'>\
				<button id='employees-anchor-controller' class='active relation-anchor-controller peter-river'>"+ employees_button_text +"</button>\
			</div>\
			";

			view_body = "\
			<div id='user-relations-body' class='user-relations-body'>\
				<div id='user-employee-container' class='active user-list'>"+ employees_container +"</div>\
			</div>\
			";

			jQuery( "#media-popup-container #media-popup-fields #loader" ).remove();
			jQuery( "#media-popup-container #media-popup-fields" ).append( view_header ).append( view_body );

			jQuery( "#media-popup-container #media-popup-fields #fire-user" ).each(function(){
				jQuery( this ).on("click", function(){
					userID = jQuery( this ).attr( "user-id" );
					relationsController.removeCompanyEmployee( userID, companyID, function( response ){
						if ( response == "fired" ) { jQuery( "[user-id='"+ userID +"']" ).remove(); }
						else { console.log( response ); }
					} );
				});
			});
		} );
	}
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
			var names_ = follower.user_follower_body.user_shortname == "" ? follower.user_follower_body.user_first_name +" "+ follower.user_follower_body.user_last_name : follower.user_follower_body.user_shortname;

			count_followers += 1;

			followers_container += "\
			<a href='"+ follower.user_follower_body.user_url +"' id='follower-anchor-"+ follower.row_id +"' class='relation-anchor'>\
				<div class='relation-container'>\
					<div class='user-avatar' style='background-image: url(\""+ follower.user_follower_body.user_avatar_url +"\");'></div>\
					<h1 class='user-names'>"+ names_ +"</h1>\
				</div>\
			</a>\
			";
		}

		var follows_container = "";
		for ( follow_key in follows ) {
			var follow = follows[ follow_key ];
			var names_ = follow.user_followed_body.user_shortname == "" ? follow.user_followed_body.user_first_name +" "+ follow.user_followed_body.user_last_name : follow.user_followed_body.user_shortname;

			count_follows += 1;

			follows_container += "\
			<a href='"+ follow.user_followed_body.user_url +"' id='follow-anchor-"+ follow.row_id +"' class='relation-anchor'>\
				<div class='relation-container'>\
					<div class='user-avatar' style='background-image: url(\""+ follow.user_followed_body.user_avatar_url +"\");'></div>\
					<h1 class='user-names'>"+ names_ +"</h1>\
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

				notification.notification_body.notification_link += (notification.notification_body.notification_link.indexOf( "?" ) < 0 ? "?" : "&") + "read_notification=" + notification.row_id;
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
				notification.notification_body.notification_link += (notification.notification_body.notification_link.indexOf( "?" ) < 0 ? "?" : "&") + "read_notification=" + notification.row_id;
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

/*
*	THIS METHOD IS USED TO SUBMIT COMPANY META DATA FROM THE USER SETTINGS PAGE
*/
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

function openCompanyJoinDialog() {
	relationsController = new UserRelations( vUserID );
	relationsController.buildCompanyJoinDialog();
}

function openLeaveCompanyDialog() {
	relationsController = new UserRelations( vUserID );
	relationsController.buildCompanyLeaveDialog();
}

function openStoryReader( storyID, slideToComments = false ) {
	view = "\
	<div id='story-reader-popup' class='reader-popup animated fadeIn'>\
		<div id='story-reader-inline' class='reader-inline-popup'>\
			<button id='close-button' class='close-button fa fa-times'></button>\
			"+ loading +"\
		</div>\
	</div>\
	";

	jQuery( "body" ).append( view );
	jQuery( "#story-reader-popup #close-button" ).on("click", function(){
		jQuery( "#story-reader-popup" ).removeClass( "fadeIn" ).addClass( "fadeOut" );
		setTimeout(function(){ jQuery( "#story-reader-popup" ).remove(); }, 750);
	});
	jQuery( "#story-reader-popup" ).on("click", function( e ){
		if ( e.target == this ) {
			jQuery( "#story-reader-popup" ).removeClass( "fadeIn" ).addClass( "fadeOut" );
			setTimeout(function(){ jQuery( "#story-reader-popup" ).remove(); }, 750);
		}
	});

	generateAJAX({
		functionName : "get_user_story",
		arguments : {
			post_id: storyID,
			company_id: companyID
		}
	}, function( response ) {
		generateAJAX({
				functionName : "set_story_view",
				arguments : {
					story_id: storyID
				}
			}, function( response ) {}
		);

		story_ = JSON.parse( response );

		names = story_.author.short_name != "" ? story_.author.short_name : story_.author.first_name +" "+ story_.author.last_name;
		like_sign = story_.meta.is_liked ? "fa-heart" : "fa-heart-o";

		comment_composer = "";
		if ( story_.meta.is_requester_employee ) {
			comment_composer = "\
			<div class='comment-composer'>\
				<input type='text' id='comment-holder'>\
				<button id='comment-controller' class='fa fa-paper-plane'></button>\
			</div>\
			";
		}

		view = "\
		<div class='author'>\
			<a href='"+ story_.author.author_url +"' class='author-anchor'>\
				<div class='avatar' style='background-image: url("+ story_.author.avatar_url +");'></div>\
				"+ names +"\
			</a>\
		</div>\
		<div class='story-container'>\
			<div class='story-banner' style='background-image: url("+ story_.banner.url +");'></div>\
			<div class='story'>\
				<h1 class='story-title'>"+ story_.title +"</h1>\
				<div class='story-content'>"+ story_.content +"</div>\
			</div>\
		</div>\
		<div class='story-meta'>\
			<button id='story-like-controller' class='like-button fa "+ like_sign +" hvr-bounce-out' story-id='"+ storyID +"'><i class='numbers'>"+ story_.meta.likes.length +"</i></button>\
			<button id='story-comments-controller' class='comment-button fa fa-comment hvr-bounce-out' story-id='"+ storyID +"'><i class='numbers'>"+ story_.meta.comments_count +"</i></button>\
		</div>\
		";

		if ( story_.meta.comments_allowed == "allow" ) {
			view += "\
			<div id='comments-container' class='story-comments'>\
				<div id='comments'>"+ loading +"</div>\
				"+ comment_composer +"\
			</div>\
			";
		}

		jQuery( "#story-reader-popup #story-reader-inline #loader" ).remove();
		jQuery( "#story-reader-popup #story-reader-inline" ).append( view );

		// Attach controls
		jQuery( "#story-reader-popup #story-reader-inline .story-meta #story-like-controller" ).on( "click", function(){
			storyController = new UserStory();
			storyID = jQuery( this ).attr( "story-id" );
			storyController.likeUnlikeStory( storyID, "", function( response ) {
				actionController = jQuery( "#story-reader-popup #story-reader-inline .story-meta #story-like-controller");
				actionController.children( ".numbers" ).html( response.likes_count );
				if ( response.action == "like" ) { actionController.removeClass( "fa-heart-o" ).addClass( "fa-heart" ); }
				else if ( response.action == "dislike" ) { actionController.removeClass( "fa-heart" ).addClass( "fa-heart-o" ); }
			} );
		} );

		if ( story_.meta.comments_allowed == "allow" ) {
			jQuery( "#story-reader-popup #story-reader-inline .story-meta #story-comments-controller" ).on( "click", function(){
				jQuery( "#story-reader-popup #story-reader-inline .comment-composer #comment-holder" ).focus();
			} );

			jQuery( "#story-reader-popup #story-reader-inline .comment-composer #comment-holder" ).on( "keydown", function( e ){
				if ( e.keyCode == 13 ) {
					storyID = jQuery( "#story-reader-popup #story-reader-inline .story-meta #story-comments-controller" ).attr( "story-id" );
					commentID = jQuery( "#story-reader-popup #story-reader-inline .comment-composer #comment-holder" ).attr( "comment-id" );
					commentContent = jQuery( "#story-reader-popup #story-reader-inline .comment-composer #comment-holder" ).val().trim();

					var storyController = new UserStory();
					storyController.publishComment( storyID, "", commentContent, commentID, function( response ){
						jQuery( "#story-reader-popup #story-reader-inline .comment-composer #comment-holder" ).val( "" ).removeAttr( "comment-id" );
						storyController.getComments( storyID, "", function( response ) {
							if ( Array.isArray( response ) ) {
								jQuery( "#story-reader-popup #story-reader-inline .story-meta #story-comments-controller .numbers" ).html( response.length );
								parseComments( response );
							}
						} );
					} );
				}
			} );

			jQuery( "#story-reader-popup #story-reader-inline .comment-composer #comment-controller" ).on( "click", function(){
				storyID = jQuery( "#story-reader-popup #story-reader-inline .story-meta #story-comments-controller" ).attr( "story-id" );
				commentID = jQuery( "#story-reader-popup #story-reader-inline .comment-composer #comment-holder" ).attr( "comment-id" );
				commentContent = jQuery( "#story-reader-popup #story-reader-inline .comment-composer #comment-holder" ).val().trim();

				var storyController = new UserStory();
				storyController.publishComment( storyID, "", commentContent, commentID, function( response ){
					jQuery( "#story-reader-popup #story-reader-inline .comment-composer #comment-holder" ).val( "" );
					storyController.getComments( storyID, "", function( response ) {
						if ( Array.isArray( response ) ) {
							jQuery( "#story-reader-popup #story-reader-inline .story-meta #story-comments-controller .numbers" ).html( response.length );
							parseComments( response );
						}
					} );
				} );
			} );

			// Pull comments
			storyController = new UserStory();
			storyController.getComments( storyID, "", function( response ) {
				if ( Array.isArray( response ) ) { parseComments( response ); }
				else { jQuery( "#story-reader-popup #story-reader-inline #comments-container #comments #loader" ).remove(); }
			} );

			// Slide to comments - If needed
			if ( slideToComments == true ) {
				jQuery( "#story-reader-popup #story-reader-inline" ).animate({
	        		scrollTop: jQuery( "#story-reader-popup #story-reader-inline #comments-container").offset().top
	    		}, 2000);
				jQuery( "#story-reader-popup #story-reader-inline .comment-composer #comment-holder").focus();
			}
		}
	});
}

function parseComments( comments, container = "#story-reader-popup #story-reader-inline #comments-container" ) {
	jQuery( container +" #comments" ).empty();

	for ( comment_key in comments ) {
		comment_ = comments[ comment_key ];

		edit_button = comment_.user.is_author ? "<button id='edit-"+ comment_.id +"' class='edit-controller fa fa-pencil'></button>" : "";
		delete_button = comment_.user.is_author ? "<button id='remove-"+ comment_.id +"' class='delete-controller fa fa-trash-o'></button>" : "";

		view_ = "\
		<div id='comment-"+ comment_.id +"' class='comment'>\
			<div id='author-"+ comment_.user.id +"' class='user-container'>\
				<a href='"+ comment_.user.url +"' class='user-anchor'>\
					<div class='avatar' style='background-image: url("+ comment_.user.avatar +");'></div>\
				</a>\
			</div>\
			<div class='comment-content'>"+ comment_.content +"</div>\
			<div class='comment-meta'>"+ edit_button + delete_button +"</div>\
		</div>\
		";
		jQuery( container +" #comments").append( view_ );

		jQuery( container +" #comment-"+ comment_.id +" #edit-"+ comment_.id ).on( "click", function(){
			storyController = new UserStory();
			storyController.editComment( jQuery( this ).attr( "id" ).split( "-" )[1], container +" #comment-"+ jQuery( this ).attr( "id" ).split( "-" )[1] +" .comment-content", container +" #comment-holder" );
		} );

		jQuery( container +" #comment-"+ comment_.id +" #remove-"+ comment_.id ).on( "click", function(){
			storyController = new UserStory();
			storyController.deleteComment( jQuery( this ).attr( "id" ).split( "-" )[1], function( response ){
				if ( response.result == true ) { jQuery( container +" #comment-"+ response.comment_id ).remove(); }
				else { console.log( response ); }
			} );
		} );
	}
}

function initializeStoryControls() {
	storyController = new UserStory();
	jQuery( ".new-story #story-like-controller" ).each(function(){
		if ( !jQuery( this ).hasClass( "inactive" ) ) {
			jQuery( this ).on( "click", function(){
				storyID = jQuery( this ).attr( "story-id" );
				storyController.likeUnlikeStory( storyID, "", function( response ) {
					actionController = jQuery( "#story-"+ storyID ).find( "#story-like-controller" );
					actionController.children( ".numbers" ).html( response.likes_count );
					if ( response.action == "like" ) { actionController.removeClass( "fa-heart-o" ).addClass( "fa-heart" ); }
					else if ( response.action == "dislike" ) { actionController.removeClass( "fa-heart" ).addClass( "fa-heart-o" ); }
				} );
			} );
		}
	});

	jQuery( ".new-story #story-comments-controller" ).each(function(){
		jQuery( this ).on( "click", function(){
			storyID = jQuery( this ).attr( "story-id" );
			openStoryReader( storyID, true );
		} );
	});

	jQuery( ".new-story #edit-controller" ).each(function(){
		jQuery( this ).on( "click", function(){
			storyID = jQuery( this ).parent().parent().attr( "id" ).split( "-" )[1];

			generateAJAX({
					functionName : "get_user_story",
					arguments : {
						post_id: storyID,
						company_id: companyID
					}
				}, function( response ) {
					story_ = JSON.parse( response );

					storyComposer = new UserStory();
					storyComposer.buildComposer();

					jQuery( "#story-composer" ).attr( "post-id", story_.ID );
					jQuery( "#story-composer #story-featured-image" ).attr( "attachment-id", story_.banner.ID ).attr( "style", "background-image: url("+ story_.banner.url +")" );
					jQuery( "#story-composer #story-header" ).html( story_.title );
					setTimeout(function(){ tinymce.activeEditor.setContent( story_.content ); }, 250);
				}
			);
		} );
	});

	jQuery( ".new-story #delete-controller" ).each(function(){
		jQuery( this ).on( "click", function(){
			storyID = jQuery( this ).parent().parent().attr( "id" ).split( "-" )[1];
			storyController = new UserStory();
			storyController.deleteStory( storyID, companyID, function( response ) {
				if ( jQuery.isNumeric( response ) ) {
					jQuery( "#story-"+ response ).removeClass( "fadeIn" ).addClass( "fadeOut" );
					setTimeout(function(){ jQuery( "#story-"+ response ).remove(); }, 750);
				}
			} );
		} );
	});

	jQuery( ".new-story" ).each(function(){ jQuery( this ).removeClass( "new-story" ); });
}

function initializeSingleStoryControls() {
	storyController = new UserStory();
	jQuery( ".story-container #like-controller" ).each(function(){
		if ( !jQuery( this ).hasClass( "inactive" ) ) {
			jQuery( this ).on( "click", function(){
				storyID = jQuery( this ).attr( "story-id" );
				storyController.likeUnlikeStory( storyID, "", function( response ) {
					actionController = jQuery( ".story-container" ).find( "#like-controller" );
					actionController.children( ".numbers" ).html( response.likes_count );
					if ( response.action == "like" ) { actionController.removeClass( "fa-heart-o" ).addClass( "fa-heart" ); }
					else if ( response.action == "dislike" ) { actionController.removeClass( "fa-heart" ).addClass( "fa-heart-o" ); }
				} );
			} );
		}
	});

	jQuery( ".story-container #comment-controller" ).on( "click", function(){ jQuery( ".story-container #comment-holder").focus(); } );

	jQuery( ".story-container #comment-holder" ).on( "keydown", function( e ){
		if ( e.keyCode == 13 ) {
			storyID = jQuery( ".story-container #comment-controller" ).attr( "story-id" );
			commentID = jQuery( ".story-container #comment-holder" ).attr( "comment-id" );
			commentContent = jQuery( ".story-container #comment-holder" ).val().trim();

			var storyController = new UserStory();
			storyController.publishComment( storyID, "", commentContent, commentID, function( response ){
				jQuery( ".story-container #comment-holder" ).val( "" ).removeAttr( "comment-id" );
				storyController.getComments( storyID, "", function( response ) {
					if ( Array.isArray( response ) ) {
						jQuery( ".story-container #comment-holder .numbers" ).html( response.length );
						parseComments( response, ".story-container #comments-container" );
					}
				} );
			} );
		}
	} );

	jQuery( ".story-container .comment-composer #comment-controller" ).on( "click", function( e ){
		storyID = jQuery( ".story-container #comment-controller" ).attr( "story-id" );
		commentID = jQuery( ".story-container #comment-holder" ).attr( "comment-id" );
		commentContent = jQuery( ".story-container #comment-holder" ).val().trim();

		var storyController = new UserStory();
		storyController.publishComment( storyID, "", commentContent, commentID, function( response ){
			jQuery( ".story-container #comment-holder" ).val( "" ).removeAttr( "comment-id" );
			storyController.getComments( storyID, "", function( response ) {
				if ( Array.isArray( response ) ) {
					jQuery( ".story-container #comment-holder .numbers" ).html( response.length );
					parseComments( response, ".story-container #comments-container" );
				}
			} );
		} );
	} );
}

function pullUserStoriesBoard( storiesContainer, args ) {
	jQuery( storiesContainer ).append( loading );

	var storiesController = new UserStory();
	storiesController.getUserStoriesBoard( args.userID, args.offset, args.compositions, function( response ){
		jQuery( storiesContainer +" #loader" ).remove();

		if ( response.length > 0 ) {
			stories_ = response;
			for ( story_key in stories_ ) {
				story_ = stories_[ story_key ];

				heart = story_.meta.is_liked ? "fa-heart" : "fa-heart-o";

				view_ = "\
				<a href='"+ story_.url +"' class='post-anchor'>\
					<div id='story-"+ story_.ID +"' class='story-container animated fadeInUp'>\
						<div class='story-banner' style='background-image: url("+ story_.banner +");'></div>\
						<h1 class='story-title'>"+ story_.title +"</h1>\
						<div class='story-content'>"+ story_.excerpt +"</div>\
						<div class='story-meta'>\
							<span class='meta story-likes fa "+ heart +"'><i class='numbers'>"+ story_.meta.likes +"</i></span>\
							<span class='meta' title='Author'><i class='icon fa fa-pencil'></i><div class='avatar' style='background-image: url("+ story_.author.avatar_url +");'></div></span>\
							<span class='meta' title='Company'><i class='icon fa fa-at'></i><div class='avatar' style='background-image: url("+ story_.company.avatar_url +");'></div></span>\
						</div>\
					</div>\
				</a>\
				";

				jQuery( storiesContainer ).append( view_ );
			}

			storiesOffset += 10;
			if ( stories_.length > 0 ) { lockStoriesLoad = false; }
			if ( firstLoad == false ) { firstLoad = true; }
		} else {
			if ( jQuery( storiesContainer ).children().length == 0 ) {
				jQuery( storiesContainer ).append( "<h1 class='no-information-message'>No stories yet...</h1>" );
			}
		}
	} );
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

function getUserBadges( userID = "" ) {

}
