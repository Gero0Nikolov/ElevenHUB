<?php
$page_id = get_the_ID();
$page_url = get_the_permalink( $page_id );
$tab = isset( $_GET[ "tab" ] ) && !empty( $_GET[ "tab" ] ) ? trim( strtolower( $_GET[ "tab" ] ) ) : "";
?>
<div id="plugins-tab-menu" class="plugins-tab-menu">
	<a href="<?php echo $page_url; ?>?tab=my-plugins" class="tab-control <?php echo $tab == "my-plugins" || empty( $tab ) ? "blue-bold-button active" : "blue-skeleton-bold-button"; ?>">My Plugins</a>
	<a href="<?php echo $page_url; ?>?tab=store" class="tab-control <?php echo $tab == "store" ? "blue-bold-button active" : "blue-skeleton-bold-button"; ?>">Store</a>
</div>
