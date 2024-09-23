<?php

namespace Elemailer_Lite\Integrations\Elementor;

if ( ! defined( 'ABSPATH' ) ) die( 'Forbidden' );

/**
 * elEmailer lite template builder library class.
 * Handles dynamically notices for lazy developers.
 *
 * @author elEmailer
 * @since 1.0.0
 */

class Library{

    use \Elemailer_Lite\Traits\Singleton;

    /**
     * Initialize library functionality
     *
     * @return void
     * @since 1.0.0
     */
    public function init() {
        add_action( 'elementor/editor/footer', [ $this, 'insert_templates' ] );
        add_action( 'elementor/editor/footer', [ $this, 'register_widget_scripts' ], 99 );
        add_action( 'elementor/editor/before_enqueue_scripts', [ $this, 'register_widget_styles' ] );
        add_action( 'elementor/preview/enqueue_styles', [ $this, 'register_widget_styles' ] );
        add_action( 'wp_ajax_elemailer-lite-remote-request', [ $this, 'remote_request' ] );
        add_action( 'wp_ajax_elemailer-lite-my-template-remote-request', [ $this, 'my_template_remote_request' ] );
        add_action( 'wp_ajax_elemailer-lite-my-template-delete-request', [ $this, 'my_template_delete_request' ] );
        add_action( 'wp_ajax_elemailer-lite-elementor-batch-process', [ $this, 'elementor_batch_process' ] );
        add_action( 'wp_ajax_elemailer-lite-elementor-batch-my-template-process', [ $this, 'my_template_elementor_batch_process' ] );
        add_action( 'wp_ajax_elemailer-sync-page-builder', [ $this, 'get_sync_page_builder' ] );
        add_action( 'wp_ajax_elemailer-saved-my-templates', [ $this, 'get_sync_saved_templates' ] );
        add_action( 'elementor/document/after_save', [ $this, 'document_after_save' ], 25, 1 );
    }

    /**
     * On template save.
     *
     * Run this method when template is being saved.
     *
     * Fired by `save_post` action.
     *
     * @since 1.0.5
     * @access public
     *
     * @param obj $post Post.
     */
    public function document_after_save( $post ) {
        if ( 'elementor_library' !== $post->get_post()->post_type ) {
            return;
        }

        if ( ! in_array( get_post_type(), [ 'em-form-template', 'em-emails-template' ] ) ) {
            return;
        }

        // Don't save type on import, the importer will do it.
        if ( did_action( 'import_start' ) ) {
            return;
        }

        update_post_meta( $post->get_post()->ID, '_elementor_template_type', 'elemailer_library' );
        wp_set_object_terms( $post->get_post()->ID, 'elemailer_library', 'elementor_library_type' );
    }

    /**
     * Insert Template
     *
     * @return void
     */
    public function insert_templates() {
        ob_start();
        require_once ELE_MAILER_LITE_PLUGIN_PUBLIC_DIR . '/views/templates.php';
        ob_end_flush();
    }
    /**
     * Register module required js on elementor's action.
     *
     * @since 1.0.5
     */
    public function register_widget_scripts() {
        if ( ! in_array( get_post_type(), [ 'em-form-template', 'em-emails-template' ] ) ) {
            return;
        }

        wp_enqueue_script( 'masonry' );
        wp_enqueue_script( 'imagesloaded' );

        $version = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? time() : ELE_MAILER_LITE_VERSION;
        wp_enqueue_script( 'elemailer-elementor-admin-page', ELE_MAILER_LITE_PLUGIN_URL . '/integrations/elementor/assets/js/elemailer-elementor-admin-page.js', array( 'jquery', 'wp-util', 'updates', 'masonry', 'imagesloaded' ), $version, true);

        $license_status   = false;
        /* translators: %s are link. */
        $license_msg      = sprintf( __( 'This is a premium template. you can purchase it from <a href="%s" target="_blank">here</a>.', 'elemailer-lite' ), 'https://elemailer.com/pricing/' );
        /* translators: %s are link. */
        $license_msg_save = sprintf( __( 'Save Email template feature is available only in <a href="%s" target="_blank">Elemailer</a> paid version.', 'elemailer-lite' ), 'https://elemailer.com/pricing/' );

        $data = apply_filters(
            'elemailer_render_localize_vars',
            array(
                'plugin_name'          => __( 'Elemailer Templates', 'elemailer-lite' ),
                'ajaxurl'              => esc_url( admin_url( 'admin-ajax.php' ) ),
                '_ajax_nonce'          => wp_create_nonce( 'elemailer-lite-check' ),
                'default_page_builder' => $this->get_default_page_builder(),
                'saved_my_templates'   => $this->get_saved_my_templates(),
                'template'             => esc_html__( 'Template', 'elemailer-lite' ),
                'block'                => esc_html__( 'Block', 'elemailer-lite' ),
                'dismiss_text'         => esc_html__( 'Dismiss', 'elemailer-lite' ),
                'install_plugin_text'  => esc_html__( 'Install Required Plugins', 'elemailer-lite' ),
                'license_status'       => $license_status,
                'isPro'                => false,
                'license_msg'          => $license_msg,
                'license_msg_save'     => $license_msg_save,
                'getProText'           => __( 'Get Access!', 'elemailer-lite' ),
                'syncCompleteMessage'  => __( 'Template library refreshed!', 'elemailer-lite' ),
                'confirmDeleteMessage' => __( 'Are you sure you want to delete?', 'elemailer-lite' ),
                'public_asset_dir'     => ELE_MAILER_LITE_PLUGIN_PUBLIC,
                'getProURL'            => esc_url( 'https://elemailer.com/pricing/?utm_source=demo-import-panel&utm_campaign=elemailer-lite&utm_medium=wp-dashboard' ),

            )
        );

        wp_localize_script( 'elemailer-elementor-admin-page', 'elemailerElementorData', $data );
    }

    /**
     * Get default page builder list
     *
     * @since 1.0.5
     *
     * @return array $templates
     */
    public function get_default_page_builder() {
        $api_url       = 'https://lib.elemailer.com/wp-json/wp/v2/elemailerlib/';
        $response      = wp_remote_get( $api_url );
        $response_body = wp_remote_retrieve_body( $response );
        $response_data = json_decode( $response_body, true );
        $templates     = array();

        if ( empty( $response_data ) ) {
            return $templates;
        }

        foreach ( $response_data as $page_data ) {
            $templates['id-' . $page_data['id']] = [
                'title'                  => $page_data['title']['rendered'],
                'featured-image-url'     => $page_data['featured_image_src']['featured_media_full'],
                'thumbnail-image-url'    => $page_data['featured_image_src']['featured_media_thumbnail'],
                'elemailer-page-api-url' => $api_url . $page_data['id'],
                'dynamic-page'           => 'no',
                'elemailer-page-tag'     => [
                    '423' => 'home'
                ],
                'site-pages-type'         => $page_data['template_type'],
                'site-pages-page-builder' => 'elementor',
                'site-pages-category'     => [
                    '60' => 'business',
                    '61' => 'free',
                ],
                'required-plugins'        => [
                    [
                        'slug' => 'elementor',
                        'init' => 'elementor/elementor.php',
                        'name' => 'Elementor',
                    ]
                ],
            ];
        }

        return $templates;
    }

    /**
     * Get default page builder list
     *
     * @since 1.0.5
     *
     * @return array $templates
     */
    public function get_saved_my_templates( $args = [] ) {
        $templates = [];

        $defaults = [
            'post_type' => 'elementor_library',
            'post_status' => 'publish',
            'posts_per_page' => -1,
            'orderby' => 'title',
            'order' => 'ASC',
            'meta_query' => [
                [
                    'key' => '_elementor_template_type',
                    'value' => 'elemailer_library',
                ],
            ],
        ];

        $query_args      = wp_parse_args( $args, $defaults );
        $templates_query = new \WP_Query( $query_args );

        if ( $templates_query->have_posts() ) {
            foreach ( $templates_query->get_posts() as $result ) {
                $templates[ $result->ID ] = [
                    'id'    => $result->ID,
                    'title' => $result->post_title,
                    'author' => get_the_author_meta( 'display_name', $result->post_author ),
                    'preview_url' => site_url( '/?elementor_library=' . $result->post_name ),
                    'date' => get_the_date( 'F j, Y', $result->ID ),
                ];
            }
        }

        return $templates;
    }

    /**
     * Elementor Templates sync
     *
     * @since 1.0.5
     */
    public function get_sync_page_builder() {
        check_ajax_referer( 'elemailer-lite-check', '_ajax_nonce' );

        if ( ! current_user_can( 'edit_posts' ) ) {
            wp_send_json_error( __( 'You are not allowed to perform this action', 'elemailer-lite' ) );
        }

        wp_send_json_success( $this->get_default_page_builder() );
    }

    /**
     * Elementor saved templates sync
     *
     * @since 1.0.5
     */
    public function get_sync_saved_templates() {
        check_ajax_referer( 'elemailer-lite-check', '_ajax_nonce' );

        if ( ! current_user_can( 'edit_posts' ) ) {
            wp_send_json_error( __( 'You are not allowed to perform this action', 'elemailer-lite' ) );
        }

        wp_send_json_success( $this->get_saved_my_templates() );
    }

    /**
     * Register module required style on elementor's action.
     *
     * @since 1.0.5
     */
    public function register_widget_styles() {
        if ( ! in_array( get_post_type(), [ 'em-form-template', 'em-emails-template' ] ) ) {
            return;
        }
        $version = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? time() : ELE_MAILER_LITE_VERSION;
        wp_enqueue_style( 'elemailer-elementor-admin-page', ELE_MAILER_LITE_PLUGIN_URL . 'integrations/elementor/assets/css/elemailer-elementor-admin-style.css', false, $version );
    }

    /**
     * Get API params
     *
     * @since 1.0.5
     *
     * @return array
     */
    public function get_api_params() {
        return apply_filters(
            'elemailer_lite_api_params', array(
                'purchase_key' => '',
                'site_url'     => get_site_url(),
                'per-page'     => 15,
                'template_status' => '',
            )
        );
    }

    /**
     * Elementor My Delete Templates Request
     *
     * @since 1.0.5
     */
    public function my_template_delete_request() {
        check_ajax_referer( 'elemailer-lite-check', '_ajax_nonce' );

        if ( ! current_user_can( 'edit_posts' ) ) {
            wp_send_json_error( __( 'You are not allowed to perform this action', 'elemailer-lite' ) );
        }

        $template_id = isset( $_POST['template_id'] ) ? sanitize_text_field( $_POST['template_id'] ) : '';

        wp_delete_post( $template_id, TRUE );
        
        wp_send_json_success( 1 );
    }

    /**
     * Elementor My Templates Request
     *
     * @since 1.0.5
     */
    public function my_template_remote_request() {
        check_ajax_referer( 'elemailer-lite-check', '_ajax_nonce' );

        if ( ! current_user_can( 'edit_posts' ) ) {
            wp_send_json_error( __( 'You are not allowed to perform this action', 'elemailer-lite' ) );
        }

        $template_id = isset( $_POST['template_id'] ) ? sanitize_text_field( $_POST['template_id'] ) : '';
        $template    = get_post( $template_id );

        if ( is_wp_error( $template ) ) {
            wp_send_json_error( wp_remote_retrieve_body( 'No data found' ) );
        }

        $templates = [
            'id'              => $template->ID,
            'title'           => $template->post_title,
            '_elementor_data' => get_post_meta( $template->ID, '_elementor_data', true ),
        ];

        wp_send_json_success( $templates );
    }

    /**
     * Elementor Templates Request
     *
     * @since 1.0.5
     */
    public function remote_request() {
        check_ajax_referer( 'elemailer-lite-check', '_ajax_nonce' );

        if ( ! current_user_can( 'edit_posts' ) ) {
            wp_send_json_error( __( 'You are not allowed to perform this action', 'elemailer-lite' ) );
        }

        $api_url  = isset( $_POST['url'] ) ? sanitize_text_field( $_POST['url'] ) : '';
        $api_url  = add_query_arg( $this->get_api_params(), $api_url );
        $response = wp_remote_get( $api_url );

        if ( is_wp_error( $response ) ) {
            wp_send_json_error( wp_remote_retrieve_body( $response ) );
        }

        $body = wp_remote_retrieve_body( $response );
        $data = json_decode( $body, true );

        wp_send_json_success( $data );
    }

    /**
     * Elementor Batch Process via AJAX for my template
     *
     * @since 1.0.5
     */
    public function my_template_elementor_batch_process() {
        // Verify Nonce.
        check_ajax_referer( 'elemailer-lite-check', '_ajax_nonce' );

        if ( ! current_user_can( 'edit_posts' ) ) {
            wp_send_json_error( __( 'You are not allowed to perform this action', 'elemailer-lite' ) );
        }

        $template_id = isset( $_POST['template_id'] ) ? absint( $_POST['template_id'] ) : '';

        if ( empty( $template_id ) ) {
            wp_send_json_error( __( 'Invalid data.', 'elemailer-lite' ) );
        }

        $template = get_post( $template_id );

        if ( empty( $template ) ) {
            wp_send_json_error( __( 'Invalid data.', 'elemailer-lite' ) );
        }

        $elementor_data = get_post_meta( $template->ID, '_elementor_data', true );

        if ( empty( $elementor_data ) ) {
            wp_send_json_error( __( 'Invalid Post Meta', 'elemailer-lite' ) );
        }

        $meta    = json_decode( $elementor_data, true );
        $post_id = isset( $_POST['id'] ) ? absint( $_POST['id'] ) : '';

        if ( empty( $post_id ) || empty( $meta ) ) {
            wp_send_json_error( __( 'Invalid Post ID or Elementor Meta', 'elemailer-lite' ) );
        }

        $import      = new \Elemailer_Lite\Integrations\Elementor\Import();
        $import_data = $import->import( $post_id, $meta );

        wp_send_json_success( $import_data );
    }

    /**
     * Elementor Batch Process via AJAX
     *
     * @since 1.0.5
     */
    public function elementor_batch_process() {
        // Verify Nonce.
        check_ajax_referer( 'elemailer-lite-check', '_ajax_nonce' );

        if ( ! current_user_can( 'edit_posts' ) ) {
            wp_send_json_error( __( 'You are not allowed to perform this action', 'elemailer-lite' ) );
        }

        $api_url = isset( $_POST['url'] ) ? esc_url_raw( $_POST['url'] ) : '';

        if ( empty( $api_url ) ) {
            wp_send_json_error( __( 'Invalid API URL.', 'elemailer-lite' ) );
        }

        $response = wp_remote_get( $api_url );

        if ( is_wp_error( $response ) ) {
            wp_send_json_error( wp_remote_retrieve_body( $response ) );
        }

        $body = wp_remote_retrieve_body( $response );
        $data = json_decode( $body, true );

        if ( ! isset( $data['_elementor_data'] ) ) {
            wp_send_json_error( __( 'Invalid Post Meta', 'elemailer-lite' ) );
        }

        $meta    = json_decode( $data['_elementor_data'], true );
        $post_id = isset( $_POST['id'] ) ? absint( $_POST['id'] ) : '';

        if ( empty( $post_id ) || empty( $meta ) ) {
            wp_send_json_error( __( 'Invalid Post ID or Elementor Meta', 'elemailer-lite' ) );
        }

        $import      = new \Elemailer_Lite\Integrations\Elementor\Import();
        $import_data = $import->import( $post_id, $meta );

        wp_send_json_success( $import_data );
    }
}
