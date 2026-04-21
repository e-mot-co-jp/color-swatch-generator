<?php
/**
 * Admin Page Class
 * Displays the color swatch generator interface in the WordPress admin
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class CSG_Admin_Page {

	/**
	 * Initialize admin page
	 */
	public static function init() {
		add_action( 'admin_menu', array( __CLASS__, 'add_admin_menu' ) );
		add_action( 'admin_enqueue_scripts', array( __CLASS__, 'enqueue_scripts_styles' ) );
	}

	/**
	 * Add admin menu
	 */
	public static function add_admin_menu() {
		add_menu_page(
			__( 'Color Swatch Generator', 'color-swatch-generator' ),
			__( 'Color Swatches', 'color-swatch-generator' ),
			'upload_files',
			'color-swatch-generator',
			array( __CLASS__, 'render_page' ),
			'dashicons-image-filter',
			76
		);
	}

	/**
	 * Enqueue scripts and styles
	 */
	public static function enqueue_scripts_styles( $hook ) {
		// Only enqueue on our plugin page
		if ( $hook !== 'toplevel_page_color-swatch-generator' ) {
			return;
		}

		// Enqueue WordPress color picker
		wp_enqueue_style( 'wp-color-picker' );
		wp_enqueue_script( 'wp-color-picker' );

		// Enqueue our styles
		wp_enqueue_style(
			'csg-styles',
			CSG_PLUGIN_URL . 'assets/css/color-swatch-generator.css',
			array(),
			CSG_VERSION
		);

		// Enqueue our scripts
		wp_enqueue_script(
			'csg-scripts',
			CSG_PLUGIN_URL . 'assets/js/color-swatch-generator.js',
			array( 'jquery', 'wp-color-picker' ),
			CSG_VERSION,
			true
		);

		// Localize script
		wp_localize_script(
			'csg-scripts',
			'csgData',
			array(
				'ajaxUrl'   => admin_url( 'admin-ajax.php' ),
				'nonce'     => wp_create_nonce( 'csg_nonce' ),
				'i18n'      => array(
					'selectColors'    => __( 'Select Colors', 'color-swatch-generator' ),
					'search'          => __( 'Search', 'color-swatch-generator' ),
					'generate'        => __( 'Generate Swatch', 'color-swatch-generator' ),
					'generating'      => __( 'Generating...', 'color-swatch-generator' ),
					'uploading'       => __( 'Uploading...', 'color-swatch-generator' ),
					'success'         => __( 'Success!', 'color-swatch-generator' ),
					'error'           => __( 'Error', 'color-swatch-generator' ),
					'uploadSuccess'   => __( 'Color swatch uploaded to media library', 'color-swatch-generator' ),
					'enterHex'        => __( 'Enter hex color code', 'color-swatch-generator' ),
					'colorName'       => __( 'Color name or hex code', 'color-swatch-generator' ),
				),
			)
		);
	}

	/**
	 * Render admin page
	 */
	public static function render_page() {
		?>
		<div class="wrap csg-wrap">
			<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
			
			<div class="csg-container">
				<div class="csg-main">
					<div class="csg-section">
						<h2><?php esc_html_e( 'Color Swatch Settings', 'color-swatch-generator' ); ?></h2>

						<!-- Number of Colors Selection -->
						<div class="csg-form-group">
							<label><?php esc_html_e( 'Number of Colors', 'color-swatch-generator' ); ?></label>
							<div class="csg-radio-group">
								<label>
									<input type="radio" name="num_colors" value="1" checked>
									<?php esc_html_e( '1 Color', 'color-swatch-generator' ); ?>
								</label>
								<label>
									<input type="radio" name="num_colors" value="2">
									<?php esc_html_e( '2 Colors', 'color-swatch-generator' ); ?>
								</label>
								<label>
									<input type="radio" name="num_colors" value="3">
									<?php esc_html_e( '3 Colors', 'color-swatch-generator' ); ?>
								</label>
							</div>
						</div>

						<!-- Color Inputs -->
						<div class="csg-colors-container">
							<?php self::render_color_input( 1 ); ?>
						</div>

						<!-- Search and Suggestions -->
						<div class="csg-form-group">
							<label><?php esc_html_e( 'Color Name Search', 'color-swatch-generator' ); ?></label>
							<div class="csg-search-container">
								<input 
									type="text" 
									id="color-search" 
									placeholder="<?php esc_attr_e( 'Search by color name (English or Katakana)', 'color-swatch-generator' ); ?>"
									class="regular-text"
								>
								<button type="button" id="search-btn" class="button">
									<?php esc_html_e( 'Search', 'color-swatch-generator' ); ?>
								</button>
							</div>
							<div id="search-results" class="csg-search-results"></div>
						</div>

						<!-- Preview -->
						<div class="csg-form-group">
							<label><?php esc_html_e( 'Preview', 'color-swatch-generator' ); ?></label>
							<div id="color-preview" class="csg-preview"></div>
						</div>

						<!-- Generate Button -->
						<div class="csg-form-group">
							<button type="button" id="generate-btn" class="button button-primary button-large">
								<?php esc_html_e( 'Generate & Upload Swatch', 'color-swatch-generator' ); ?>
							</button>
						</div>

						<!-- Status Message -->
						<div id="status-message" class="notice" style="display:none;"></div>
					</div>
				</div>

				<!-- Sidebar with Information -->
				<div class="csg-sidebar">
					<div class="csg-info-box">
						<h3><?php esc_html_e( 'Information', 'color-swatch-generator' ); ?></h3>
						<ul>
							<li><?php esc_html_e( 'Size: 250 x 250 pixels', 'color-swatch-generator' ); ?></li>
							<li><?php esc_html_e( 'Format: GIF', 'color-swatch-generator' ); ?></li>
							<li><?php esc_html_e( 'Multiple colors are divided vertically', 'color-swatch-generator' ); ?></li>
							<li><?php esc_html_e( 'Supports hex color input', 'color-swatch-generator' ); ?></li>
						</ul>
					</div>
				</div>
			</div>
		</div>
		<?php
	}

	/**
	 * Render color input field
	 *
	 * @param int $index Color input index
	 */
	private static function render_color_input( $index ) {
		?>
		<div class="csg-color-input-group">
			<label><?php printf( esc_html__( 'Color %d', 'color-swatch-generator' ), $index ); ?></label>
			<input 
				type="text" 
				class="csg-color-picker" 
				data-index="<?php echo esc_attr( $index ); ?>"
				placeholder="#FF0000"
				value=""
			>
			<div class="csg-color-swatch" style="background-color: #FFFFFF; border: 1px solid #CCC;"></div>
		</div>
		<?php
	}
}
