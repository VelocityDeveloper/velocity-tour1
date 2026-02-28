# Changelog

Semua perubahan penting pada theme ini dicatat di file ini.

## [2.1.0] - 2026-02-28

### Added
- Migrasi Customizer child ke native WordPress API (tanpa Kirki).
- Repeater native untuk `keunggulan_items` dan `home_galeri`.
- Helper icon Bootstrap (`inc/icons.php`) dengan pilihan icon dari parent theme.
- Helper media/thumbnail fallback (`inc/media.php`) dengan dukungan `no-image.webp`.
- Asset kontrol repeater Customizer (`css/customizer-repeater.css`, `js/customizer-repeater.js`).
- Default settings child ditambahkan lewat filter `justg_theme_default_settings`.

### Changed
- `page-home.php` disesuaikan agar memakai data helper/repeater yang sudah disanitasi.
- Output icon keunggulan dirapikan agar posisi icon tepat di tengah lingkaran.
- Search form disesuaikan ke Bootstrap 5 (`visually-hidden`, tanpa `input-group-append`).
- Enqueue stylesheet child dirapikan (tidak enqueue bootstrap-icons child, versi CSS pakai filemtime).
- CSS child dibersihkan dari override background hardcoded dan class BS4 yang tersisa.

### Fixed
- Potensi notice variabel WhatsApp di header (`inc/part-header.php`).
- Field customizer yang belum diisi/publish sekarang tetap punya fallback default yang konsisten.
