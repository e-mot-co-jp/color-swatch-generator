(function($) {
	'use strict';

	const CSG = {
		// Configuration
		maxColors: 3,
		colors: [],
		allColors: [],

		// Initialize
		init() {
			this.cacheElements();
			this.bindEvents();
			this.loadColorSuggestions();
		},

		// Cache DOM elements
		cacheElements() {
			this.$numColorsRadios = $('input[name="num_colors"]');
			this.$colorInputsContainer = $('.csg-colors-container');
			this.$colorSearch = $('#color-search');
			this.$searchBtn = $('#search-btn');
			this.$searchResults = $('#search-results');
			this.$generateBtn = $('#generate-btn');
			this.$statusMessage = $('#status-message');
			this.$colorPreview = $('#color-preview');
		},

		// Bind event handlers
		bindEvents() {
			this.$numColorsRadios.on('change', (e) => this.handleNumColorsChange(e));
			this.$searchBtn.on('click', () => this.handleColorSearch());
			this.$colorSearch.on('keypress', (e) => {
				if (e.which === 13) { // Enter key
					this.handleColorSearch();
					return false;
				}
			});
			this.$generateBtn.on('click', () => this.handleGenerateSwatch());
			$(document).on('change', '.csg-color-picker', (e) => this.handleColorChange(e));
			$(document).on('input', '.csg-color-picker', (e) => {
				const $input = $(e.target);
				const index = $input.data('index');
				const color = $input.val();
				this.updateColorSwatch(index, color);
				this.updatePreview();
			});
		},

		// Handle number of colors change
		handleNumColorsChange(e) {
			const numColors = parseInt($(e.target).val());
			this.renderColorInputs(numColors);
			setTimeout(() => {
				this.updatePreview();
			}, 200);
		},

		// Render color input fields
		renderColorInputs(numColors) {
			this.$colorInputsContainer.html('');

			for (let i = 1; i <= numColors; i++) {
				const html = `
					<div class="csg-color-input-group">
						<label><?php esc_html_e( '色', 'color-swatch-generator' ); ?> ${i}</label>
						<input 
							type="text" 
							class="csg-color-picker" 
							data-index="${i}"
							placeholder="#FF0000"
							value=""
						>
						<div class="csg-color-swatch" style="background-color: #FFFFFF; border: 1px solid #CCC;"></div>
					</div>
				`;
				this.$colorInputsContainer.append(html);
			}

			// Initialize color pickers
			this.initColorPickers();
			
			// Update preview after small delay to ensure pickers are ready
			setTimeout(() => {
				this.updatePreview();
			}, 100);
		},

		// Initialize color picker for new inputs
		initColorPickers() {
			const self = this;
			$('.csg-color-picker').each((index, element) => {
				const $input = $(element);
				
				// Skip if already initialized
				if ($input.hasClass('wp-color-picker')) {
					return;
				}
				
				$input.wpColorPicker({
					change: (e, ui) => {
						const $target = $(e.target);
						const idx = $target.data('index');
						const color = $target.val();
						
						// Update swatch
						self.updateColorSwatch(idx, color);
						
						// Update preview after a small delay
						setTimeout(() => {
							self.updatePreview();
						}, 50);
					},
					clear: () => {
						self.updatePreview();
					},
					palettes: ['#FF0000', '#FFA500', '#FFFF00', '#00FF00', '#0000FF', '#800080', '#FFFFFF', '#000000']
				});
			});
		},

		// Handle color change
		handleColorChange(e) {
			const $input = $(e.target);
			const index = $input.data('index');
			const color = $input.val();

			if (this.isValidHex(color)) {
				this.updateColorSwatch(index, color);
				this.updatePreview();
			}
		},

		// Update color swatch preview
		updateColorSwatch(index, color) {
			let normalizedColor = color ? color.toString().trim() : '#FFFFFF';
			
			// Add # if missing
			if (normalizedColor && !normalizedColor.startsWith('#')) {
				normalizedColor = '#' + normalizedColor;
			}
			
			// Ensure valid hex format, otherwise use white
			if (!this.isValidHex(normalizedColor)) {
				normalizedColor = '#FFFFFF';
			}
			
			const $input = $(`.csg-color-picker[data-index="${index}"]`);
			const $group = $input.closest('.csg-color-input-group');
			const $swatch = $group.find('.csg-color-swatch');
			
			if ($swatch.length) {
				$swatch.css('background-color', normalizedColor);
			}
		},

		// Handle color search
		handleColorSearch() {
			const searchTerm = this.$colorSearch.val().trim();

			if (!searchTerm) {
					this.showMessage('色の名前またはHexコードを入力してください', 'error');
				return;
			}

			this.showMessage('検索中...', 'info');

			$.ajax({
				url: csgData.ajaxUrl,
				type: 'POST',
				data: {
					action: 'csg_search_colors',
					nonce: csgData.nonce,
					search: searchTerm
				},
				success: (response) => {
					if (response.success) {
						this.displaySearchResults(response.data.colors);
					} else {
						this.showMessage('色が見つかりません', 'error');
					}
				},
				error: () => {
					this.showMessage('検索に失敗しました', 'error');
				}
			});
		},

		// Display search results
		displaySearchResults(colors) {
			this.$searchResults.html('');

			if (!colors || colors.length === 0) {
				this.showMessage('No colors found', 'error');
				return;
			}

			const $resultsList = $('<div class="csg-results-list"></div>');

			colors.forEach((color) => {
				const names = color.names.join(', ');
				const $item = $(`
					<div class="csg-color-result" style="background-color: ${color.hex};">
						<div class="csg-color-result-info">
							<strong>${color.hex}</strong><br>
							<small>${names}</small>
						</div>
						<button type="button" class="button button-small csg-select-color" data-color="${color.hex}">
							Select
						</button>
					</div>
				`);

				$item.on('click', '.csg-select-color', (e) => {
					e.preventDefault();
					this.addColorToInput($(e.target).data('color'));
				});

				$resultsList.append($item);
			});

			this.$searchResults.append($resultsList);
		},

		// Add color to color input
		addColorToInput(color) {
			// Get first empty color input or first input
			let $targetInput = $('.csg-color-picker').filter(function() {
				return !$(this).val();
			}).first();

			// If all inputs are filled, replace the first one
			if ($targetInput.length === 0) {
				$targetInput = $('.csg-color-picker').first();
			}

			$targetInput.val(color).trigger('change');
			this.$colorSearch.val('');
			this.$searchResults.html('');
			this.showMessage('色を追加しました', 'success');
		},

		// Update preview
		updatePreview() {
			const numColors = parseInt($('input[name="num_colors"]:checked').val());
			const colors = [];

			for (let i = 1; i <= numColors; i++) {
				let color = $(`.csg-color-picker[data-index="${i}"]`).val().trim();
				
				// Normalize color value
				if (color && !color.startsWith('#')) {
					color = '#' + color;
				}
				
				if (color && this.isValidHex(color)) {
					// Ensure color is 6-char hex with #
					if (color.length === 4) { // 3-char hex like #RGB
						color = '#' + color.substring(1).split('').map(x => x + x).join('');
					}
					colors.push(color);
				} else {
					colors.push('#FFFFFF');
				}
			}

			const totalWidth = 250;
			const colorWidth = Math.floor(totalWidth / numColors);
			
			// Build preview HTML with horizontal (left-right) split - using flex-direction: row
			let previewHtml = '<div class="csg-preview-box" style="display: flex; flex-direction: row; width: 250px; height: 250px; border: 1px solid #CCC; margin: 0 auto; box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15); border-radius: 3px; overflow: hidden;">';

			colors.forEach((color, index) => {
				const isLast = (index === colors.length - 1);
				// Last color takes remaining width to avoid rounding errors
				const width = isLast ? (totalWidth - (colorWidth * index)) : colorWidth;
				previewHtml += `<div style="background-color: ${color}; width: ${width}px; height: 100%; display: block; flex: 0 0 ${width}px;"></div>`;
			});

			previewHtml += '</div>';
			this.$colorPreview.html(previewHtml);
		},

		// Handle swatch generation
		handleGenerateSwatch() {
			const numColors = parseInt($('input[name="num_colors"]:checked').val());
			const colors = [];

			for (let i = 1; i <= numColors; i++) {
				const color = $(`.csg-color-picker[data-index="${i}"]`).val();
				if (!this.isValidHex(color)) {
					this.showMessage(`色 ${i} に有効な色コードを入力してください`, 'error');
					return;
				}
				colors.push(color);
			}

			this.$generateBtn.prop('disabled', true).text(csgData.i18n.generating);
			this.showMessage(csgData.i18n.uploading, 'info');

			$.ajax({
				url: csgData.ajaxUrl,
				type: 'POST',
				data: {
					action: 'csg_generate_swatch',
					nonce: csgData.nonce,
					colors: colors,
					num_colors: numColors
				},
				success: (response) => {
					if (response.success) {
						const attachment = response.data;
						const message = `
							${csgData.i18n.success}: ${attachment.title}<br>
							<a href="${attachment.url}" target="_blank" class="button">View Image</a>
							<a href="${csgData.ajaxUrl.replace('admin-ajax.php', '')}upload.php?item=${attachment.attachment_id}" target="_blank" class="button">View in Media</a>
						`;
						this.showMessage(message, 'success', false);
					} else {
						this.showMessage(response.data.message || csgData.i18n.error, 'error');
					}
				},
				error: () => {
					this.showMessage('GIF生成に失敗しました。もう一度お試しください。', 'error');
				},
				complete: () => {
					this.$generateBtn.prop('disabled', false).text(csgData.i18n.generate);
				}
			});
		},

		// Load color suggestions
		loadColorSuggestions() {
			$.ajax({
				url: csgData.ajaxUrl,
				type: 'POST',
				data: {
					action: 'csg_get_color_suggestions',
					nonce: csgData.nonce
				},
				success: (response) => {
					if (response.success) {
						this.allColors = response.data.colors;
					}
				}
			});
		},

		// Validate hex color
		isValidHex(color) {
			if (!color) return false;
			const hex = color.toString().replace('#', '').trim();
			// 3文字または6文字のhexコードを検証
			return /^[0-9A-Fa-f]{3}$|^[0-9A-Fa-f]{6}$/.test(hex);
		},

		// Show message
		showMessage(message, type = 'info', isHtml = true) {
			this.$statusMessage
				.removeClass('notice-success notice-error notice-info notice-warning')
				.addClass(`notice-${type}`)
				.html(`<p>${message}</p>`)
				.show();

			// Auto-hide after 5 seconds if not error
			if (type !== 'error') {
				setTimeout(() => {
					this.$statusMessage.slideUp();
				}, 5000);
			}
		}
	};

	// Initialize on document ready
	$(document).ready(function() {
		// Initialize color pickers first
		CSG.initColorPickers();
		
		// Initialize the rest of the UI
		CSG.init();
		
		// Update preview on initial load
		setTimeout(() => {
			CSG.updatePreview();
		}, 200);
	});

})(jQuery);
