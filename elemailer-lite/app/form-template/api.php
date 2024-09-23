<?php

namespace Elemailer_Lite\App\Form_Template;

defined('ABSPATH') || exit;

/**
 * rest api initialization class
 * is this class every method will a rest api
 * 
 * @author elEmailer
 * @since 1.0.0
 */
class Api extends \Elemailer_Lite\Core\Api
{

    /**
     * rest api prefix and parameter set function
     *
     * @return void
     * @since 1.0.0
     */
    public function config()
    {
        $this->prefix = 'form-templates';
        $this->param  = "/(?P<id>\w+)";
    }

    /**
     * create/update template rest api callback function
     * make a rest api with method
     *
     * @return array
     * @since 1.0.0
     */
    public function post_update()
    {
        $template_id = $this->request['id'];
        $submitted_data = $this->request->get_params();

        return Action::instance()->store($template_id, $submitted_data);
    }

    /**
     * get template settings rest api callback function
     * make a rest api with method
     *
     * @return array
     * @since 1.0.0
     */
    public function get_get()
    {
        $template_id = $this->request['id'];

        return Action::instance()->get_template_setting($template_id);
    }
}
