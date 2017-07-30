jQuery( document ).ready( function(){
	jQuery( "#promotator-composer .plugin-controls" ).each( function(){
		jQuery( this ).children( ".plugin-activate" ).on( "click", function(){
			pluginID = jQuery( this ).attr( "id" ).split( "plugin-" )[ 1 ];
			authorID = jQuery( this ).attr( "author" );
			jQuery.ajax( {
				url : ajaxurl,
				type : "POST",
				data : {
					action : "controll_user_plugin",
					plugin_id : pluginID,
					status : "activate",
					author_id : authorID
				},
				success : function( response ){
					window.location.reload( true );
				},
				error : function( response ){ console.log( response ); }
			} );
		} );

		jQuery( this ).children( ".plugin-deactivate" ).on( "click", function(){
			pluginID = jQuery( this ).attr( "id" ).split( "plugin-" )[ 1 ];
			authorID = jQuery( this ).attr( "author" );
			jQuery.ajax( {
				url : ajaxurl,
				type : "POST",
				data : {
					action : "controll_user_plugin",
					plugin_id : pluginID,
					status : "deactivate",
					author_id : authorID
				},
				success : function( response ){
					window.location.reload( true );
				},
				error : function( response ){ console.log( response ); }
			} );
		} );

		jQuery( this ).children( ".plugin-decline" ).on( "click", function(){
			pluginID = jQuery( this ).attr( "id" ).split( "plugin-" )[ 1 ];
			authorID = jQuery( this ).attr( "author" );
			jQuery.ajax( {
				url : ajaxurl,
				type : "POST",
				data : {
					action : "controll_user_plugin",
					plugin_id : pluginID,
					status : "decline",
					author_id : authorID
				},
				success : function( response ){
					window.location.reload( true );
				},
				error : function( response ){ console.log( response ); }
			} );
		} );

		jQuery( this ).children( ".plugin-approve" ).on( "click", function(){
			pluginID = jQuery( this ).attr( "id" ).split( "plugin-" )[ 1 ];
			authorID = jQuery( this ).attr( "author" );
			jQuery.ajax( {
				url : ajaxurl,
				type : "POST",
				data : {
					action : "controll_user_plugin",
					plugin_id : pluginID,
					status : "approve",
					author_id : authorID
				},
				success : function( response ){
					window.location.reload( true );
				},
				error : function( response ){ console.log( response ); }
			} );
		} );
	} );
} );
