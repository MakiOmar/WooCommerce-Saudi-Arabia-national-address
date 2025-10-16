<?php
/**
 * Debug Script for Saudi Address Plugin
 * 
 * Place this file in your WordPress root directory and access it via browser
 * Example: http://yoursite.com/debug-saudi-address.php
 */

// Load WordPress
require_once( dirname( __FILE__ ) . '/wp-load.php' );

// Check if user is admin
if ( ! current_user_can( 'manage_options' ) ) {
	wp_die( 'You do not have permission to access this page.' );
}

header( 'Content-Type: text/html; charset=utf-8' );
?>
<!DOCTYPE html>
<html>
<head>
	<title>Saudi Address Plugin Debug</title>
	<style>
		body { font-family: Arial, sans-serif; margin: 20px; }
		h1 { color: #0073aa; }
		h2 { color: #333; margin-top: 30px; }
		table { border-collapse: collapse; width: 100%; margin-top: 10px; }
		th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
		th { background-color: #0073aa; color: white; }
		.success { color: green; }
		.error { color: red; }
		.warning { color: orange; }
		pre { background: #f5f5f5; padding: 10px; border: 1px solid #ddd; overflow-x: auto; }
	</style>
</head>
<body>
	<h1>Saudi Address Plugin Debug Information</h1>
	
	<h2>1. Plugin Status</h2>
	<table>
		<tr>
			<th>Check</th>
			<th>Status</th>
			<th>Details</th>
		</tr>
		<tr>
			<td>WordPress Version</td>
			<td class="success">✓</td>
			<td><?php echo get_bloginfo( 'version' ); ?></td>
		</tr>
		<tr>
			<td>WooCommerce Active</td>
			<td class="<?php echo class_exists( 'WooCommerce' ) ? 'success' : 'error'; ?>">
				<?php echo class_exists( 'WooCommerce' ) ? '✓' : '✗'; ?>
			</td>
			<td>
				<?php 
				if ( class_exists( 'WooCommerce' ) ) {
					echo 'Version: ' . WC()->version;
				} else {
					echo 'WooCommerce is NOT active!';
				}
				?>
			</td>
		</tr>
		<tr>
			<td>Saudi Address Plugin Active</td>
			<td class="<?php echo class_exists( 'Saudi_Address_WooCommerce' ) ? 'success' : 'error'; ?>">
				<?php echo class_exists( 'Saudi_Address_WooCommerce' ) ? '✓' : '✗'; ?>
			</td>
			<td>
				<?php 
				if ( defined( 'SAUDI_ADDRESS_WC_VERSION' ) ) {
					echo 'Version: ' . SAUDI_ADDRESS_WC_VERSION;
				} else {
					echo 'Plugin class not found!';
				}
				?>
			</td>
		</tr>
		<tr>
			<td>Saudi_Address_Checkout Class</td>
			<td class="<?php echo class_exists( 'Saudi_Address_Checkout' ) ? 'success' : 'error'; ?>">
				<?php echo class_exists( 'Saudi_Address_Checkout' ) ? '✓' : '✗'; ?>
			</td>
			<td><?php echo class_exists( 'Saudi_Address_Checkout' ) ? 'Class loaded' : 'Class NOT found!'; ?></td>
		</tr>
	</table>
	
	<h2>2. Plugin Settings</h2>
	<table>
		<tr>
			<th>Option Name</th>
			<th>Value</th>
			<th>Expected</th>
		</tr>
		<?php
		$options = array(
			'saudi_address_enabled'                  => 'yes',
			'saudi_address_required'                 => 'yes',
			'saudi_address_language'                 => 'A',
			'saudi_address_verify_address'           => 'no',
			'saudi_address_show_region'              => 'yes',
			'saudi_address_show_city'                => 'yes',
			'saudi_address_show_district'            => 'yes',
			'saudi_address_show_building_number'     => 'yes',
			'saudi_address_show_postal_code'         => 'yes',
			'saudi_address_show_additional_number'   => 'no',
			'saudi_address_show_street'              => 'no',
			'saudi_address_show_unit_number'         => 'no',
			'saudi_address_api_url'                  => 'https://apina.address.gov.sa/NationalAddress/v3.1',
			'saudi_address_api_key'                  => '(should be set)',
		);
		
		foreach ( $options as $option => $expected ) {
			$value = get_option( $option );
			$is_set = ( $value !== false && $value !== '' );
			
			if ( $option === 'saudi_address_api_key' ) {
				$display_value = $is_set ? '****** (SET)' : '(NOT SET)';
			} else {
				$display_value = $is_set ? $value : '(NOT SET)';
			}
			
			$status = $is_set ? 'success' : 'warning';
			
			echo '<tr>';
			echo '<td>' . esc_html( $option ) . '</td>';
			echo '<td class="' . $status . '">' . esc_html( $display_value ) . '</td>';
			echo '<td>' . esc_html( $expected ) . '</td>';
			echo '</tr>';
		}
		?>
	</table>
	
	<h2>3. Registered Hooks</h2>
	<table>
		<tr>
			<th>Hook Name</th>
			<th>Status</th>
			<th>Details</th>
		</tr>
		<?php
		$hooks_to_check = array(
			'woocommerce_after_checkout_billing_form',
			'woocommerce_checkout_process',
			'woocommerce_checkout_update_order_meta',
			'wp_enqueue_scripts',
		);
		
		foreach ( $hooks_to_check as $hook_name ) {
			global $wp_filter;
			$has_hook = isset( $wp_filter[ $hook_name ] ) && ! empty( $wp_filter[ $hook_name ]->callbacks );
			
			echo '<tr>';
			echo '<td>' . esc_html( $hook_name ) . '</td>';
			echo '<td class="' . ( $has_hook ? 'success' : 'warning' ) . '">';
			echo $has_hook ? '✓ Registered' : '✗ Not registered';
			echo '</td>';
			echo '<td>';
			
			if ( $has_hook ) {
				$callbacks = $wp_filter[ $hook_name ]->callbacks;
				$count = 0;
				foreach ( $callbacks as $priority => $functions ) {
					$count += count( $functions );
				}
				echo $count . ' callback(s) registered';
			} else {
				echo 'No callbacks';
			}
			
			echo '</td>';
			echo '</tr>';
		}
		?>
	</table>
	
	<h2>4. Test Output</h2>
	<p>Attempting to manually call the add_saudi_address_fields function:</p>
	<pre><?php
	if ( class_exists( 'Saudi_Address_Checkout' ) && class_exists( 'WC_Checkout' ) ) {
		echo "Creating checkout instance...\n";
		$checkout = new Saudi_Address_Checkout();
		$wc_checkout = WC()->checkout();
		
		echo "Calling add_saudi_address_fields...\n";
		ob_start();
		$checkout->add_saudi_address_fields( $wc_checkout );
		$output = ob_get_clean();
		
		if ( ! empty( $output ) ) {
			echo "SUCCESS! Fields were generated:\n";
			echo htmlspecialchars( substr( $output, 0, 500 ) ) . '...';
		} else {
			echo "ERROR: No output generated!\n";
			echo "This means the function returned early or there was an issue.\n";
		}
	} else {
		echo "ERROR: Required classes not found!\n";
	}
	?></pre>
	
	<h2>5. Recent Error Log</h2>
	<p>Last 20 lines from debug.log that mention "Saudi Address":</p>
	<pre><?php
	$log_file = WP_CONTENT_DIR . '/debug.log';
	if ( file_exists( $log_file ) ) {
		$lines = file( $log_file );
		$saudi_lines = array();
		
		foreach ( $lines as $line ) {
			if ( stripos( $line, 'saudi address' ) !== false ) {
				$saudi_lines[] = $line;
			}
		}
		
		$saudi_lines = array_slice( $saudi_lines, -20 );
		
		if ( ! empty( $saudi_lines ) ) {
			echo htmlspecialchars( implode( '', $saudi_lines ) );
		} else {
			echo "No Saudi Address entries found in debug.log\n";
		}
	} else {
		echo "debug.log file not found at: $log_file\n";
		echo "To enable debug logging, add this to wp-config.php:\n";
		echo "define( 'WP_DEBUG', true );\n";
		echo "define( 'WP_DEBUG_LOG', true );\n";
		echo "define( 'WP_DEBUG_DISPLAY', false );\n";
	}
	?></pre>
	
	<h2>6. Quick Actions</h2>
	<form method="post" style="margin-top: 20px;">
		<input type="hidden" name="action" value="set_defaults" />
		<button type="submit" style="padding: 10px 20px; background: #0073aa; color: white; border: none; cursor: pointer;">
			Set Default Options Now
		</button>
	</form>
	
	<?php
	if ( isset( $_POST['action'] ) && $_POST['action'] === 'set_defaults' ) {
		update_option( 'saudi_address_enabled', 'yes' );
		update_option( 'saudi_address_required', 'yes' );
		update_option( 'saudi_address_language', 'A' );
		update_option( 'saudi_address_verify_address', 'no' );
		update_option( 'saudi_address_show_region', 'yes' );
		update_option( 'saudi_address_show_city', 'yes' );
		update_option( 'saudi_address_show_district', 'yes' );
		update_option( 'saudi_address_show_building_number', 'yes' );
		update_option( 'saudi_address_show_postal_code', 'yes' );
		update_option( 'saudi_address_show_additional_number', 'no' );
		update_option( 'saudi_address_show_street', 'no' );
		update_option( 'saudi_address_show_unit_number', 'no' );
		update_option( 'saudi_address_api_url', 'https://apina.address.gov.sa/NationalAddress/v3.1' );
		
		echo '<div style="background: #4CAF50; color: white; padding: 15px; margin-top: 20px; border-radius: 4px;">';
		echo '<strong>✓ Success!</strong> Default options have been set. Refresh this page to see updated values.';
		echo '</div>';
	}
	?>
	
	<p style="margin-top: 40px; color: #666; font-size: 12px;">
		<strong>Note:</strong> After reviewing this information, you can delete this file for security.
	</p>
</body>
</html>

