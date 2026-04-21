<?php
/**
 * Color Database Class
 * Manages color names and provides search functionality
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class CSG_Color_Database {

	/**
	 * Get all available colors with names
	 *
	 * @return array Color data
	 */
	public static function get_colors() {
		$colors = array(
			// Reds
			array( 'hex' => '#FF0000', 'names' => array( 'Red', 'レッド', 'red' ) ),
			array( 'hex' => '#FF1493', 'names' => array( 'Deep Pink', 'ディープピンク', 'deep pink' ) ),
			array( 'hex' => '#FF69B4', 'names' => array( 'Hot Pink', 'ホットピンク', 'hot pink' ) ),
			array( 'hex' => '#FFB6C1', 'names' => array( 'Light Pink', 'ライトピンク', 'light pink' ) ),
			array( 'hex' => '#FFC0CB', 'names' => array( 'Pink', 'ピンク', 'pink' ) ),
			array( 'hex' => '#CD5C5C', 'names' => array( 'Indian Red', 'インディアンレッド', 'indian red' ) ),
			array( 'hex' => '#F08080', 'names' => array( 'Light Coral', 'ライトコーラル', 'light coral' ) ),
			array( 'hex' => '#FA8072', 'names' => array( 'Salmon', 'サーモン', 'salmon' ) ),
			array( 'hex' => '#E9967A', 'names' => array( 'Dark Salmon', 'ダークサーモン', 'dark salmon' ) ),
			array( 'hex' => '#F0808080', 'names' => array( 'Light Salmon', 'ライトサーモン', 'light salmon' ) ),
			array( 'hex' => '#DC143C', 'names' => array( 'Crimson', 'クリムゾン', 'crimson' ) ),
			array( 'hex' => '#8B0000', 'names' => array( 'Dark Red', 'ダークレッド', 'dark red' ) ),

			// Oranges
			array( 'hex' => '#FFA500', 'names' => array( 'Orange', 'オレンジ', 'orange' ) ),
			array( 'hex' => '#FF8C00', 'names' => array( 'Dark Orange', 'ダークオレンジ', 'dark orange' ) ),
			array( 'hex' => '#FF7F50', 'names' => array( 'Coral', 'コーラル', 'coral' ) ),
			array( 'hex' => '#FF6347', 'names' => array( 'Tomato', 'トマト', 'tomato' ) ),
			array( 'hex' => '#FFB347', 'names' => array( 'Pastel Orange', 'パステルオレンジ', 'pastel orange' ) ),

			// Yellows
			array( 'hex' => '#FFFF00', 'names' => array( 'Yellow', 'イエロー', 'yellow' ) ),
			array( 'hex' => '#FFFFE0', 'names' => array( 'Light Yellow', 'ライトイエロー', 'light yellow' ) ),
			array( 'hex' => '#FFFACD', 'names' => array( 'Lemon Chiffon', 'レモンシフォン', 'lemon chiffon' ) ),
			array( 'hex' => '#FAFAD2', 'names' => array( 'Light Goldenrod', 'ライトゴールデンロッド', 'light goldenrod' ) ),
			array( 'hex' => '#FFD700', 'names' => array( 'Gold', 'ゴールド', 'gold' ) ),
			array( 'hex' => '#EEE8AA', 'names' => array( 'Pale Goldenrod', 'ペールゴールデンロッド', 'pale goldenrod' ) ),
			array( 'hex' => '#DAA520', 'names' => array( 'Goldenrod', 'ゴールデンロッド', 'goldenrod' ) ),
			array( 'hex' => '#B8860B', 'names' => array( 'Dark Goldenrod', 'ダークゴールデンロッド', 'dark goldenrod' ) ),

			// Greens
			array( 'hex' => '#00FF00', 'names' => array( 'Lime', 'ライム', 'lime' ) ),
			array( 'hex' => '#008000', 'names' => array( 'Green', 'グリーン', 'green' ) ),
			array( 'hex' => '#00FF7F', 'names' => array( 'Spring Green', 'スプリンググリーン', 'spring green' ) ),
			array( 'hex' => '#90EE90', 'names' => array( 'Light Green', 'ライトグリーン', 'light green' ) ),
			array( 'hex' => '#98FB98', 'names' => array( 'Pale Green', 'ペールグリーン', 'pale green' ) ),
			array( 'hex' => '#3CB371', 'names' => array( 'Medium Sea Green', 'ミディアムシーグリーン', 'medium sea green' ) ),
			array( 'hex' => '#2E8B57', 'names' => array( 'Sea Green', 'シーグリーン', 'sea green' ) ),
			array( 'hex' => '#228B22', 'names' => array( 'Forest Green', 'フォレストグリーン', 'forest green' ) ),
			array( 'hex' => '#006400', 'names' => array( 'Dark Green', 'ダークグリーン', 'dark green' ) ),
			array( 'hex' => '#6B8E23', 'names' => array( 'Olive Drab', 'オリーブドラブ', 'olive drab' ) ),
			array( 'hex' => '#808000', 'names' => array( 'Olive', 'オリーブ', 'olive' ) ),

			// Cyans
			array( 'hex' => '#00FFFF', 'names' => array( 'Cyan', 'シアン', 'cyan' ) ),
			array( 'hex' => '#00CED1', 'names' => array( 'Dark Turquoise', 'ダークターコイズ', 'dark turquoise' ) ),
			array( 'hex' => '#00BFFF', 'names' => array( 'Deep Sky Blue', 'ディープスカイブルー', 'deep sky blue' ) ),
			array( 'hex' => '#40E0D0', 'names' => array( 'Turquoise', 'ターコイズ', 'turquoise' ) ),
			array( 'hex' => '#7FFFD4', 'names' => array( 'Aquamarine', 'アクアマリン', 'aquamarine' ) ),
			array( 'hex' => '#AFEEEE', 'names' => array( 'Pale Turquoise', 'ペールターコイズ', 'pale turquoise' ) ),
			array( 'hex' => '#E0FFFF', 'names' => array( 'Light Cyan', 'ライトシアン', 'light cyan' ) ),

			// Blues
			array( 'hex' => '#0000FF', 'names' => array( 'Blue', 'ブルー', 'blue' ) ),
			array( 'hex' => '#000080', 'names' => array( 'Navy', 'ネイビー', 'navy' ) ),
			array( 'hex' => '#4169E1', 'names' => array( 'Royal Blue', 'ロイヤルブルー', 'royal blue' ) ),
			array( 'hex' => '#1E90FF', 'names' => array( 'Dodger Blue', 'ドッジャーブルー', 'dodger blue' ) ),
			array( 'hex' => '#6495ED', 'names' => array( 'Cornflower Blue', 'コーンフラワーブルー', 'cornflower blue' ) ),
			array( 'hex' => '#00008B', 'names' => array( 'Dark Blue', 'ダークブルー', 'dark blue' ) ),
			array( 'hex' => '#0047AB', 'names' => array( 'Cobalt Blue', 'コバルトブルー', 'cobalt blue' ) ),
			array( 'hex' => '#ADD8E6', 'names' => array( 'Light Blue', 'ライトブルー', 'light blue' ) ),
			array( 'hex' => '#B0E0E6', 'names' => array( 'Powder Blue', 'パウダーブルー', 'powder blue' ) ),
			array( 'hex' => '#87CEEB', 'names' => array( 'Sky Blue', 'スカイブルー', 'sky blue' ) ),
			array( 'hex' => '#87CEFA', 'names' => array( 'Light Sky Blue', 'ライトスカイブルー', 'light sky blue' ) ),
			array( 'hex' => '#4682B4', 'names' => array( 'Steel Blue', 'スチールブルー', 'steel blue' ) ),

			// Purples
			array( 'hex' => '#800080', 'names' => array( 'Purple', 'パープル', 'purple' ) ),
			array( 'hex' => '#9932CC', 'names' => array( 'Dark Orchid', 'ダークオーキッド', 'dark orchid' ) ),
			array( 'hex' => '#9400D3', 'names' => array( 'Dark Violet', 'ダークバイオレット', 'dark violet' ) ),
			array( 'hex' => '#8B00FF', 'names' => array( 'Blue Violet', 'ブルーバイオレット', 'blue violet' ) ),
			array( 'hex' => '#BA55D3', 'names' => array( 'Medium Orchid', 'ミディアムオーキッド', 'medium orchid' ) ),
			array( 'hex' => '#DA70D6', 'names' => array( 'Orchid', 'オーキッド', 'orchid' ) ),
			array( 'hex' => '#EE82EE', 'names' => array( 'Violet', 'バイオレット', 'violet' ) ),
			array( 'hex' => '#DDA0DD', 'names' => array( 'Plum', 'プラム', 'plum' ) ),
			array( 'hex' => '#D8BFD8', 'names' => array( 'Thistle', 'アザミ', 'thistle' ) ),
			array( 'hex' => '#FFB6FF', 'names' => array( 'Magenta Light', 'マゼンタライト', 'magenta light' ) ),
			array( 'hex' => '#FF00FF', 'names' => array( 'Magenta', 'マゼンタ', 'magenta' ) ),

			// Browns
			array( 'hex' => '#8B4513', 'names' => array( 'Saddle Brown', 'サドルブラウン', 'saddle brown' ) ),
			array( 'hex' => '#A0522D', 'names' => array( 'Sienna', 'シエナ', 'sienna' ) ),
			array( 'hex' => '#8B7355', 'names' => array( 'Burlywood', 'バーリーウッド', 'burlywood' ) ),
			array( 'hex' => '#D2B48C', 'names' => array( 'Tan', 'タン', 'tan' ) ),
			array( 'hex' => '#CD853F', 'names' => array( 'Peru', 'ペルー', 'peru' ) ),
			array( 'hex' => '#DEB887', 'names' => array( 'Burlywood', 'バーリーウッド', 'burlywood' ) ),
			array( 'hex' => '#D2691E', 'names' => array( 'Chocolate', 'チョコレート', 'chocolate' ) ),
			array( 'hex' => '#BC8F8F', 'names' => array( 'Rosy Brown', 'ロージーブラウン', 'rosy brown' ) ),

			// Grays
			array( 'hex' => '#808080', 'names' => array( 'Gray', 'グレー', 'gray' ) ),
			array( 'hex' => '#A9A9A9', 'names' => array( 'Dark Gray', 'ダークグレー', 'dark gray' ) ),
			array( 'hex' => '#C0C0C0', 'names' => array( 'Silver', 'シルバー', 'silver' ) ),
			array( 'hex' => '#D3D3D3', 'names' => array( 'Light Gray', 'ライトグレー', 'light gray' ) ),
			array( 'hex' => '#DCDCDC', 'names' => array( 'Gainsboro', 'ゲインズボロ', 'gainsboro' ) ),
			array( 'hex' => '#F5F5F5', 'names' => array( 'White Smoke', 'ホワイトスモーク', 'white smoke' ) ),
			array( 'hex' => '#FFFFFF', 'names' => array( 'White', 'ホワイト', 'white' ) ),
			array( 'hex' => '#000000', 'names' => array( 'Black', 'ブラック', 'black' ) ),
		);

		return apply_filters( 'csg_colors', $colors );
	}

	/**
	 * Search colors by name (supports English, Katakana, and mixed case)
	 *
	 * @param string $search_term Search term
	 * @return array Matching colors
	 */
	public static function search_colors( $search_term ) {
		$search_term = sanitize_text_field( $search_term );
		if ( empty( $search_term ) ) {
			return array();
		}

		$search_lower = strtolower( $search_term );
		$all_colors = self::get_colors();
		$results = array();

		foreach ( $all_colors as $color ) {
			foreach ( $color['names'] as $name ) {
				$name_lower = strtolower( $name );
				
				// Handle full-width katakana conversion to half-width
				$name_normalized = self::normalize_string( $name_lower );
				$search_normalized = self::normalize_string( $search_lower );

				// Check if search term is contained in name
				if ( strpos( $name_normalized, $search_normalized ) !== false ||
					 strpos( $name_lower, $search_lower ) !== false ) {
					$results[] = $color;
					break; // Move to next color after finding a match
				}
			}
		}

		return $results;
	}

	/**
	 * Normalize string (convert full-width to half-width katakana)
	 *
	 * @param string $string String to normalize
	 * @return string Normalized string
	 */
	private static function normalize_string( $string ) {
		// Convert full-width katakana to half-width
		$string = mb_convert_kana( $string, 'c', 'UTF-8' );
		// Trim whitespace
		$string = trim( $string );
		return $string;
	}

	/**
	 * Get color by hex value
	 *
	 * @param string $hex Hex color value
	 * @return array|false Color data or false
	 */
	public static function get_color_by_hex( $hex ) {
		$hex = strtoupper( $hex );
		if ( strpos( $hex, '#' ) !== 0 ) {
			$hex = '#' . $hex;
		}

		$all_colors = self::get_colors();
		foreach ( $all_colors as $color ) {
			if ( strtoupper( $color['hex'] ) === $hex ) {
				return $color;
			}
		}

		return false;
	}
}
