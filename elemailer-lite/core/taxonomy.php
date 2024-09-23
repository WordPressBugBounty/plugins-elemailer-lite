<?php

namespace Elemailer_Lite\Core;

defined('ABSPATH') || exit;

/**
 * custom taxonomy for a post type register core class
 * used to creating taxonomy by extending it.
 * 
 * @author elEmailer
 * @since 1.0.0
 */
abstract class Taxonomy
{

    /**
     * constructor function
     * help register taxonomy
     *
     * @return constructor function
     * @since 1.0.0
     */
    public function __construct()
    {

        $name = $this->taxonomy_name();
        $post_type = $this->post_type();
        $args = $this->taxonomy_args();

        add_action('init', function () use ($name, $post_type, $args) {
            register_taxonomy($name, $post_type, $args);
        });
    }

    /**
     * taxonomy args ready function
     * implement from child class
     * @return void
     * @since 1.0.0
     */
    public abstract function taxonomy_args();
}
