<?php

defined('ABSPATH') || exit;

/**
 * Obsulate @since version 1.8
 * Fake container to avoid fatal error with Elementor experiments that comes from modules directly and has no way to remove them. 
 *
 * @author elEmailer 
 * @since 1.6
 */
class Container
{
    use \Elemailer_Lite\Traits\Singleton;

    /**
     * initial function of this class
     *
     * @since 1.6
     */
    public function init()
    {
        
    }
}


