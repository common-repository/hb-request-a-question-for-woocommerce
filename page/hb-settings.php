<?php


class hb_request_a_question_for_woocommerce_settings extends WC_Settings_Page {
    public function __construct() {
        $this->id    = 'hbrequest';
        $this->label = esc_html(__( 'HB Request', 'hb-request-a-question-for-woocommerce' ));
        parent::__construct();
    }
    public function get_sections() {
        $sections = array(
            ''              => esc_html(__( 'General', 'hb-request-a-question-for-woocommerce' )),
        );
        return apply_filters( 'woocommerce_get_sections_' . $this->id, $sections );
    }
    public function output() {
        global $current_section;

        $settings = $this->get_settings( $current_section );
        WC_Admin_Settings::output_fields( $settings );
    }
    public function save() {
        global $current_section;
        $settings = $this->get_settings( $current_section );
        WC_Admin_Settings::save_fields( $settings );

        if ( $current_section ) {
            do_action( 'woocommerce_update_options_' . $this->id . '_' . $current_section );
        }
    }
    public function get_settings( $current_section = '' ) {
        $settings[] = [
            'name' => esc_html(__('Setting', 'hb-request-a-question-for-woocommerce')),
            'type' => 'title',
            'desc' => '',
        ];
        $settings[] = [
            'desc' => esc_html(__('Send automatically changes status to “Reply”', 'hb-request-a-question-for-woocommerce')),
            'type'  => 'checkbox',
            'id'    => 'hb_request_a_price_auto',
        ];
        $settings[] = [
            'desc'      => esc_html(__('Allow customer to update the contact form', 'hb-request-a-question-for-woocommerce')),
            'type'      => 'checkbox',
            'id'        => 'hb_request_a_price_update',
        ];
        $settings[] = [
            'desc' => esc_html(__('When the form status is complete,allow customers to update the contact form', 'hb-request-a-question-for-woocommerce')),
            'type'  => 'checkbox',
            'id'    => 'hb_request_a_price_update_complete',
        ];
        $settings[] = [
            'type'  => 'sectionend',
            'id'    => 'hb_request_a_price_setting',
        ];
        $settings[] = [
            'name' => esc_html(__('Notice', 'hb-request-a-question-for-woocommerce')),
            'type' => 'title',
            'desc' => '',
        ];
        $settings[] = [
            'desc'  => esc_html(__('LINE Notify(Recommend)', 'hb-request-a-question-for-woocommerce')),
            'type'  => 'checkbox',
            'id'    => 'hb_request_a_price_lINE_notify',
        ];
        $settings[] = [
            'type'  => 'text',
            'id'    => 'hb_request_a_price_lINE_notify_token',
            'desc'  => esc_html(__( 'LINE Notify Token','hb-request-a-question-for-woocommerce')),
        ];
        $settings[] = [
            'desc'  => esc_html(__('WP Mail', 'hb-request-a-question-for-woocommerce')),
            'type'  => 'checkbox',
            'id'    => 'hb_request_a_price_wc_mail_notify',
        ];
        $settings[] = [
            'type'  => 'email',
            'desc'  => esc_html(__( 'Mail Address','hb-request-a-question-for-woocommerce')),
            'id'    => 'hb_request_a_price_mail_Address'
        ];
        $settings[] = [
            'type'  => 'text',
            'id'    => 'hb_request_a_price_Mail_Title_Address',
            'desc'  => esc_html(__( 'Mail Title','hb-request-a-question-for-woocommerce')),
        ];
        $settings[] = [
            'type'  => 'sectionend',
            'id'    => 'hb_request_a_price_notice',
        ];
        $settings[] = [
            'name' => esc_html(__('Content', 'hb-request-a-question-for-woocommerce')),
            'type' => 'title',
        ];
        $settings[] = [
            'type'  => 'textarea',
            'id'    => 'hb_request_a_price_notice_content',
            'desc'  => esc_html(__( 'Content','hb-request-a-question-for-woocommerce')),
        ];
        $settings[] = [
            'type'  => 'sectionend',
            'id'    => 'hb_request_a_price',
        ];
        return apply_filters( 'woocommerce_get_settings_' . $this->id, $settings, $current_section );
    }
}

new hb_request_a_question_for_woocommerce_settings();