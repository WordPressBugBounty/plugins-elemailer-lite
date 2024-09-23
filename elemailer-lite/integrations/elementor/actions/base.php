<?php

namespace Elemailer_Lite\Integrations\Elementor\Actions;

defined('ABSPATH') || exit;

/**
 * Base class for all elementor related actions
 *
 * @author elEmailer 
 * @since 1.0.0
 */
class Base
{
    use \Elemailer_Lite\Traits\Singleton;

    /**
     * initial function of this class
     *
     * @since 1.0.0
     */
    public function init()
    {
        // trigger elementor free actions to modify elementor
        Hooks::instance()->trigger_elementor_free_actions();

        // is plugin active function load if not exists
        if (!function_exists('is_plugin_active')) {
            require_once(ABSPATH . '/wp-admin/includes/plugin.php');
        }

        // elementor pro check and fired elementor pro actions
        if (is_plugin_active('elementor-pro/elementor-pro.php')) {
            Hooks::instance()->trigger_elementor_pro_actions();
        }
    }
}
