<?php
/*
*	View name: Company view of the calendar
*/

$user_id = get_current_user_id();

$filter_ = isset( $_GET[ "events_filter" ] ) && !empty( $_GET[ "events_filter" ] ) ? $_GET[ "events_filter" ] : "upcoming";

$calendar_ = new HUB_CALENDAR;
$events_ = $filter_ == "upcoming" ? $calendar_->get_upcoming_events() : $calendar_->get_past_events();
?>
<div class="calendar-options-container">
	<div class="left-column">
		<button id="upcoming-events" class="option <?php echo $filter_ == "upcoming" ? "active" : ""; ?>">Upcoming</button>
		<span class="bull-separator">&bull;</span>
		<button id="past-events" class="option <?php echo $filter_ == "past" ? "active" : ""; ?>">Past</button>
	</div>
	<div class="right-column">
		<?php
		if ( isset( $events_ ) && !empty( $events_ ) ) { ?> <button id="add-event-controller" class="add-event-controller green-bold-button">Add</button> <?php }
		?>
	</div>
</div>
<div class="events-container">
<?php
if ( isset( $events_ ) && !empty( $events_ ) ) { /* List events */ }
else {
	?>

	<h1 class="no-information-message">You don't have any events at the moment.</h1>
	<button id="add-event-controller" class="add-event-controller green-bold-button">Add</button>

	<?php
}

?>
</div>
