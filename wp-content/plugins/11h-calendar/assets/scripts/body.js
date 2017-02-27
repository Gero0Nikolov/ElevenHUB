var HubCalendar = function() {
	var holder_ = this;

	this.openAddEventDialog = function() {
		event_picker_view = "\
		<div id='event-picker-container' class='event-picker-container animated fadeIn'>\
			<div id='event-picker-inner' class='event-picker-inner'>\
				<div id='step-1' class='event-picker animated'>\
					<div id='date-picker' class='date-picker'>\
						<input type='text' id='day' class='small bordered' placeholder='28'>\
						<input type='text' id='month' class='large bordered' placeholder='December'>\
						<input type='text' id='year' class='medium' placeholder='1990'>\
					</div>\
					<div id='event-box' class='event-box normal'>\
						<h1 id='event-title' class='event-title'>Free</h1>\
						<div id='event-date' class='event-date'>28 DEC 1990</div>\
					</div>\
					<div id='color-picker' class='color-picker'>\
						<button id='color-emerald' class='color-box color-emerald'></button>\
						<button id='color-peter_river' class='color-box color-peter_river'></button>\
						<button id='color-amethyst' class='color-box color-amethyst'></button>\
						<button id='color-wet_asphalt' class='color-box color-wet_asphalt'></button>\
						<button id='color-alizarin' class='color-box color-alizarin'></button>\
						<button id='color-clouds' class='color-box color-clouds'></button>\
					</div>\
					<button id='next-step' class='event-button'>Next</button>\
				</div>\
				<div id='step-2' class='hidden animated'>\
					<div id='event-banner' class='event-banner'>\
						<div class='overlay'>Change!</div>\
					</div>\
					<div id='event-info' class='event-info'>\
						<h1 id='event-title' class='event-title' contenteditable='true' placeholder='Event title'></h1>\
						<textarea id='event-description' class='event-description' placeholder='Event is going to...'></textarea>\
						</div>\
					<div id='event-participants' class='event-participants'>\
						<button id='participant-4' class='participant' style='background-image: url(http://elevenhub.blogy.co/wp-content/uploads/2016/11/1D529754-81CE-46D1-A519-42045FDB9B2D.jpg);'></button>\
						<button id='participant-4' class='participant' style='background-image: url(http://elevenhub.blogy.co/wp-content/uploads/2016/11/1D529754-81CE-46D1-A519-42045FDB9B2D.jpg);'></button>\
						<button id='participant-4' class='participant' style='background-image: url(http://elevenhub.blogy.co/wp-content/uploads/2016/11/1D529754-81CE-46D1-A519-42045FDB9B2D.jpg);'></button>\
						<button id='add-participants' class='add-participants'>+</button>\
					</div>\
					<button id='schedule' class='event-button'>Schedule</button>\
					<button id='previous-step' class='simple-button'>Back</button>\
				</div>\
			</div>\
		</div>\
		";

		jQuery( "body" ).append( event_picker_view );

		jQuery( "#event-picker-container" ).on("click", function( e ){
			if ( e.target == this ) { holder_.removeAddEventDialog(); }
		});

		jQuery( "#event-picker-container #event-picker-inner #next-step" ).on("click", function(){
			jQuery( "#event-picker-container #event-picker-inner #step-1" ).fadeOut( "medium" );
			setTimeout(function() { jQuery( "#event-picker-container #event-picker-inner #step-2" ).fadeIn( "medium" ); }, 400 );
		});

		jQuery( "#event-picker-container #event-picker-inner #previous-step" ).on("click", function(){
			jQuery( "#event-picker-container #event-picker-inner #step-2" ).fadeOut( "medium" );
			setTimeout(function() { jQuery( "#event-picker-container #event-picker-inner #step-1" ).fadeIn( "medium" ); }, 400 );
		});
	}

	this.removeAddEventDialog = function() {
		jQuery( "#event-picker-container" ).removeClass( "fadeIn" ).addClass( "fadeOut" );
		setTimeout(function(){ jQuery( "#event-picker-container" ).remove(); }, 750);
	}
}
