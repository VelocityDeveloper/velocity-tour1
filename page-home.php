<?php

/**
 * Template Name: Home Template
 *
 * Template for displaying a page just with the header and footer area and a "naked" content area in between.
 * Good for landingpages and other types of pages where you want to add a lot of custom markup.
 *
 * @package justg
 */

get_header();
$home_judul = velocitytheme_option('home_judul');
$home_keterangan = velocitytheme_option('home_keterangan');
$home_teks_tombol = velocitytheme_option('home_teks_tombol');
$home_link = velocitytheme_option('home_link');
$home_banner = velocitytheme_option('home_banner');
$gambar_kecil1 = velocitytheme_option('home_banner1');
$gambar_kecil2 = velocitytheme_option('home_banner2');
$judul_keunggulan = velocitytheme_option('judul_keunggulan');
$keunggulan = velocitytheme_option('keunggulan_items');
$home_judul_galeri = velocitytheme_option('home_judul_galeri');
$home_galeri = velocitytheme_option('home_galeri');
?>

<div class="wrapper" id="page-wrapper">

  <div class="velocity-home-banner">
      <div class="row align-items-center">
        <div class="col-12 col-md-6 col-xl-7 velocity-home-banner-text text-center text-md-start mb-4 mb-md-0">
			<div class="velocity-banner-text-frame">
                <?php if(!empty($home_judul)){ ?>
                    <h1 class="velocity-home-banner-title mb-4"><?php echo $home_judul; ?></h1>
                <?php } if(!empty($home_keterangan)){ ?>
			        <p class="fs-5"><?php echo $home_keterangan; ?></p>
                <?php } ?>
                    <div class="pt-4">
                        <a class="btn btn-lg btn-outline-light px-5 py-3 text-uppercase fw-bold" href="<?php echo $home_link; ?>"><?php echo $home_teks_tombol; ?></a>
                    </div>
			</div>
        </div>
        <div class="col-12 col-md-6 col-xl-5 pt-4 pt-md-0 velocity-home-banner-image text-center">
			<div class="velocity-image-frame">
                <?php if(!empty($home_banner)){ ?>
				    <img class="image-1" src="<?php echo $home_banner; ?>">
                <?php } if(!empty($gambar_kecil1)){ ?>
				    <img class="image-2" src="<?php echo $gambar_kecil1; ?>" />
                <?php } if(!empty($gambar_kecil2)){ ?>
				    <img class="image-3" src="<?php echo $gambar_kecil2; ?>" />
                <?php } ?>
			</div>
        </div>
      </div>
  </div>


<?php
$args = [
    'post_type'      => 'paket-tour',
    'posts_per_page' => 3,
    'orderby'        => 'date',
    'order'          => 'DESC',
];

$query = new WP_Query($args);

if ($query->have_posts()) : ?>
    <div class="container py-5">
        <div class="row py-5 my-3">
            <h2 class="col-12 fs-2 fw-bold text-center mb-4">Paket Wisata</h2>
            <?php while ($query->have_posts()) : $query->the_post(); ?>
                <div class="col-md-4 mb-4">
                    <div class="card h-100 pb-5">
                        <?php if (has_post_thumbnail()) : ?>
                            <a href="<?php the_permalink(); ?>">
                                <div class="ratio ratio-1x1">
                                    <?php the_post_thumbnail('large', ['class' => 'card-img-top']); ?>
                                </div>
                            </a>
                        <?php endif; ?>
                        <div class="card-body pb-0">
                            <h3 class="card-title fs-5 fw-bold">
                                <a href="<?php the_permalink(); ?>" class="text-decoration-none text-dark">
                                    <?php the_title(); ?>
                                </a>
                            </h3>                            
                            <?php
                                $terms = get_the_terms(get_the_ID(), 'kategori-paket');
                                if ($terms && !is_wp_error($terms)) {
                                    $icon = '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="me-1 align-middle bi bi-tags" viewBox="0 0 16 16"> <path d="M3 2v4.586l7 7L14.586 9l-7-7zM2 2a1 1 0 0 1 1-1h4.586a1 1 0 0 1 .707.293l7 7a1 1 0 0 1 0 1.414l-4.586 4.586a1 1 0 0 1-1.414 0l-7-7A1 1 0 0 1 2 6.586z"/> <path d="M5.5 5a.5.5 0 1 1 0-1 .5.5 0 0 1 0 1m0 1a1.5 1.5 0 1 0 0-3 1.5 1.5 0 0 0 0 3M1 7.086a1 1 0 0 0 .293.707L8.75 15.25l-.043.043a1 1 0 0 1-1.414 0l-7-7A1 1 0 0 1 0 7.586V3a1 1 0 0 1 1-1z"/> </svg>';
                                    $term_links = [];
                                    foreach ($terms as $term) {
                                        $term_links[] = '<a class="text-secondary" href="' . esc_url(get_term_link($term)) . '">' . esc_html($term->name) . '</a>';
                                    }
                                    echo '<p class="card-text text-secondary">'.$icon.implode(', ', $term_links). '</p>';
                                }
                            ?>
                            <?php if (function_exists('velocity_tombol_pemesanan')) {
                                echo '<div class="text-end position-absolute bottom-0 end-0 p-3">';
                                    echo velocity_tombol_pemesanan();
                                echo '</div>';
                            } ?>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    </div>
<?php
    wp_reset_postdata();
endif;
?>

<?php if (!empty($keunggulan)) : ?>
<div class="bg-theme text-white velocity-keunggulan">
    <div class="container py-5">
        <div class="row py-5 my-3 text-center">
            <h2 class="col-12 fs-2 fw-bold text-center text-white mb-4"><?php echo $judul_keunggulan; ?></h2>
            <?php foreach ($keunggulan as $item) : ?>
                <div class="col-md-3 mb-4">
                    <div class="h-100 p-4 border rounded shadow-sm">
                        <?php if (!empty($item['icon'])) : ?>
                            <div class="mb-3">
                                <i class="bi bi-<?php echo esc_attr($item['icon']); ?> fs-1 color-theme d-inline-flex justify-content-center align-items-center rounded-circle bg-white"></i>
                            </div>
                        <?php endif; ?>
                        <?php if (!empty($item['nama'])) : ?>
                            <h3 class="h4 fw-bold mb-2 text-white"><?php echo esc_html($item['nama']); ?></h3>
                        <?php endif; ?>
                        <?php if (!empty($item['deskripsi'])) : ?>
                            <p class="text-white"><?php echo esc_html($item['deskripsi']); ?></p>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>
<?php endif; ?>


<?php if (!empty($home_galeri)) : ?>
<div class="container py-5">
    <div class="py-5 my-3 text-center">
        <?php if(!empty($home_judul_galeri)){ ?>
            <h2 class="fs-2 fw-bold mb-4"><?php echo $home_judul_galeri; ?></h2>
        <?php } ?>
        <div class="row">
            <?php foreach ($home_galeri as $item) : ?>
            <div class="col-md-3 col-6 mb-4">
                <a href="<?php echo esc_url($item['gambar']); ?>" class="galeri-popup">
                <div class="ratio ratio-1x1">
                    <img src="<?php echo esc_url($item['gambar']); ?>" class="img-fluid rounded shadow-sm" />
                </div>
                </a>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        jQuery('.galeri-popup').magnificPopup({
            type: 'image',
            gallery: {
            enabled: true
            }
        });
    });
</script>
<?php endif; ?>


</div><!-- #page-wrapper -->

<?php
get_footer();
