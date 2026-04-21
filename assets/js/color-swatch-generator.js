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
		},

		// Handle number of colors change
		handleNumColorsChange(e) {
			const numColors = parseInt($(e.target).val());
			this.renderColorInputs(numColors);
			this.updatePreview();
		},

		// Render color input fields
		renderColorInputs(numColors) {
			this.$colorInputsContainer.html('');

			for (let i = 1; i <= numColors; i++) {
				const html = `
					<div class="csg-color-input-group">
						<label>Color ${i}</label>
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
		},

		// Initialize color picker for new inputs
		initColorPickers() {
			$('.csg-color-picker').wpColorPicker({
				change: (e) => {
					const $input = $(e.target);
					const index = $input.data('index');
					const color = $input.val();
					
					if (this.isValidHex(color)) {
						this.updateColorSwatch(index, color);
						this.updatePreview();
					}
				},
				clear: () => {
					this.updatePreview();
				}
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
			const $input = $(`.csg-color-picker[data-index="${index}"]`);
			const $group = $input.closest('.csg-color-input-group');
			const $swatch = $group.find('.csg-color-swatch');
			$swatch.css('background-color', color);
		},

		// Handle color search
		handleColorSearch() {
			const searchTerm = this.$colorSearch.val().trim();

			if (!searchTerm) {
				this.showMessage('Please enter a color name or hex code', 'error');
				return;
			}

			this.showMessage('Searching...', 'info');

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
						this.showMessage('No colors found', 'error');
					}
				},
				error: () => {
					this.showMessage('Search failed', 'error');
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
			this.showMessage('Color added', 'success');
		},

		// Update preview
		updatePreview() {
			const numColors = parseInt($('input[name="num_colors"]:checked').val());
			const colors = [];

			for (let i = 1; i <= numColors; i++) {
				const color = $(`.csg-color-picker[data-index="${i}"]`).val();
				if (this.isValidHex(color)) {
					colors.push(color);
				} else {
					colors.push('#FFFFFF');
				}
			}

			const colorHeight = 250 / numColors;
			let previewHtml = '<div class="csg-preview-box" style="width: 250px; height: 250px; border: 1px solid #CCC;">';

			colors.forEach((color) => {
				previewHtml += `<div style="background-color: ${color}; width: 100%; height: ${colorHeight}px;"></div>`;
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
					this.showMessage(`Please enter a valid color for Color ${i}`, 'error');
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
					this.showMessage('Generation failed. Please try again.', 'error');
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
			const hex = color.replace('#', '');
			return /^[0-9A-Fa-f]{6}$/.test(hex);
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
		CSG.init();
		// Initialize color pickers after page load
		setTimeout(() => {
			CSG.initColorPickers();
		}, 100);
	});

})(jQuery);
