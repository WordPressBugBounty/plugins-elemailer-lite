<?php

namespace Elemailer_Lite\Integrations\Elementor;

defined('ABSPATH') || exit;

/**
 * Elementor integration base class.
 * This class load everything related to elementor
 *
 * @author elEmailer
 * @since 1.0.0
 */
class Base
{
    use \Elemailer_Lite\Traits\Singleton;

    public function init()
    {
        // Check if Elementor installed and activated.
        if (!did_action('elementor/loaded')) {
            add_action('admin_notices', array($this, 'required_elementor'));
            return;
        }
        // check elementor pro activation
        // is plugin active function load if not exists
        if (!function_exists('is_plugin_active')) {
            require_once(ABSPATH . '/wp-admin/includes/plugin.php');
        }

        // elementor pro check and fired elementor pro actions
        if (file_exists(WP_PLUGIN_DIR . '/elementor-pro/elementor-pro.php') && !is_plugin_active('elementor-pro/elementor-pro.php')) {
            add_action('admin_notices', [$this, 'required_elementor_pro']);
        }
        // Check for required Elementor version.
        if (!version_compare(ELEMENTOR_VERSION, '3.0.11', '>=')) {
            add_action('admin_notices', array($this, 'required_elementor_version'));
            return;
        }

        // register widgets
        Widgets\Base::instance()->init();

        // fire elementor actions
        Actions\Base::instance()->init();

        // action for enqueue style on elementor editor panel
        add_action('elementor/editor/after_enqueue_styles', [$this, 'load_css_elementor_editor_panel']);

        // action for enqueue style on elementor frontend in editing page
        add_action('elementor/frontend/after_enqueue_styles', [$this, 'load_css_elementor_frontend']);

        // action for enqueue scripts on elementor editor panel
        add_action('elementor/editor/after_enqueue_scripts', [$this, 'load_js_elementor_editor_panel']);

        // action for enqueue scripts on elementor frontend in editing page
        add_action('elementor/frontend/before_enqueue_scripts', [$this, 'load_js_elementor_frontend']);

        add_action( 'admin_init', [ $this, 'display_admin_notice_logic' ], 5 );
        add_action( 'admin_notices', [ $this, 'display_admin_notice' ] );
        add_action('elementor/page_templates/canvas/after_content', [$this, 'show_footer_branding']);
    }

    /**
     * Show footer branding
     *
     * @return void
     * @since 1.0.9
     */
    public function show_footer_branding( $content )
    {
        if ( ! in_array( get_post_type(), [ 'em-form-template', 'em-emails-template' ] ) ) {
            return;
        }

        $info = get_option('elemailer_lite_info');  // run footer branding only for new users after v-1.0.0
			
		if ( isset( $info['old_user'] ) && ! $info['old_user'] && 'yes' !== get_option( 'elemailer_hide_mail_branding', true ) ) : ?>
		 	<!-- START Branding FOOTER -->
			<div class="elementor-section-wrap" style="text-align: center;padding-top: 40px;">
				<table role="presentation" border="0" cellpadding="0" cellspacing="0" style="margin: auto;font-size: 14px;">
				<tr>
					<td class="content-block">
					Email designed with Elementor ❤️ Powered by  <a target="_blank" href="https://elemailer.com?source=inemail">Elemailer</a>                           
					<a target="_blank" href="https://elemailer.com?source=inemail">
						<img src="https://elemailer.com/wp-content/uploads/2020/11/logo.png" style=" display: block; width: 120px; margin: auto; padding: 15px 0px 15px 0px; ">
					</a>
					</td>
				</tr>
				</table>
			</div>
			<!-- END Branding FOOTER -->
    	<?php endif;
    }

    /**
     * Display Admin Notice rules add
     *
     * @since 1.0.5
     */
    public function display_admin_notice_logic()
    {
        if ( isset( $_GET['elemailer_lite_notice_not_now'] ) && ! empty( $_GET['elemailer_lite_notice_not_now'] ) ) {
            if ( 1 === absint( $_GET['elemailer_lite_notice_not_now'] ) ) {
                update_option( 'elemailer_lite_notice_not_now_time', strtotime( "now" ) );
            }
        }

        if ( isset( $_GET['elemailer_lite_notice_never_show'] ) && ! empty( $_GET['elemailer_lite_notice_never_show'] ) ) {
            if ( 1 === absint( $_GET['elemailer_lite_notice_never_show'] ) ) {
                update_option( 'elemailer_lite_notice_never_show', 'yes' );
            }
        }
    }

    /**
     * Display Admin Notice, asking for a review
     *
     * @since 1.0.5
     */
    public function display_admin_notice()
    {
        if ( empty( get_option( 'elemailer_lite_info' ) ) ) {
            return;
        }

        if ( 'yes' === get_option( 'elemailer_lite_notice_never_show' ) ) {
            return;
        }

        $buttons = [
            [
                'label'  => esc_html__('Rate Now!', 'elemailer-lite'),
                'url'    => esc_url('https://wordpress.org/support/plugin/elemailer-lite/reviews/#new-post'),
                'custom_style' => 'margin: 0px 10px 10px 0px;',
                'target' => '_blank',
            ],
            [
                'label' => esc_html__('Not Now!', 'elemailer-lite'),
                'url'   => esc_url( get_admin_url() . '?elemailer_lite_notice_not_now=1' ),
                'style' => [
                    'class' => 'secondary-primary',
                ]
            ],
            [
                'label'        => esc_html__('Never Show!', 'elemailer-lite'),
                'url'          => esc_url( get_admin_url() . '?elemailer_lite_notice_never_show=1' ),
                'custom_style' => 'position: absolute; right: 10px; color: #ccc;',
                'style' => [
                    'class' => 'elemailer-lite-notice-never-show',
                ]
            ],

        ];

        $elemailer_lite_info = get_option( 'elemailer_lite_info' );

        if (
            strtotime( '-30 days' ) >= get_option( 'elemailer_lite_notice_not_now_time', strtotime( '-30 days' ) ) &&
            strtotime( date( "Y-m-d H:i:s", strtotime( '-7 days' ) ) ) >= strtotime( date( "Y-m-d H:i:s", strtotime( $elemailer_lite_info['activation_time'] ) ) )
        ) {
            \Elemailer_Lite\Helpers\Notice::push(
                [
                    'id'               => 'request-for-review',
                    'type'             => 'info',
                    'style'            => 'block',
                    'dismissible-time' => 14 * DAY_IN_SECONDS,
                    'dismissible'      => true,
                    'btn'              => $buttons,
                    'message'          => sprintf(esc_html__('You have been using Elemailer Lite for a couple of days. We hope you really enjoy using it. If so please do give us a rating to boost our morale to work more on this.', 'elemailer-lite'), '3.4'),
                ]
            );
        }
    }

    /**
     * elementor missing notice function
     *
     * @return void
     * @since 1.0.0
     */
    public function required_elementor()
    {

        if (isset($_GET['activate'])) {
            unset($_GET['activate']);
        }

        if (file_exists(WP_PLUGIN_DIR . '/elementor/elementor.php')) {
            $btn['label'] = esc_html__('Activate Elementor', 'elemailer-lite');
            $btn['url'] = wp_nonce_url(self_admin_url('plugins.php?action=activate&plugin=elementor/elementor.php&plugin_status=all&paged=1'), 'activate-plugin_elementor/elementor.php');
        } else {
            $btn['label'] = esc_html__('Install Elementor', 'elemailer-lite');
            $btn['url'] = wp_nonce_url(self_admin_url('update.php?action=install-plugin&plugin=elementor'), 'install-plugin_elementor');
        }

        \Elemailer_Lite\Helpers\Notice::push(
            [
                'id'          => 'elemailer-lite-required-elementor',
                'type'        => 'error',
                'dismissible' => false,
                'btn'         => $btn,
                'message'     => sprintf(esc_html__('Elemailer Lite needs Elementor version %1$s+ , which is not activated.', 'elemailer-lite'), '3.4'),
            ]
        );
    }

    /**
     * elementor older version notice function
     *
     * @return void
     * @since 1.0.0
     */
    public function required_elementor_version()
    {

        $btn['label'] = esc_html__('Update Elementor', 'elemailer-lite');
        $btn['url'] = wp_nonce_url(self_admin_url('update.php?action=upgrade-plugin&plugin=elementor/elementor.php'), 'upgrade-plugin_elementor/elementor.php');

        \Elemailer_Lite\Helpers\Notice::push(
            [
                'id'          => 'elemailer-lite-unsupported-elementor-version',
                'type'        => 'error',
                'dismissible' => false,
                'btn'         => $btn,
                'message'     => sprintf(esc_html__('Elemailer Lite needs Elementor version %1$s+ for working it, lower version is running. Please update.', 'elemailer-lite'), '3.4'),
            ]
        );
    }
    
    /**
     * elementor missing notice function
     *
     * @return void
     * @since 1.0.0
     */
    public function required_elementor_pro()
    {

        if (isset($_GET['activate'])) {
            unset($_GET['activate']);
        }

        if (file_exists(WP_PLUGIN_DIR . '/elementor-pro/elementor-pro.php')) {
            $btn['label'] = esc_html__('Activate Elementor pro', 'elemailer-lite');
            $btn['url'] = wp_nonce_url(self_admin_url('plugins.php?action=activate&plugin=elementor-pro/elementor-pro.php&plugin_status=all&paged=1'), 'activate-plugin_elementor-pro/elementor-pro.php');
            $target = false;
        } else {
            $btn['label'] = esc_html__('Install Elementor pro', 'elemailer-lite');
            $btn['url'] = esc_url('https://elementor.com/pro/');
            $target = true;
        }

        \Elemailer_Lite\Helpers\Notice::push(
            [
                'id'          => 'elemailer-lite-elementor-pro-required',
                'type'        => 'error',
                'dismissible' => false,
                'target'      => ($target)? '_blank': '',
                'btn'         => $btn,
                'message'     => sprintf(esc_html__('To use Elemailer Lite with Elementor pro Forms to send email elementor pro %1$s+ should be installed & activated. Ignore if you plan to use Elemailer with other Form plugin(s).', 'elemailer-lite'), '3.4'),
            ]
        );
    }

    /**
     * function for enqueue style on elementor editor panel
     *
     * @return void
     * @since 1.0.0
     */
    public function load_css_elementor_editor_panel()
    {
        $post_type = get_post_type();

        if ((in_array($post_type, ['em-form-template', 'em-emails-template']))) :
            $version = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? time() : ELE_MAILER_LITE_VERSION;
            wp_enqueue_style('elemailer-elementor-editor', ELE_MAILER_LITE_PLUGIN_URL . 'integrations/elementor/assets/css/editor.css', false, $version );
        endif;
    }

    /**
     * function for enqueue style on elementor frontend in editing page
     *
     * @return void
     * @since 1.0.0
     */
    public function load_css_elementor_frontend()
    {
        $post_type = get_post_type();

        if ((in_array($post_type, ['em-form-template', 'em-emails-template']))) :
            wp_enqueue_style('elemailer-elementor-frontend', ELE_MAILER_LITE_PLUGIN_URL . 'integrations/elementor/assets/css/frontend.css', false, ELE_MAILER_LITE_VERSION);
        endif;
    }

    /**
     * function for enqueue scripts on elementor editor panel
     *
     * @return void
     * @since 1.0.0
     */
    public function load_js_elementor_editor_panel()
    {
        $post_type = get_post_type();

        if ((in_array($post_type, ['em-form-template', 'em-emails-template']))) :

            wp_enqueue_script('elemailer-elementor-editor', ELE_MAILER_LITE_PLUGIN_URL . 'integrations/elementor/assets/js/editor.js', array('jquery'), ELE_MAILER_LITE_VERSION, true);
            wp_localize_script(
                'elemailer-elementor-editor',
                'elemailer_lite',
                [
                    'ajaxUrl' => admin_url('admin-ajax.php'),
                    'url' => get_admin_url(),
                    'restUrl' => get_rest_url(),
                    'wpRestNonce' => wp_create_nonce('wp_rest'),
                ]
            );

        endif;
    }

    /**
     * function for enqueue scripts on elementor frontend in editing page
     *
     * @return void
     * @since 1.0.0
     */
    public function load_js_elementor_frontend()
    {
        $post_type = get_post_type();

        if ((in_array($post_type, ['em-form-template', 'em-emails-template']))) :

            wp_enqueue_script('elemailer-elementor-frontend', ELE_MAILER_LITE_PLUGIN_URL . 'integrations/elementor/assets/js/frontend.js', array('jquery'), ELE_MAILER_LITE_VERSION, true);

        endif;
    }
}
