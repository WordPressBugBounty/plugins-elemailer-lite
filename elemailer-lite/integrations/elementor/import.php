<?php

namespace Elemailer_Lite\Integrations\Elementor;

defined('ABSPATH') || exit;

// If plugin - 'Elementor' not exist then return.
if ( ! class_exists( '\Elementor\Plugin' ) ) {
    return;
}

use Elementor\Plugin;
use Elementor\TemplateLibrary\Source_Local;

/**
 * Elementor template library local source.
 *
 * Elementor template library local source handler class is responsible for
 * handling local Elementor templates saved by the user locally on his site.
 *
 * @since 2.0.0 Added compatibility for Elemetnor v2.5.0
 */
class Import extends Source_Local {

    use \Elemailer_Lite\Traits\Singleton;

    /**
     * Import and update post meta.
     *
     * @since 1.0.5
     *
     * @param  integer $post_id Post ID.
     * @param  array   $data Elementor Data.
     *
     * @return array   $data Elementor Imported Data.
     */
    public function import( $post_id = 0, $data = array() ) {
        if ( ! empty( $post_id ) && ! empty( $data ) ) {

            // Ignore placeholder when import. @since 1.0.6
            add_filter( 'elementor/utils/get_placeholder_image_src', [ $this, 'ignore_placeholder' ], 35 );

            $data = wp_json_encode( $data, true );
            $data = json_decode( $data, true );
            // Import the data.
            $data = $this->process_export_import_content( $data, 'on_import' );

            // Update processed meta.
            update_metadata( 'post', $post_id, '_elementor_data', $data );

            // !important, Clear the cache after images import.
            Plugin::$instance->posts_css_manager->clear_cache();

            return $data;
        }

        return array();
    }

    /**
     * Ignore placeholder when import
     *
     * @since 1.0.6
     *
     * @param string $url Placeholder image url.
     *
     * @return null
     */
    public function ignore_placeholder( $url ) {
        return '';
    }
}