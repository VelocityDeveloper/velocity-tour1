<?php

/**
 * Enqueue child theme styles and scripts.
 */
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

/**
 * Load the parent style.css file
 *
 * @link http://codex.wordpress.org/Child_Themes
 */
if (!function_exists('justg_child_enqueue_parent_style')) {
    function justg_child_enqueue_parent_style()
    {
        // Dynamically get version number of the parent stylesheet (lets browsers re-cache your stylesheet when you update your theme)
        $parenthandle = 'parent-style';
        $theme = wp_get_theme();

        // Jika plugin tidak me-load CSS
        if (
            !wp_style_is('magnific-popup-styles', 'enqueued') &&
            !wp_style_is('magnific-popup-styles', 'registered')
        ) {
            wp_enqueue_style(
                'velocity-tour1-magnific-popup-styles',
                'https://cdnjs.cloudflare.com/ajax/libs/magnific-popup.js/1.0.0/magnific-popup.min.css',
                [],
                '1.0.0'
            );
        }

        // Jika plugin tidak me-load JS
        if (
            !wp_script_is('magnific-popup-script', 'enqueued') &&
            !wp_script_is('magnific-popup-script', 'registered')
        ) {
            wp_enqueue_script(
                'velocity-tour1-magnific-popup-script',
                'https://cdnjs.cloudflare.com/ajax/libs/magnific-popup.js/1.0.0/jquery.magnific-popup.min.js',
                ['jquery'],
                '1.0.0',
                true
            );
        }


        // Load the stylesheet
        wp_enqueue_style(
            $parenthandle,
            get_template_directory_uri() . '/style.css',
            array(),  // if the parent theme code has a dependency, copy it to here
            $theme->parent()->get('Version')
        );

        // load bootstrap-icons
        wp_enqueue_style(
            'velocity-tour1-bootstrap-icons',
            get_stylesheet_directory_uri() . '/css/bootstrap-icons.css',
            array(),
            filemtime(get_stylesheet_directory() . '/css/bootstrap-icons.css') // ini untuk cache busting
        );

        // $css_version = $theme->parent()->get('Version') . '.' . filemtime( get_stylesheet_directory() . '/css/custom.css' );
        $css_version = $theme->parent()->get('Version');
        wp_enqueue_style(
            'velocity-tour1-custom-style',
            get_stylesheet_directory_uri() . '/css/custom.css',
            array(),  // if the parent theme code has a dependency, copy it to here
            $css_version
        );

        wp_enqueue_style(
            'velocity-google-fonts',
            'https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap',
            false
        );

        wp_enqueue_style(
            'child-style',
            get_stylesheet_uri(),
            array($parenthandle),
            $theme->get('Version')
        );

        $js_version = $theme->parent()->get('Version') . '.' . filemtime(get_stylesheet_directory() . '/js/custom.js');
        wp_enqueue_script('justg-custom-scripts', get_stylesheet_directory_uri() . '/js/custom.js', array(), $js_version, true);
    }
}
