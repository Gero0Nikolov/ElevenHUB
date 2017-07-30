<?php
global $wpdb;
?>
<div class="wrap">
    <h1 class="wp-heading-inline">Registered Plugins</h1>
    <div id="promotator-composer" class="promotator-composer postbox">
		<?php
		$server_path = get_home_path();
		$plugins_path = $server_path ."wp-content/plugins/";
		$folders = scandir( $plugins_path );

		foreach ( $folders as $folder ) {
			if ( is_dir( $plugins_path . $folder ) ) {
				if ( file_exists( $plugins_path . $folder ."/hub-player.php" ) && is_plugin_active( $folder ."/". $folder .".php" ) ) {
					$plugin_path = $plugins_path . $folder;
					$plugin_info = file_get_contents( $plugin_path ."/". $folder .".php" );

					$plugin_name = trim( explode( "*", explode( "Plugin name:", $plugin_info )[ 1 ] )[ 0 ] );
					$plugin_description = trim( explode( "*", explode( "Description:", $plugin_info )[ 1 ] )[ 0 ] );
					$plugin_icon_path = file_exists( $plugin_path ."/hub-assets/icon.png" ) ? plugins_url() ."/". $folder ."/hub-assets/icon.png" : "";
					$plugin_background_path = file_exists( $plugin_path ."/hub-assets/background.jpg" ) ? plugins_url() ."/". $folder ."/hub-assets/background.jpg" : "";

					$plugin_author = trim( explode( "*", explode( "Author:", $plugin_info )[ 1 ] )[ 0 ] );

					// Get the plugin status
					$table_ = $wpdb->prefix ."registered_plugins";
					$sql_ = "SELECT status FROM $table_ WHERE plugin_id='$folder'";
					$results_ = $wpdb->get_results( $sql_ );
					$result_ = isset( $results_[ 0 ]->status ) && !empty( $results_[ 0 ]->status ) ? $results_[ 0 ]->status : "";
					?>

					<div id="plugin-<?php echo $folder; ?>" class="plugin-container">
						<div class="header-section">
							<div class="left-section plugin-background" style="background-image: url(<?php echo $plugin_background_path; ?>);">
								<div class="plugin-icon" style="background-image: url(<?php echo $plugin_icon_path; ?>);"></div>
							</div>
							<div class="right-section">
								<h1 class="plugin-name"><?php echo $plugin_name; ?></h1>
								<div class="plugin-description"><?php echo $plugin_description; ?></div>
								<div class="plugin-controls">
								<?php
								if ( $result_ != "declined" ) {
									$result_ = $result_ == "approved" ? "" : $result_;							
									?>

									<button id="plugin-<?php echo $folder; ?>" author="<?php echo $plugin_author; ?>" class="plugin-<?php echo $result_ == "active" ? "deactivate" : ($result_ == "deactivated" || empty( $result_ ) ? "activate" : ""); ?> button button-primary button-large">
										<?php echo $result_ == "active" ? "Deactivate" : ($result_ == "deactivated" || empty( $result_ ) ? "Activate" : ""); ?>
									</button>
									<button id="plugin-<?php echo $folder; ?>" author="<?php echo $plugin_author; ?>" class="plugin-decline button button-large">Decline</button>

									<?php
								} else {
									?>

									<button id="plugin-<?php echo $folder; ?>" author="<?php echo $plugin_author; ?>" class="plugin-approve button button-large">Approve</button>

									<?php
								}
								?>
								</div>
							</div>
						</div>
					</div>

					<?php
				}
			}
		}
		?>
	</div>
</div>
