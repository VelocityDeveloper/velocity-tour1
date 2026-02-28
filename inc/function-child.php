<?php

/**
 * Fuction yang digunakan di theme ini.
 */
if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly.
}

add_action('after_setup_theme', 'velocitychild_theme_setup', 9);
add_action('customize_register', 'velocitychild_customize_register', 30);
add_action('customize_controls_enqueue_scripts', 'velocitychild_customize_control_assets');
add_filter('justg_theme_default_settings', 'velocitychild_default_settings', 20);

if (!function_exists('velocitychild_default_settings')) {
	/**
	 * Extend parent default theme settings with child customizer keys.
	 *
	 * @param array<string, mixed> $defaults Parent defaults.
	 * @return array<string, mixed>
	 */
	function velocitychild_default_settings($defaults) {
		$child_defaults = array(
			'kontak_telepon'    => '',
			'kontak_whatsapp'   => '',
			'kontak_email'      => '',
			'home_banner'       => '',
			'home_banner1'      => '',
			'home_banner2'      => '',
			'home_judul'        => '',
			'home_keterangan'   => '',
			'home_teks_tombol'  => 'Pesan Sekarang',
			'home_link'         => '',
			'judul_keunggulan'  => 'Mengapa Memilih Kami?',
			'keunggulan_items'  => array(),
			'home_judul_galeri' => 'Galeri Foto',
			'home_galeri'       => array(),
		);

		return array_merge($defaults, $child_defaults);
	}
}

if (!function_exists('velocitychild_customize_control_assets')) {
	/**
	 * Enqueue assets for customizer repeater control.
	 *
	 * @return void
	 */
	function velocitychild_customize_control_assets() {
		$theme   = wp_get_theme();
		$version = $theme ? $theme->get('Version') : '1.0.0';

		$repeater_css_path = get_stylesheet_directory() . '/css/customizer-repeater.css';
		$repeater_js_path  = get_stylesheet_directory() . '/js/customizer-repeater.js';
		$repeater_css_ver  = file_exists($repeater_css_path) ? filemtime($repeater_css_path) : $version;
		$repeater_js_ver   = file_exists($repeater_js_path) ? filemtime($repeater_js_path) : $version;

		wp_enqueue_media();

		wp_enqueue_style(
			'velocitychild-customizer-repeater',
			get_stylesheet_directory_uri() . '/css/customizer-repeater.css',
			array(),
			$repeater_css_ver
		);

		wp_enqueue_script(
			'velocitychild-customizer-repeater',
			get_stylesheet_directory_uri() . '/js/customizer-repeater.js',
			array('jquery', 'customize-controls', 'media-editor', 'media-views'),
			$repeater_js_ver,
			true
		);
	}
}

if (!function_exists('velocitychild_decode_repeater_value')) {
	/**
	 * Decode repeater value if stored as JSON string.
	 *
	 * @param mixed $value Repeater raw value.
	 * @return array<int, array<string, mixed>>
	 */
	function velocitychild_decode_repeater_value($value) {
		if (is_string($value)) {
			$decoded = json_decode($value, true);
			if (json_last_error() === JSON_ERROR_NONE) {
				$value = $decoded;
			}
		}

		if (!is_array($value)) {
			return array();
		}

		return $value;
	}
}

if (!function_exists('velocitychild_resolve_image_value_to_url')) {
	/**
	 * Resolve stored image value to URL.
	 *
	 * @param mixed  $value Image ID or URL.
	 * @param string $size  Image size.
	 * @return string
	 */
	function velocitychild_resolve_image_value_to_url($value, $size = 'full') {
		if (is_numeric($value)) {
			$image_id = absint($value);
			if ($image_id > 0) {
				$image_url = wp_get_attachment_image_url($image_id, $size);
				if ($image_url) {
					return $image_url;
				}
			}
		}

		$image_url = esc_url_raw((string) $value);
		if (!empty($image_url)) {
			return $image_url;
		}

		return '';
	}
}

if (!function_exists('velocitychild_sanitize_home_keterangan')) {
	/**
	 * Sanitize home banner description.
	 *
	 * @param mixed $value Raw value.
	 * @return string
	 */
	function velocitychild_sanitize_home_keterangan($value) {
		return wp_kses_post((string) $value);
	}
}

if (!function_exists('velocitychild_sanitize_home_link')) {
	/**
	 * Sanitize banner CTA link.
	 *
	 * @param mixed $value Raw value.
	 * @return string
	 */
	function velocitychild_sanitize_home_link($value) {
		return esc_url_raw((string) $value);
	}
}

if (!function_exists('velocitychild_get_keunggulan_repeater_fields')) {
	/**
	 * Repeater fields for home advantages.
	 *
	 * @return array<string, array<string, mixed>>
	 */
	function velocitychild_get_keunggulan_repeater_fields() {
		return array(
			'icon' => array(
				'type'        => 'select',
				'label'       => __('Icon', 'justg'),
				'description' => sprintf(
					__('Referensi icon: <a href="%s" target="_blank" rel="noopener noreferrer">icons.getbootstrap.com</a>', 'justg'),
					esc_url('https://icons.getbootstrap.com/')
				),
				'default'     => velocitychild_get_default_service_icon(),
				'choices'     => velocitychild_get_bootstrap_icon_choices(),
			),
			'nama' => array(
				'type'    => 'text',
				'label'   => __('Nama', 'justg'),
				'default' => '',
			),
			'deskripsi' => array(
				'type'    => 'textarea',
				'label'   => __('Deskripsi', 'justg'),
				'default' => '',
			),
		);
	}
}

if (!function_exists('velocitychild_get_legacy_keunggulan_items')) {
	/**
	 * Backward compatibility for old keunggulan items.
	 *
	 * @return array<int, array<string, mixed>>
	 */
	function velocitychild_get_legacy_keunggulan_items() {
		$value = velocitytheme_option('keunggulan_items', array());
		return velocitychild_sanitize_keunggulan_items($value);
	}
}

if (!function_exists('velocitychild_sanitize_keunggulan_items')) {
	/**
	 * Sanitize home advantages repeater values.
	 *
	 * @param mixed $value Raw value.
	 * @return array<int, array<string, mixed>>
	 */
	function velocitychild_sanitize_keunggulan_items($value) {
		$items = velocitychild_decode_repeater_value($value);
		$clean = array();

		foreach ($items as $item) {
			if (!is_array($item)) {
				continue;
			}

			$icon      = isset($item['icon']) ? velocitychild_normalize_service_icon($item['icon']) : velocitychild_get_default_service_icon();
			$nama      = isset($item['nama']) ? sanitize_text_field((string) $item['nama']) : '';
			$deskripsi = isset($item['deskripsi']) ? wp_kses_post((string) $item['deskripsi']) : '';

			if ('' === $nama && '' === trim(wp_strip_all_tags($deskripsi)) && '' === $icon) {
				continue;
			}

			$clean[] = array(
				'icon'      => $icon,
				'nama'      => $nama,
				'deskripsi' => $deskripsi,
			);
		}

		return $clean;
	}
}

if (!function_exists('velocitychild_get_home_keunggulan_items')) {
	/**
	 * Get active advantages items with legacy fallback.
	 *
	 * @return array<int, array<string, mixed>>
	 */
	function velocitychild_get_home_keunggulan_items() {
		$items_raw = get_theme_mod('keunggulan_items', null);
		if (null === $items_raw) {
			$items = velocitychild_get_legacy_keunggulan_items();
		} else {
			$items = velocitychild_sanitize_keunggulan_items($items_raw);
		}

		return $items;
	}
}

if (!function_exists('velocitychild_get_gallery_repeater_fields')) {
	/**
	 * Repeater fields for home gallery.
	 *
	 * @return array<string, array<string, mixed>>
	 */
	function velocitychild_get_gallery_repeater_fields() {
		return array(
			'gambar' => array(
				'type'    => 'image',
				'label'   => __('Gambar Galeri', 'justg'),
				'default' => '',
			),
		);
	}
}

if (!function_exists('velocitychild_get_legacy_home_galeri_items')) {
	/**
	 * Backward compatibility for old gallery items.
	 *
	 * @return array<int, array<string, mixed>>
	 */
	function velocitychild_get_legacy_home_galeri_items() {
		$value = velocitytheme_option('home_galeri', array());
		return velocitychild_sanitize_home_galeri_items($value);
	}
}

if (!function_exists('velocitychild_sanitize_home_galeri_items')) {
	/**
	 * Sanitize home gallery repeater values.
	 *
	 * @param mixed $value Raw value.
	 * @return array<int, array<string, mixed>>
	 */
	function velocitychild_sanitize_home_galeri_items($value) {
		$items = velocitychild_decode_repeater_value($value);
		$clean = array();

		foreach ($items as $item) {
			if (!is_array($item) || !isset($item['gambar'])) {
				continue;
			}

			$gambar_raw = $item['gambar'];
			$gambar     = '';

			if (is_numeric($gambar_raw) && absint($gambar_raw) > 0) {
				$gambar = (string) absint($gambar_raw);
			} else {
				$gambar = esc_url_raw((string) $gambar_raw);
			}

			if ('' === $gambar) {
				continue;
			}

			$clean[] = array(
				'gambar' => $gambar,
			);
		}

		return $clean;
	}
}

if (!function_exists('velocitychild_get_home_gallery_items')) {
	/**
	 * Get active home gallery items with resolved image URLs.
	 *
	 * @return array<int, array<string, string>>
	 */
	function velocitychild_get_home_gallery_items() {
		$items_raw = get_theme_mod('home_galeri', null);
		if (null === $items_raw) {
			$items = velocitychild_get_legacy_home_galeri_items();
		} else {
			$items = velocitychild_sanitize_home_galeri_items($items_raw);
		}

		$output = array();
		foreach ($items as $item) {
			$gambar = isset($item['gambar']) ? $item['gambar'] : '';
			$url    = velocitychild_resolve_image_value_to_url($gambar, 'large');
			if ($url) {
				$output[] = array(
					'gambar_url' => $url,
				);
			}
		}

		return $output;
	}
}

if (!class_exists('Velocitychild_Repeater_Control') && class_exists('WP_Customize_Control')) {
	/**
	 * Generic repeater control for WordPress customizer.
	 */
	class Velocitychild_Repeater_Control extends WP_Customize_Control {
		/**
		 * Control type.
		 *
		 * @var string
		 */
		public $type = 'velocity_repeater';

		/**
		 * Repeater fields definition.
		 *
		 * @var array<string, array<string, string>>
		 */
		public $fields = array();

		/**
		 * Item label for repeater entries.
		 *
		 * @var string
		 */
		public $item_label = '';

		/**
		 * Add button label.
		 *
		 * @var string
		 */
		public $add_button_label = '';

		/**
		 * Constructor.
		 *
		 * @param WP_Customize_Manager $manager Manager.
		 * @param string               $id      Control ID.
		 * @param array                $args    Control args.
		 * @param array                $options Options.
		 */
		public function __construct($manager, $id, $args = array(), $options = array()) {
			if (isset($args['fields'])) {
				$this->fields = (array) $args['fields'];
				unset($args['fields']);
			}
			if (isset($args['item_label'])) {
				$this->item_label = (string) $args['item_label'];
				unset($args['item_label']);
			}
			if (isset($args['add_button_label'])) {
				$this->add_button_label = (string) $args['add_button_label'];
				unset($args['add_button_label']);
			}
			parent::__construct($manager, $id, $args);
		}

		/**
		 * Render control content.
		 *
		 * @return void
		 */
		protected function render_content() {
			if (empty($this->fields)) {
				return;
			}

			$value = $this->value();
			if (is_string($value)) {
				$decoded = json_decode($value, true);
				$value   = (json_last_error() === JSON_ERROR_NONE) ? $decoded : array();
			}

			if (!is_array($value)) {
				$value = array();
			}

			$encoded_value = wp_json_encode($value);
			if (empty($encoded_value)) {
				$encoded_value = '[]';
			}
			?>
			<div class="velocity-repeater-control">
				<?php if (!empty($this->label)) : ?>
					<span class="customize-control-title"><?php echo esc_html($this->label); ?></span>
				<?php endif; ?>
				<?php if (!empty($this->description)) : ?>
					<p class="description"><?php echo wp_kses_post($this->description); ?></p>
				<?php endif; ?>

				<div class="velocity-repeater" data-fields="<?php echo esc_attr(wp_json_encode($this->fields)); ?>" data-default-label="<?php echo esc_attr($this->item_label ? $this->item_label : __('Item', 'justg')); ?>">
					<input type="hidden" class="velocity-repeater-store" <?php $this->link(); ?> value="<?php echo esc_attr($encoded_value); ?>">
					<div class="velocity-repeater-items">
						<?php
						if (!empty($value)) {
							foreach ($value as $item) {
								echo $this->get_single_item_markup($item); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
							}
						}
						?>
					</div>
					<button type="button" class="button button-primary velocity-repeater-add"><?php echo esc_html($this->add_button_label ? $this->add_button_label : __('Tambah Item', 'justg')); ?></button>
					<script type="text/html" class="velocity-repeater-template">
						<?php echo $this->get_single_item_markup(array()); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					</script>
				</div>
			</div>
			<?php
		}

		/**
		 * Render a repeater item.
		 *
		 * @param array<string, mixed> $item_values Item value.
		 * @return string
		 */
		private function get_single_item_markup($item_values = array()) {
			ob_start();
			$summary = $this->item_label ? $this->item_label : __('Item', 'justg');
			?>
			<div class="velocity-repeater-item">
				<button type="button" class="velocity-repeater-toggle" aria-expanded="true">
					<span class="velocity-repeater-item-label"><?php echo esc_html($summary); ?></span>
					<span class="velocity-repeater-toggle-icon" aria-hidden="true"></span>
				</button>
				<div class="velocity-repeater-item-body">
					<?php foreach ($this->fields as $field_key => $field) :
						$field_type    = isset($field['type']) ? $field['type'] : 'text';
						$field_label   = isset($field['label']) ? $field['label'] : '';
						$field_default = isset($field['default']) ? $field['default'] : '';
						$field_desc    = isset($field['description']) ? $field['description'] : '';
						$field_value   = isset($item_values[$field_key]) ? $item_values[$field_key] : $field_default;

						if ('image' === $field_type) :
							$image_value = (string) $field_value;
							$image_id    = absint($field_value);
							$image_url   = '';

							if ($image_id > 0) {
								$image_url = wp_get_attachment_image_url($image_id, 'thumbnail');
							} elseif (filter_var($image_value, FILTER_VALIDATE_URL)) {
								$image_url = $image_value;
							}
							?>
							<div class="velocity-repeater-field">
								<span class="velocity-repeater-field-label"><?php echo esc_html($field_label); ?></span>
								<div class="velocity-repeater-image-field">
									<input type="hidden" data-field="<?php echo esc_attr($field_key); ?>" data-default="<?php echo esc_attr($field_default); ?>" value="<?php echo esc_attr($image_value); ?>">
									<div class="velocity-repeater-image-preview<?php echo $image_url ? ' has-image' : ''; ?>">
										<?php if ($image_url) : ?>
											<img src="<?php echo esc_url($image_url); ?>" alt="">
										<?php endif; ?>
									</div>
									<div class="velocity-repeater-image-actions">
										<button type="button" class="button velocity-repeater-media-select"><?php esc_html_e('Pilih Gambar', 'justg'); ?></button>
										<button type="button" class="button-link button-link-delete velocity-repeater-media-remove"><?php esc_html_e('Hapus', 'justg'); ?></button>
									</div>
								</div>
								<?php if (!empty($field_desc)) : ?>
									<span class="description customize-control-description"><?php echo wp_kses_post($field_desc); ?></span>
								<?php endif; ?>
							</div>
						<?php elseif ('textarea' === $field_type) : ?>
							<label class="velocity-repeater-field">
								<span class="velocity-repeater-field-label"><?php echo esc_html($field_label); ?></span>
								<textarea data-field="<?php echo esc_attr($field_key); ?>" data-default="<?php echo esc_attr($field_default); ?>"><?php echo esc_textarea((string) $field_value); ?></textarea>
								<?php if (!empty($field_desc)) : ?>
									<span class="description customize-control-description"><?php echo wp_kses_post($field_desc); ?></span>
								<?php endif; ?>
							</label>
						<?php elseif ('select' === $field_type) : ?>
							<?php $choices = isset($field['choices']) && is_array($field['choices']) ? $field['choices'] : array(); ?>
							<label class="velocity-repeater-field">
								<span class="velocity-repeater-field-label"><?php echo esc_html($field_label); ?></span>
								<select data-field="<?php echo esc_attr($field_key); ?>" data-default="<?php echo esc_attr($field_default); ?>">
									<?php foreach ($choices as $choice_value => $choice_label) : ?>
										<option value="<?php echo esc_attr((string) $choice_value); ?>" <?php selected((string) $field_value, (string) $choice_value); ?>><?php echo esc_html((string) $choice_label); ?></option>
									<?php endforeach; ?>
								</select>
								<?php if (!empty($field_desc)) : ?>
									<span class="description customize-control-description"><?php echo wp_kses_post($field_desc); ?></span>
								<?php endif; ?>
							</label>
						<?php else : ?>
							<label class="velocity-repeater-field">
								<span class="velocity-repeater-field-label"><?php echo esc_html($field_label); ?></span>
								<input type="<?php echo esc_attr($field_type); ?>" data-field="<?php echo esc_attr($field_key); ?>" data-default="<?php echo esc_attr($field_default); ?>" value="<?php echo esc_attr((string) $field_value); ?>">
								<?php if (!empty($field_desc)) : ?>
									<span class="description customize-control-description"><?php echo wp_kses_post($field_desc); ?></span>
								<?php endif; ?>
							</label>
						<?php endif; ?>
					<?php endforeach; ?>
					<div class="velocity-repeater-actions">
						<button type="button" class="button velocity-repeater-clone"><?php esc_html_e('Clone', 'justg'); ?></button>
						<button type="button" class="button button-secondary velocity-repeater-remove"><?php esc_html_e('Hapus', 'justg'); ?></button>
					</div>
				</div>
			</div>
			<?php
			return ob_get_clean();
		}
	}
}

if (!function_exists('velocitychild_customize_register')) {
	/**
	 * Child theme customizer settings without Kirki.
	 *
	 * @param WP_Customize_Manager $wp_customize Customizer manager.
	 * @return void
	 */
	function velocitychild_customize_register(WP_Customize_Manager $wp_customize) {
		$textdomain = 'justg';

		if (!$wp_customize->get_panel('panel_velocity')) {
			$wp_customize->add_panel(
				'panel_velocity',
				array(
					'priority'    => 10,
					'title'       => esc_html__('Velocity Theme', $textdomain),
					'description' => '',
				)
			);
		}

		$site_identity_section = $wp_customize->get_section('title_tagline');
		if ($site_identity_section) {
			$site_identity_section->panel    = 'panel_velocity';
			$site_identity_section->priority = 10;
			$site_identity_section->title    = __('Site Identity', $textdomain);
		}

		$wp_customize->add_section(
			'section_header_kontak',
			array(
				'title'    => esc_html__('Kontak Header', $textdomain),
				'panel'    => 'panel_velocity',
				'priority' => 20,
			)
		);

		$wp_customize->add_setting(
			'kontak_telepon',
			array(
				'default'           => '',
				'sanitize_callback' => 'sanitize_text_field',
			)
		);
		$wp_customize->add_control(
			'kontak_telepon',
			array(
				'type'    => 'text',
				'label'   => esc_html__('Nomor Telepon', $textdomain),
				'section' => 'section_header_kontak',
			)
		);

		$wp_customize->add_setting(
			'kontak_whatsapp',
			array(
				'default'           => '',
				'sanitize_callback' => 'sanitize_text_field',
			)
		);
		$wp_customize->add_control(
			'kontak_whatsapp',
			array(
				'type'    => 'text',
				'label'   => esc_html__('Nomor WhatsApp', $textdomain),
				'section' => 'section_header_kontak',
			)
		);

		$wp_customize->add_setting(
			'kontak_email',
			array(
				'default'           => '',
				'sanitize_callback' => 'sanitize_text_field',
			)
		);
		$wp_customize->add_control(
			'kontak_email',
			array(
				'type'    => 'text',
				'label'   => esc_html__('Email', $textdomain),
				'section' => 'section_header_kontak',
			)
		);

		$wp_customize->add_section(
			'section_home_banner',
			array(
				'panel'    => 'panel_velocity',
				'title'    => esc_html__('Halaman Depan - Banner', $textdomain),
				'priority' => 30,
			)
		);

		$wp_customize->add_setting(
			'home_banner',
			array(
				'default'           => '',
				'sanitize_callback' => 'esc_url_raw',
			)
		);
		$wp_customize->add_control(
			new WP_Customize_Image_Control(
				$wp_customize,
				'home_banner',
				array(
					'label'   => esc_html__('Gambar Besar', $textdomain),
					'section' => 'section_home_banner',
				)
			)
		);

		$wp_customize->add_setting(
			'home_banner1',
			array(
				'default'           => '',
				'sanitize_callback' => 'esc_url_raw',
			)
		);
		$wp_customize->add_control(
			new WP_Customize_Image_Control(
				$wp_customize,
				'home_banner1',
				array(
					'label'   => esc_html__('Gambar Kecil 1', $textdomain),
					'section' => 'section_home_banner',
				)
			)
		);

		$wp_customize->add_setting(
			'home_banner2',
			array(
				'default'           => '',
				'sanitize_callback' => 'esc_url_raw',
			)
		);
		$wp_customize->add_control(
			new WP_Customize_Image_Control(
				$wp_customize,
				'home_banner2',
				array(
					'label'   => esc_html__('Gambar Kecil 2', $textdomain),
					'section' => 'section_home_banner',
				)
			)
		);

		$wp_customize->add_setting(
			'home_judul',
			array(
				'default'           => '',
				'sanitize_callback' => 'sanitize_text_field',
			)
		);
		$wp_customize->add_control(
			'home_judul',
			array(
				'type'    => 'text',
				'label'   => esc_html__('Judul', $textdomain),
				'section' => 'section_home_banner',
			)
		);

		$wp_customize->add_setting(
			'home_keterangan',
			array(
				'default'           => '',
				'sanitize_callback' => 'velocitychild_sanitize_home_keterangan',
			)
		);
		$wp_customize->add_control(
			'home_keterangan',
			array(
				'type'    => 'textarea',
				'label'   => esc_html__('Keterangan', $textdomain),
				'section' => 'section_home_banner',
			)
		);

		$wp_customize->add_setting(
			'home_teks_tombol',
			array(
				'default'           => 'Pesan Sekarang',
				'sanitize_callback' => 'sanitize_text_field',
			)
		);
		$wp_customize->add_control(
			'home_teks_tombol',
			array(
				'type'    => 'text',
				'label'   => esc_html__('Teks Tombol', $textdomain),
				'section' => 'section_home_banner',
			)
		);

		$wp_customize->add_setting(
			'home_link',
			array(
				'default'           => '',
				'sanitize_callback' => 'velocitychild_sanitize_home_link',
			)
		);
		$wp_customize->add_control(
			'home_link',
			array(
				'type'    => 'url',
				'label'   => esc_html__('Link Tujuan', $textdomain),
				'section' => 'section_home_banner',
			)
		);

		$wp_customize->add_section(
			'keunggulan_section',
			array(
				'title'    => esc_html__('Halaman Depan - Keunggulan', $textdomain),
				'priority' => 50,
				'panel'    => 'panel_velocity',
			)
		);

		$wp_customize->add_setting(
			'judul_keunggulan',
			array(
				'default'           => 'Mengapa Memilih Kami?',
				'sanitize_callback' => 'sanitize_text_field',
			)
		);
		$wp_customize->add_control(
			'judul_keunggulan',
			array(
				'type'    => 'text',
				'label'   => esc_html__('Judul', $textdomain),
				'section' => 'keunggulan_section',
			)
		);

		$wp_customize->add_setting(
			'keunggulan_items',
			array(
				'default'           => velocitychild_get_legacy_keunggulan_items(),
				'sanitize_callback' => 'velocitychild_sanitize_keunggulan_items',
			)
		);
		$wp_customize->add_control(
			new Velocitychild_Repeater_Control(
				$wp_customize,
				'keunggulan_items',
				array(
					'label'            => esc_html__('Daftar Keunggulan', $textdomain),
					'section'          => 'keunggulan_section',
					'priority'         => 10,
					'fields'           => velocitychild_get_keunggulan_repeater_fields(),
					'item_label'       => esc_html__('Keunggulan', $textdomain),
					'add_button_label' => esc_html__('Tambah Keunggulan', $textdomain),
				)
			)
		);

		$wp_customize->add_section(
			'section_home_galeri',
			array(
				'panel'    => 'panel_velocity',
				'title'    => esc_html__('Halaman Depan - Galeri Foto', $textdomain),
				'priority' => 60,
			)
		);

		$wp_customize->add_setting(
			'home_judul_galeri',
			array(
				'default'           => 'Galeri Foto',
				'sanitize_callback' => 'sanitize_text_field',
			)
		);
		$wp_customize->add_control(
			'home_judul_galeri',
			array(
				'type'    => 'text',
				'label'   => esc_html__('Judul', $textdomain),
				'section' => 'section_home_galeri',
			)
		);

		$wp_customize->add_setting(
			'home_galeri',
			array(
				'default'           => velocitychild_get_legacy_home_galeri_items(),
				'sanitize_callback' => 'velocitychild_sanitize_home_galeri_items',
			)
		);
		$wp_customize->add_control(
			new Velocitychild_Repeater_Control(
				$wp_customize,
				'home_galeri',
				array(
					'label'            => esc_html__('Galeri Foto', $textdomain),
					'section'          => 'section_home_galeri',
					'priority'         => 10,
					'fields'           => velocitychild_get_gallery_repeater_fields(),
					'item_label'       => esc_html__('Foto', $textdomain),
					'add_button_label' => esc_html__('Tambah Foto', $textdomain),
				)
			)
		);

		// Remove unused parent/legacy panels and controls.
		$wp_customize->remove_panel('global_panel');
		$wp_customize->remove_panel('panel_header');
		$wp_customize->remove_panel('panel_footer');
		$wp_customize->remove_panel('panel_antispam');
		$wp_customize->remove_control('display_header_text');
		$wp_customize->remove_section('header_image');
		$wp_customize->remove_section('header_section');
		$wp_customize->remove_section('section_colorvelocity');
	}
}

function velocitychild_theme_setup()
{

	// Load justg_child_enqueue_parent_style after theme setup
	add_action('wp_enqueue_scripts', 'justg_child_enqueue_parent_style', 20);

	//remove action from Parent Theme
	remove_action('justg_header', 'justg_header_menu');
	remove_action('justg_do_footer', 'justg_the_footer_open');
	remove_action('justg_do_footer', 'justg_the_footer_content');
	remove_action('justg_do_footer', 'justg_the_footer_close');
	remove_theme_support('widgets-block-editor');
}


///remove breadcrumbs
add_action('wp_head', function () {
	if (!is_single()) {
		remove_action('justg_before_title', 'justg_breadcrumb');
	}
});

if (!function_exists('justg_header_open')) {
    function justg_header_open()
    {
        echo '<header class="bg-white shadow shadow-sm" id="wrapper-header" itemscope itemtype="http://schema.org/WebSite">';
    }
}
if (!function_exists('justg_header_close')) {
    function justg_header_close()
    {
        echo '</header>';
    }
}

///add action builder part
add_action('justg_header', 'justg_header_berita');
function justg_header_berita()
{
	require_once(get_stylesheet_directory() . '/inc/part-header.php');
}
add_action('justg_do_footer', 'justg_footer_berita');
function justg_footer_berita()
{
	require_once(get_stylesheet_directory() . '/inc/part-footer.php');
}

// excerpt more
if ( ! function_exists( 'velocity_custom_excerpt_more' ) ) {
	function velocity_custom_excerpt_more( $more ) {
		return '...';
	}
}
add_filter( 'excerpt_more', 'velocity_custom_excerpt_more' );

// excerpt length
function velocity_excerpt_length($length){
	return 40;
}
add_filter('excerpt_length','velocity_excerpt_length');


//register widget
add_action('widgets_init', 'justg_widgets_init', 20);
if (!function_exists('justg_widgets_init')) {
	function justg_widgets_init()
	{
		$text_theme = 'justg';
		$before_widget = '<aside id="%1$s" class="widget %2$s">';
		$after_widget = '</aside>';
		$before_title = '<h6 class="widget-title position-relative mb-3">';
		$after_title = '</h6>';
		register_sidebar(
			array(
				'name'          => __('Main Sidebar', $text_theme),
				'id'            => 'main-sidebar',
				'description'   => __('Main sidebar widget area', $text_theme),
				'before_widget' => $before_widget,
				'after_widget'  => $after_widget,
				'before_title'  => $before_title,
				'after_title'   => $after_title,
				'show_in_rest'   => false,
			)
		);
		// Register footer widget area
		register_sidebar(
			array(
				'name'          => __( 'Footer Widget Area 1', 'justg' ),
				'id'            => 'footer-widget-1',
				'description'   => __( '', 'justg' ),
				'before_widget' => '<aside id="%1$s" class="mb-4 widget %2$s">',
				'after_widget'  => '</aside>',
				'before_title'  => '<h6 class="widget-title"><span>',
				'after_title'   => '</span></h6>',
			)
		);
		register_sidebar(
			array(
				'name'          => __( 'Footer Widget Area 2', 'justg' ),
				'id'            => 'footer-widget-2',
				'description'   => __( '', 'justg' ),
				'before_widget' => '<aside id="%1$s" class="mb-4 widget %2$s">',
				'after_widget'  => '</aside>',
				'before_title'  => '<h6 class="widget-title"><span>',
				'after_title'   => '</span></h6>',
			)
		);
		register_sidebar(
			array(
				'name'          => __( 'Footer Widget Area 3', 'justg' ),
				'id'            => 'footer-widget-3',
				'description'   => __( '', 'justg' ),
				'before_widget' => '<aside id="%1$s" class="mb-4 widget %2$s">',
				'after_widget'  => '</aside>',
				'before_title'  => '<h6 class="widget-title"><span>',
				'after_title'   => '</span></h6>',
			)
		);
	}
}
if (!function_exists('justg_right_sidebar_check')) {
	function justg_right_sidebar_check()
	{
		if (is_singular('fl-builder-template')) {
			return;
		}
		if (!is_active_sidebar('main-sidebar')) {
			return;
		}
		echo '<div class="right-sidebar velocity-widget widget-area col-sm-12 col-md-4 order-3" id="right-sidebar" role="complementary">';
		do_action('justg_before_main_sidebar');
		dynamic_sidebar('main-sidebar');
		do_action('justg_after_main_sidebar');
		echo '</div>';
	}
}
