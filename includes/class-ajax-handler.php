<?php
/**
 * AJAX Handler Class
 * Handles AJAX requests for color generation and media upload
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class CSG_AJAX_Handler {

	/**
	 * Initialize AJAX handlers
	 */
	public static function init() {
		add_action( 'wp_ajax_csg_search_colors', array( __CLASS__, 'handle_search_colors' ) );
		add_action( 'wp_ajax_nopriv_csg_search_colors', array( __CLASS__, 'handle_search_colors' ) );

		add_action( 'wp_ajax_csg_generate_swatch', array( __CLASS__, 'handle_generate_swatch' ) );
		// Note: Only allow logged-in users to generate and upload
		add_action( 'wp_ajax_csg_get_color_suggestions', array( __CLASS__, 'handle_get_color_suggestions' ) );
		add_action( 'wp_ajax_nopriv_csg_get_color_suggestions', array( __CLASS__, 'handle_get_color_suggestions' ) );
	}

	/**
	 * Handle color search AJAX request
	 */
	public static function handle_search_colors() {
		check_ajax_referer( 'csg_nonce', 'nonce' );

		if ( ! isset( $_POST['search'] ) ) {
			wp_send_json_error( array( 'message' => __( '検索パラメータが見つかりません', 'color-swatch-generator' ) ) );
		}

		$search_term = sanitize_text_field( $_POST['search'] );
		$results = CSG_Color_Database::search_colors( $search_term );

		wp_send_json_success( array(
			'colors' => $results,
		) );
	}

	/**
	 * Handle swatch generation and upload AJAX request
	 */
	public static function handle_generate_swatch() {
		check_ajax_referer( 'csg_nonce', 'nonce' );

		// Check user capability
		if ( ! current_user_can( 'upload_files' ) ) {
			wp_send_json_error( array( 'message' => __( '権限が不足しています', 'color-swatch-generator' ) ) );
		}

		if ( ! isset( $_POST['colors'] ) || ! isset( $_POST['num_colors'] ) ) {
			wp_send_json_error( array( 'message' => __( '必須パラメータが見つかりません', 'color-swatch-generator' ) ) );
		}

		$colors = isset( $_POST['colors'] ) ? array_map( 'sanitize_text_field', (array) $_POST['colors'] ) : array();
		$num_colors = intval( $_POST['num_colors'] );

		// Validate inputs
		if ( empty( $colors ) || $num_colors < 1 || $num_colors > 3 ) {
			wp_send_json_error( array( 'message' => __( '無効な色の数です', 'color-swatch-generator' ) ) );
		}

		// Generate GIF
		$file_path = CSG_GIF_Generator::generate( $colors, $num_colors );

		if ( ! $file_path ) {
			wp_send_json_error( array( 'message' => __( 'GIFの生成に失敗しました', 'color-swatch-generator' ) ) );
		}

		// Upload to media library
		$color_info = implode( ', ', $colors );
		$attachment_id = CSG_GIF_Generator::upload_to_media_library( $file_path, $color_info );

		if ( ! $attachment_id ) {
			wp_send_json_error( array( 'message' => __( 'メディアライブラリへのアップロードに失敗しました', 'color-swatch-generator' ) ) );
		}

		// Get attachment details
		$attachment = get_post( $attachment_id );
		$attachment_url = wp_get_attachment_url( $attachment_id );

		wp_send_json_success( array(
			'attachment_id' => $attachment_id,
			'url'           => $attachment_url,
			'title'         => $attachment->post_title,
			'message'       => __( 'カラースウォッチが正常に生成およびアップロードされました', 'color-swatch-generator' ),
		) );
	}

	/**
	 * Handle color suggestions AJAX request
	 */
	public static function handle_get_color_suggestions() {
		check_ajax_referer( 'csg_nonce', 'nonce' );

		$colors = CSG_Color_Database::get_colors();

		wp_send_json_success( array(
			'colors' => $colors,
		) );
	}
}
