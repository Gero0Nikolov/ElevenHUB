<?php 
/**

*	Template Name: Employee or Company template

*	@package eleven hub

*/

get_header();
?>

<h1 class='user-type-header'>What is your profile association?</h1>
<div id='employee-company-content' class="flex-container">
	<div class='left-side'>
		<button id='user-employee' class='user-type-container orange-type mt-2em' onclick="chooseProfileAssociation( 'employee' );">
			<span class="fa fa-user icon"></span>
			<span class="text">Employee</span>
		</button>
	</div>
	<div class='right-side'>
		<button id='user-company' class='user-type-container green-type mt-2em' onclick="chooseProfileAssociation( 'company' );">
			<span class="fa fa-users icon"></span>
			<span class="text">Company</span>
		</button>
	</div>
</div>