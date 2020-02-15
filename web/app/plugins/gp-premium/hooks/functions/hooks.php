<?php
// No direct access, please
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! function_exists( 'generate_execute_hooks' ) ) {
	function generate_execute_hooks( $id ) {
		$hooks = get_option( 'generate_hooks' );

		$content = isset( $hooks[$id] ) ? $hooks[$id] : null;

		$disable = isset( $hooks[$id . '_disable'] ) ? $hooks[$id . '_disable'] : null;

		if ( ! $content || 'true' == $disable ) {
			return;
		}

		$php = isset( $hooks[$id . '_php'] ) ? $hooks[$id . '_php'] : null;

		$value = do_shortcode( $content );

		if ( 'true' == $php && ! defined( 'GENERATE_HOOKS_DISALLOW_PHP' ) ) {
			eval( "?>$value<?php " );
		} else {
			echo $value;
		}
	}
}

if ( ! function_exists( 'generate_hooks_wp_head' ) ) {
	add_action( 'wp_head', 'generate_hooks_wp_head' );

	function generate_hooks_wp_head() {
		generate_execute_hooks( 'generate_wp_head' );
	}
}

if ( ! function_exists( 'generate_hooks_before_header' ) ) {
	add_action( 'generate_before_header', 'generate_hooks_before_header', 4 );

	function generate_hooks_before_header() {
		generate_execute_hooks( 'generate_before_header' );
	}
}

if ( ! function_exists( 'generate_hooks_before_header_content' ) ) {
	add_action( 'generate_before_header_content', 'generate_hooks_before_header_content' );

	function generate_hooks_before_header_content() {
		generate_execute_hooks( 'generate_before_header_content' );
	}
}

if ( ! function_exists( 'generate_hooks_after_header_content' ) ) {
	add_action( 'generate_after_header_content', 'generate_hooks_after_header_content' );

	function generate_hooks_after_header_content() {
		generate_execute_hooks( 'generate_after_header_content' );
	}
}

if ( ! function_exists( 'generate_hooks_after_header' ) ) {
	add_action( 'generate_after_header', 'generate_hooks_after_header' );

	function generate_hooks_after_header() {
		generate_execute_hooks( 'generate_after_header' );
	}
}

if ( ! function_exists( 'generate_hooks_inside_main_content' ) ) {
	add_action( 'generate_before_main_content', 'generate_hooks_inside_main_content', 9 );

	function generate_hooks_inside_main_content() {
		generate_execute_hooks( 'generate_before_main_content' );
	}
}

if ( ! function_exists( 'generate_hooks_before_content' ) ) {
	add_action( 'generate_before_content', 'generate_hooks_before_content' );

	function generate_hooks_before_content() {
		generate_execute_hooks( 'generate_before_content' );
	}
}

if ( ! function_exists( 'generate_hooks_after_entry_header' ) ) {
	add_action( 'generate_after_entry_header', 'generate_hooks_after_entry_header' );

	function generate_hooks_after_entry_header() {
		generate_execute_hooks( 'generate_after_entry_header' );
	}
}

if ( ! function_exists( 'generate_hooks_after_content' ) ) {
	add_action( 'generate_after_content', 'generate_hooks_after_content' );

	function generate_hooks_after_content() {
		generate_execute_hooks( 'generate_after_content' );
	}
}

if ( ! function_exists( 'generate_hooks_before_right_sidebar_content' ) ) {
	add_action( 'generate_before_right_sidebar_content', 'generate_hooks_before_right_sidebar_content', 5 );

	function generate_hooks_before_right_sidebar_content() {
		generate_execute_hooks( 'generate_before_right_sidebar_content' );
	}
}

if ( ! function_exists( 'generate_hooks_after_right_sidebar_content' ) ) {
	add_action( 'generate_after_right_sidebar_content', 'generate_hooks_after_right_sidebar_content' );

	function generate_hooks_after_right_sidebar_content() {
		generate_execute_hooks( 'generate_after_right_sidebar_content' );
	}
}

if ( ! function_exists( 'generate_hooks_before_left_sidebar_content' ) ) {
	add_action( 'generate_before_left_sidebar_content', 'generate_hooks_before_left_sidebar_content', 5 );

	function generate_hooks_before_left_sidebar_content() {
		generate_execute_hooks( 'generate_before_left_sidebar_content' );
	}
}

if ( ! function_exists( 'generate_hooks_after_left_sidebar_content' ) ) {
	add_action( 'generate_after_left_sidebar_content', 'generate_hooks_after_left_sidebar_content' );

	function generate_hooks_after_left_sidebar_content() {
		generate_execute_hooks( 'generate_after_left_sidebar_content' );
	}
}

if ( ! function_exists( 'generate_hooks_before_footer' ) ) {
	add_action( 'generate_before_footer', 'generate_hooks_before_footer' );

	function generate_hooks_before_footer() {
		generate_execute_hooks( 'generate_before_footer' );
	}
}

if ( ! function_exists( 'generate_hooks_after_footer_widgets' ) ) {
	add_action( 'generate_after_footer_widgets', 'generate_hooks_after_footer_widgets' );

	function generate_hooks_after_footer_widgets() {
		generate_execute_hooks( 'generate_after_footer_widgets' );
	}
}

if ( ! function_exists( 'generate_hooks_before_footer_content' ) ) {
	add_action( 'generate_before_footer_content', 'generate_hooks_before_footer_content' );

	function generate_hooks_before_footer_content() {
		generate_execute_hooks( 'generate_before_footer_content' );
	}
}

if ( ! function_exists( 'generate_hooks_after_footer_content' ) ) {
	add_action( 'generate_after_footer_content', 'generate_hooks_after_footer_content' );

	function generate_hooks_after_footer_content() {
		generate_execute_hooks( 'generate_after_footer_content' );
	}
}

if ( ! function_exists( 'generate_hooks_wp_footer' ) ) {
	add_action( 'wp_footer', 'generate_hooks_wp_footer' );

	function generate_hooks_wp_footer() {
		generate_execute_hooks( 'generate_wp_footer' );
	}
}
