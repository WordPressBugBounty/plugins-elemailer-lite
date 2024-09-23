<?php

namespace Elemailer_Lite\Integrations\Shortcode\Actions;

defined('ABSPATH') || exit;

/**
 * Shortcode hooks related class
 *  
 * @author elEmailer 
 * @since 1.0.3
 */
class Hooks
{
    use \Elemailer_Lite\Traits\Singleton;

    /**
     * inilial class for shortcode hooks function
     *
     * @return void
     * @since 1.0.3
     */
    public function init()
    {

    // Cf7 shortcode support
    add_action( 'wpcf7_before_send_mail', [$this, 'wpcf7_support_elemailer_shortcode'], 10, 1);

    }

    /**
     * Function to replace mail, mail2 shortcode inside cf7 form.
     *
     * @param $contact_form object 
     * @return $contact_form object
     * @since 1.0.3
     */

    public function wpcf7_support_elemailer_shortcode( $contact_form )
    {
        $mail = $contact_form->prop( 'mail' );
        $mail_2 = $contact_form->prop( 'mail_2' );


        // Body content
        $mail['body'] =  $this->process_elemailer_shortcode($mail['body']);
        $mail_2['body'] =  $this->process_elemailer_shortcode($mail_2['body']);
       
        // Update the mail 
        $contact_form->set_properties(array('mail' => $mail));
        $contact_form->set_properties(array('mail_2' => $mail_2));
    }

    /**
     * Only return elemailer template and remove everything else unless we enable the last line commented
     *
     * @param string[shortcode] $shortcode 
     * @return string[html] $html
     * @since 1.0.1
     */
    public function process_elemailer_shortcode($shortcode)
    {

        $status = preg_match_all('(\[elemailer:id="[0-9]+"])', $shortcode, $data, PREG_SET_ORDER);

        // always return the shortcode if it doesn't match your own!
        if (!$status) return $shortcode;

        $match = isset($data[0][0]) ? $data[0][0] : false;
        $id = rtrim(ltrim($match, '[elemailer:id="'), '"]');
        
        if ( get_post_status ( $id ) !== 'publish' ) return $shortcode;

        $html = \Elemailer_Lite\Helpers\Util::get_template_content($id);
        $html = \Elemailer_Lite\Helpers\Util::get_email_html_template($id, $html);

        // if we want to allow other content we need to uncomment it.
        //$html=preg_replace('(\[elemailer:id="[0-9]+"])', $html, $shortcode);

        return $html;
    }






}
