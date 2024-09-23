<?php

namespace Elemailer_Lite\App\Form_Template;

defined('ABSPATH') || exit;

/**
 * template related base class for initialization
 * used for creating cpt, api
 * 
 * @author elEmailer
 * @since 1.0.0
 */
class Base
{

    use \Elemailer_Lite\Traits\Singleton;

    public $template;
    public $api;

    /**
     * initialization function for all of the form template related functionality
     *
     * @return void
     * @since 1.0.0
     */
    public function init()
    {
        $this->template = new Cpt();
        $this->api = new Api();

        add_action('admin_footer', [$this, 'modal_view']);
        add_action( 'publish_em-form-template', [$this, 'our_elemailer_lite_post_number_max'],10,3);

        /**
         * Custom column in list table for showing shortcode
         *
         * @return void
         * @since 1.0.4
         */
        add_filter('manage_edit-em-form-template_columns', [$this, 'admin_column_elemailer_form_shortcode']);
        add_action( 'manage_em-form-template_posts_custom_column', [$this, 'admin_column_elemailer_form_shortcode_display'], 10, 2 );
    }

    /**
     * show admin modal function
     * pass all page id and title to modal
     *
     * @return void
     * @since 1.0.0
     */
    public function modal_view()
    {

        $screen = get_current_screen();

        if ($screen->id == 'edit-em-form-template') {

            include_once ELE_MAILER_LITE_PLUGIN_PUBLIC_DIR . '/views/template-create-modal.php';
        }
    }

     /**
     * Preparing column shortcode for the admin column / wp list table (top row)
     * We may register more column here and then use it 
     * @return $columns
     * @since 1.0.4
     */

    public function admin_column_elemailer_form_shortcode($columns){
         $columns["shortcode"] = 'Shortcode<style>th#shortcode {text-align: left;}.column-shortcode {padding-left:3%!important;} .inline-edit-em-form-template .inline-edit-status {display:none!important; }</style>';
         return $columns;
    }

    /**
     * getting the data for each column / wp list table (each column)
     * We can use switch case to compare column names ( if any ) and use here
     * @return $columns;
     * @since 1.0.1
     */

    public function admin_column_elemailer_form_shortcode_display($column, $post_id ){
        if ( $column == 'shortcode'){ 
        ?>

            <input class="elementor-shortcode-input" type="text" readonly="" onfocus="this.select()" value='[elemailer:id="<?php echo $post_id; ?>"]' >
        <?php

        }
    }

    public function our_elemailer_lite_post_number_max($post_id, $post, $old_status){

        $count_property = wp_count_posts('em-form-template')->publish;

        if ( $count_property > 3 && $old_status!=='publish' ) {
            // count too high, let's set it to not published.

            //$post = array('post_status' => 'draft');
            $post->post_status= 'draft';

            wp_update_post( $post );

        }
    }
  
}
