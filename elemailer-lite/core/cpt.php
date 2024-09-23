<?php

namespace Elemailer_Lite\Core;

defined('ABSPATH') || exit;

/**
 * custom post type register core class
 * used to creating cpt by extending it.
 * 
 * @author elEmailer
 * @since 1.0.0
 */
abstract class Cpt
{

    /**
     * constructor function
     * help register cpt
     *
     * @return constructor function
     * @since 1.0.0
     */
    public function __construct()
    {

        $name = $this->post_type();
        $args = $this->post_args();

        add_action('init', function () use ($name, $args) {
            register_post_type($name, $args);
        });
    }

    /**
     * cpt args ready function
     *implement from child class
     * @return void
     * @since 1.0.0
     */
    public abstract function post_args();
}
