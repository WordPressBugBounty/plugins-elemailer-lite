<?php

namespace Elemailer_Lite\App\Form_Template;

defined('ABSPATH') || exit;

/**
 * Custom post type register class
 * register post type for email template
 *
 * @author elEmailer 
 * @since 1.0.0
 */
class Cpt extends \Elemailer_Lite\Core\Cpt
{
    /**
     * get post name function
     *
     * @return string
     */
    public function post_type()
    {
        return 'em-form-template';
    }

    /**
     * templete settings fields get function
     * used for sanitizing templete settings during create/ update template
     *
     * @return void
     * @since 1.0.0
     */
    public function get_template_settings_fields()
    {

        return [

            'title' => [
                'name' => 'title',
            ],
            'subject' => [
                'name' => 'subject',
            ],
            'emailTo' => [
                'name' => 'emailTo',
            ],
            'emailFrom' => [
                'name' => 'emailFrom',
            ],
            'emailReplyTo' => [
                'name' => 'emailReplyTo',
            ],
        ];
    }

    /**
     * configure post type function
     *
     * @return array $args
     * @since 1.0.0
     */
    public function post_args()
    {
        $labels = array(
            'name'                  => esc_html_x('Form Templates', 'Post Type General Name', 'elemailer-lite'),
            'singular_name'         => esc_html_x('Form Template', 'Post Type Singular Name', 'elemailer-lite'),
            'menu_name'             => esc_html__('Form Template', 'elemailer-lite'),
            'name_admin_bar'        => esc_html__('Form Template', 'elemailer-lite'),
            'archives'              => esc_html__('Form Template Archives', 'elemailer-lite'),
            'attributes'            => esc_html__('Form Template Attributes', 'elemailer-lite'),
            'parent_item_colon'     => esc_html__('Parent Item:', 'elemailer-lite'),
            'all_items'             => esc_html__('Form Templates', 'elemailer-lite'),
            'add_new_item'          => esc_html__('Add New Form Template', 'elemailer-lite'),
            'add_new'               => esc_html__('Add New', 'elemailer-lite'),
            'new_item'              => esc_html__('New Form Template', 'elemailer-lite'),
            'edit_item'             => esc_html__('Edit Form Template', 'elemailer-lite'),
            'update_item'           => esc_html__('Update Form Template', 'elemailer-lite'),
            'view_item'             => esc_html__('View Form Template', 'elemailer-lite'),
            'view_items'            => esc_html__('View Form Templates', 'elemailer-lite'),
            'search_items'          => esc_html__('Search Form Templates', 'elemailer-lite'),
            'not_found'             => esc_html__('Not found', 'elemailer-lite'),
            'not_found_in_trash'    => esc_html__('Not found in Trash', 'elemailer-lite'),
            'featured_image'        => esc_html__('Featured Image', 'elemailer-lite'),
            'set_featured_image'    => esc_html__('Set featured image', 'elemailer-lite'),
            'remove_featured_image' => esc_html__('Remove featured image', 'elemailer-lite'),
            'use_featured_image'    => esc_html__('Use as featured image', 'elemailer-lite'),
            'insert_into_item'      => esc_html__('Insert into form', 'elemailer-lite'),
            'uploaded_to_this_item' => esc_html__('Uploaded to this form', 'elemailer-lite'),
            'items_list'            => esc_html__('Form Templates list', 'elemailer-lite'),
            'items_list_navigation' => esc_html__('Form Templates list navigation', 'elemailer-lite'),
            'filter_items_list'     => esc_html__('Filter froms list', 'elemailer-lite'),
        );
        $rewrite = array(
            'slug'                  => 'em-form-template',
            'with_front'            => true,
            'pages'                 => false,
            'feeds'                 => false,
        );
        $args = array(
            'label'                 => esc_html__('Form Templates', 'elemailer-lite'),
            'description'           => esc_html__('elemailer form template create', 'elemailer-lite'),
            'labels'                => $labels,
            'supports'              => array('title', 'editor', 'elementor', 'permalink'),
            'hierarchical'          => true,
            'public'                => true,
            'show_ui'               => true,
            'show_in_menu'          => 'elemailer-menu',
            'menu_icon'             => 'dashicons-welcome-write-blog',
            'menu_position'         => 5,
            'show_in_admin_bar'     => false,
            'show_in_nav_menus'     => false,
            'can_export'            => true,
            'has_archive'           => false,
            'publicly_queryable'    => true,
            'rewrite'               => $rewrite,
            'query_var'             => true,
            'exclude_from_search'   => true,
            'publicly_queryable'    => true,
            'capability_type'       => 'page',
            'show_in_rest'          => false,
            'rest_base'             => $this->post_type(),
        );

        return $args;
    }

    /**
     * register post type function
     * flush rewrites after regsiter cpt function
     *
     * @return void
     */
    public function flush_rewrites()
    {
        $name = $this->post_type();
        $args = $this->post_args();
        register_post_type($name, $args);
        flush_rewrite_rules();
    }
}
