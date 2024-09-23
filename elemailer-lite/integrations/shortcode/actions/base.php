<?php

namespace Elemailer_Lite\Integrations\Shortcode\Actions;

defined('ABSPATH') || exit;

/**
 * Shortcode action related base class
 *
 * @author elEmailer 
 * @since 1.0.3
 */
class Base
{
    use \Elemailer_Lite\Traits\Singleton;

    /**
     * initial function for every action of Shortcode
     *
     * @return void
     * @since 1.0.3
     */
    public function init()
    {
        Hooks::instance()->init();
    }

}
