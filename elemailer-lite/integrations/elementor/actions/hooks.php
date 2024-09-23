<?php

namespace Elemailer_Lite\Integrations\Elementor\Actions;

defined('ABSPATH') || exit;

use \Elementor\Controls_Manager;
use \Elementor\Utils;


/**
 * all hook related class for activating some extra functionality based on elementor
 *
 * @author elEmailer
 * @since 1.0.0
 */
class Hooks
{
    use \Elemailer_Lite\Traits\Singleton;

    /**
     * initial function of this class
     *
     * @since 1.0.0
     */
    public function trigger_elementor_free_actions()
    {
        // add template select control in elementor form widget
        add_action('elementor/element/form/section_email/before_section_end', [$this, 'email_template_selector'], 10, 2);

        // add template select control in elementor form widget
        add_action('elementor/element/form/section_email_2/before_section_end', [$this, 'email2_template_selector'], 10, 2);

        // add custom background control in section control
        add_action('elementor/element/section/section_background/before_section_end', [$this, 'section_background'], 10, 2);

        // change the title of the setting panel for our email type
        add_filter('elementor/document/config', [$this, 'register_document_config'],10,2);


        // remove container experiement inside elemailer since @1.2
        add_action( 'elementor/experiments/default-features-registered', [$this, 'remove_container_experiment'],1);
        
        add_filter( 'elementor/admin/localize_settings', [$this, 'remove_all_experiment_requirement'],1);

    }

    /**
     * function for activate elementor pro functionality
     *
     * @return void
     * @since 1.0.0
     */
    public function trigger_elementor_pro_actions()
    {
        // elementor form submission catch hook

        //@since v1.0.3 after it introduced Form_Actions_Registrar class to add control
        // Fallback for older releases before 3.5 pro ie: 3.4.2


        if(class_exists('\ElementorPro\Modules\Forms\Registrars\Form_Actions_Registrar')){

            add_action('elementor_pro/forms/actions/register', [$this, 'trigger_elementor_form_submission_new']);

        }else{

            add_action('elementor_pro/forms/form_submitted', [$this, 'trigger_elementor_form_submission']);

        }
    }
    
    /**
     * catch elementor pro form submission function - 3.5 pro new version support
     *
     * @param object $module
     * @since 1.0.3
     */
    public function trigger_elementor_form_submission_new($module)
    {
        // override email class of elementor pro
        $module->register(new Void_Email());

        // override email2 class of elementor pro
        $module->register(new Void_Email2());
    }
    /**
     * catch elementor pro form submission function for older version ie:3.4.2
     * has to be removed going forward
     * @param object $module
     * @since 1.0.0
     */
    public function trigger_elementor_form_submission($module)
    {
        // override email class of elementor pro
        $module->add_form_action('email', new Void_Email());

        // override email2 class of elementor pro
        $module->add_form_action('email2', new Void_Email2());
    } 

     

    /**
     * option for selecting elemailer lite template function in form widget
     *
     * @param object $element
     * @param array $args
     * @since 1.0.0
     */
    public function email_template_selector($element, $args)
    {
        // add a control
        $element->add_control(
            'show_elemailer_email_template_selector',
            [
                'label' => esc_html__('Use Elemailer Lite', 'elemailer-lite'),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => esc_html__('Yes', 'elemailer-lite'),
                'label_off' => esc_html__('No', 'elemailer-lite'),
                'return_value' => 'yes',
                'default' => 'no',
            ]
        );

        $element->add_control(
            'select_elemailer_email_template',
            [
                'label' => esc_html__('Select elemailer lite template', 'elemailer-lite'),
                'type' => Controls_Manager::SELECT,
                'label_block' => true,
                'options' => \Elemailer_Lite\App\Form_Template\Action::instance()->get_all_template(),
                'condition' => [
                    'show_elemailer_email_template_selector' => 'yes',
                ],
            ]
        );
    }

    /**
     * option for selecting elemailer lite template function in form widget
     *
     * @param object $element
     * @param array $args
     * @since 1.0.0
     */
    public function email2_template_selector($element, $args)
    {
        // add a control
        $element->add_control(
            'show_elemailer_email_template_selector_2',
            [
                'label' => esc_html__('Use elemailer lite template', 'elemailer-lite'),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => esc_html__('Yes', 'elemailer-lite'),
                'label_off' => esc_html__('No', 'elemailer-lite'),
                'return_value' => 'yes',
                'default' => 'no',
            ]
        );

        $element->add_control(
            'select_elemailer_email_template_2',
            [
                'label' => esc_html__('Select elemailer lite template', 'elemailer-lite'),
                'type' => Controls_Manager::SELECT,
                'label_block' => true,
                'options' => \Elemailer_Lite\App\Form_Template\Action::instance()->get_all_template(),
                'condition' => [
                    'show_elemailer_email_template_selector_2' => 'yes',
                ],
            ]
        );
    }
    
    /**
     * Remove the container experiement. TO DO: in future further modification will be needed for sure.
     * @since 1.2
     */
    public function remove_container_experiment( $document ) {
        if(isset( $_GET['post']) && in_array( get_post_type($_GET['post']), ['em-form-template', 'em-emails-template'] ) ){
          
        $document->remove_feature( 'container'); 

         // @since 1.8 we no longer need it because of remove_all_experiement_requirement system. we will remove it later
         // patch for fixing fatal error on elementor loop builder as it reqires container. So we are initializing fake loop
         // require_once('container.php');

        add_filter( 'pre_option_elementor_experiment-editor_v2', function() {
            return 'inactive';
             } );

         
          }
    }

    
    public function remove_all_experiment_requirement( $settings ) {
          if(isset( $_GET['post']) && in_array( get_post_type($_GET['post']), ['em-form-template', 'em-emails-template'] ) ){
           
            $settings['experiments']='';
            
            return $settings; //return empty expreiemt list for our post types
         
          }
          return $settings; // return original value
    }

    // passed array, id of the page
    public function register_document_config($obj, $id){

        if(!in_array( get_post_type($id), ['em-form-template', 'em-emails-template'] ) ){
            return;
        } 

        $obj['settings']['panelPage']['title'] = __('Email Settings','elemailer');
        $obj['urls']['exit_to_dashboard'] =  empty(wp_get_referer()) ? admin_url() : wp_get_referer();
        
        return $obj;
    }


    /**
     * custom control add in section of elementor for adding background
     *
     * @param object $element
     * @param array $args
     * @return void
     * @since 1.0.0
     */
    public function section_background($element, $args)
    {
        // return if this is not post type of elemailer
        $post_type = get_post_type();

        if (
            ! in_array( $post_type, ['em-form-template', 'em-emails-template'] ) &&
            \Elementor\Plugin::$instance->editor->is_edit_mode()
        ) {
            return;
        }

        $element->add_control(
            'section_background_type',
            [
                'label' => __('Background Type', 'elemailer-lite'),
                'type' => Controls_Manager::SELECT,
                'separator' => 'before',
                'default' => 'color',
                'classes' => 'elemailer-section-control',
                'options' => [
                    'color'  => __('Color', 'elemailer-lite'),
                    'image' => __('Image', 'elemailer-lite'),
                ],
            ]
        );

        $element->add_control(
            'section_background_color',
            [
                'label' => __('Background Color', 'elemailer-lite'),
                'type' => Controls_Manager::COLOR,
                'classes' => 'elemailer-section-control',
                'condition' => [
                    'section_background_type' => 'color',
                ],
                'selectors' => [
                    '{{WRAPPER}} > .elementor-container' => 'background: {{VALUE}};',
                ],
                'dynamic' => [
                    'active' => false,
                ],
                'global' => [
                    'active' => false,
                ],
            ]
        );

        $element->add_control(
            'section_background_image',
            [
                'label' => __('Choose Image', 'elemailer-lite'),
                'type' => Controls_Manager::MEDIA,
                'classes' => 'elemailer-section-control',
                'default' => [
                    'url' => Utils::get_placeholder_image_src(),
                ],
                'condition' => [
                    'section_background_type' => 'image',
                ],
                'selectors' => [
                    '{{WRAPPER}} > .elementor-container' => 'background: url(\'{{URL}}\') no-repeat center;',
                ],
            ]
        );
    }
}
