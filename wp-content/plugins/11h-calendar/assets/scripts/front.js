jQuery( document ).ready(function(){
	if ( jQuery( ".add-event-controller" ).length ) {
		var calendar = new HubCalendar();

		jQuery( ".add-event-controller" ).each(function(){
			jQuery( this ).on( "click", function(){
				calendar.openAddEventDialog();
			} );
		});
	}
});
