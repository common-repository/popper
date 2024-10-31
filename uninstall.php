<?php
/**
 * Uninstall hook
 *
 * @package popper
 */

defined( 'WP_UNINSTALL_PLUGIN' ) || exit;

$popper_option = get_option( 'popper', false );

if ( $popper_option && ! empty( $popper_option['uninstall'] ) ) {
	delete_option( 'popper' );
}
