<?php
/**
 * GIF Generator Class
 * Handles the generation of color swatch GIF images
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class CSG_GIF_Generator {

	const SIZE = 250;
	const FORMAT = 'image/gif';

	/**
	 * Generate a color swatch GIF image
	 *
	 * @param array $colors Array of hex color values (e.g., ['#FF0000', '#00FF00'])
	 * @param int $num_colors Number of colors
	 * @return string|false File path or false on error
	 */
	public static function generate( $colors, $num_colors = 1 ) {
		if ( empty( $colors ) || $num_colors < 1 || $num_colors > 3 ) {
			return false;
		}

		// Validate and sanitize colors
		$colors = self::sanitize_colors( $colors, $num_colors );
		if ( empty( $colors ) ) {
			return false;
		}

		// Create image
		$image = imagecreatetruecolor( self::SIZE, self::SIZE );
		if ( ! $image ) {
			return false;
		}

		// Calculate height per color
		$color_height = intval( self::SIZE / $num_colors );
		$y_offset = 0;

		// Fill each color section
		foreach ( $colors as $color ) {
			$rgb = self::hex_to_rgb( $color );
			$color_int = imagecolorallocate( $image, $rgb['r'], $rgb['g'], $rgb['b'] );
			
			if ( $color_int === false ) {
				imagedestroy( $image );
				return false;
			}

			imagefilledrectangle(
				$image,
				0,
				$y_offset,
				self::SIZE,
				$y_offset + $color_height,
				$color_int
			);

			$y_offset += $color_height;
		}

		// Save GIF image
		$file_path = self::save_image( $image, $colors );
		imagedestroy( $image );

		return $file_path;
	}

	/**
	 * Sanitize and validate color array
	 *
	 * @param array $colors Array of hex color values
	 * @param int $num_colors Expected number of colors
	 * @return array|false Sanitized colors or false
	 */
	private static function sanitize_colors( $colors, $num_colors ) {
		if ( ! is_array( $colors ) ) {
			return false;
		}

		$sanitized = array();

		for ( $i = 0; $i < $num_colors; $i++ ) {
			if ( ! isset( $colors[ $i ] ) ) {
				return false;
			}

			$color = sanitize_text_field( $colors[ $i ] );
			
			// Remove # if present
			if ( strpos( $color, '#' ) === 0 ) {
				$color = substr( $color, 1 );
			}

			// Validate hex format
			if ( ! preg_match( '/^[0-9A-Fa-f]{6}$/', $color ) ) {
				return false;
			}

			$sanitized[] = '#' . $color;
		}

		return $sanitized;
	}

	/**
	 * Convert hex color to RGB
	 *
	 * @param string $hex Hex color value
	 * @return array|false RGB array or false
	 */
	private static function hex_to_rgb( $hex ) {
		$hex = str_replace( '#', '', $hex );

		if ( strlen( $hex ) !== 6 ) {
			return false;
		}

		return array(
			'r' => hexdec( substr( $hex, 0, 2 ) ),
			'g' => hexdec( substr( $hex, 2, 2 ) ),
			'b' => hexdec( substr( $hex, 4, 2 ) ),
		);
	}

	/**
	 * Save image to file
	 *
	 * @param resource $image GD image resource
	 * @param array $colors Array of colors used
	 * @return string|false File path or false
	 */
	private static function save_image( $image, $colors ) {
		$upload_dir = wp_upload_dir();
		$color_swatch_dir = $upload_dir['basedir'] . '/color-swatches';

		// Ensure directory exists
		if ( ! file_exists( $color_swatch_dir ) ) {
			if ( ! wp_mkdir_p( $color_swatch_dir ) ) {
				return false;
			}
		}

		// Generate filename
		$color_hash = md5( implode( '-', $colors ) . time() );
		$filename = 'swatch-' . $color_hash . '.gif';
		$file_path = $color_swatch_dir . '/' . $filename;

		// Save GIF
		if ( ! imagegif( $image, $file_path ) ) {
			return false;
		}

		return $file_path;
	}

	/**
	 * Upload generated GIF to media library
	 *
	 * @param string $file_path Path to the GIF file
	 * @param string $color_info Color information for title/description
	 * @return int|false Attachment ID or false
	 */
	public static function upload_to_media_library( $file_path, $color_info = '' ) {
		if ( ! file_exists( $file_path ) ) {
			return false;
		}

		require_once ABSPATH . 'wp-admin/includes/image.php';
		require_once ABSPATH . 'wp-admin/includes/file.php';
		require_once ABSPATH . 'wp-admin/includes/media.php';

		$filename = basename( $file_path );
		$upload_dir = wp_upload_dir();
		$rel_path = str_replace( $upload_dir['basedir'], '', $file_path );

		$attachment = array(
			'post_mime_type' => self::FORMAT,
			'post_title'     => 'Color Swatch - ' . $color_info,
			'post_content'   => '',
			'post_status'    => 'inherit',
		);

		// Insert attachment
		$attachment_id = wp_insert_attachment( $attachment, $file_path );

		if ( is_wp_error( $attachment_id ) ) {
			return false;
		}

		// Generate attachment metadata
		$attach_data = wp_generate_attachment_metadata( $attachment_id, $file_path );
		wp_update_attachment_metadata( $attachment_id, $attach_data );

		return $attachment_id;
	}
}
