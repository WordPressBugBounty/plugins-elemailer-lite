<?php

namespace Elemailer_Lite\Integrations\Shortcode;

defined('ABSPATH') || exit;

/**
 * Shortcode integration related base class
 * We will add all shortcode support through this class 
 * @author elEmailer 
 * @since 1.0.3
 */
class Base
{
    use \Elemailer_Lite\Traits\Singleton;

    /**
     * initialize function for loading everything related on mailpoet
     *
     * @return void
     * @since 1.0.3
     */
    public function init()
    {
        Actions\Base::instance()->init();
    }

}
