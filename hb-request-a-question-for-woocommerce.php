<?php
/*
 * Plugin Name: HB Request A Question For Woocommerce
 * Plugin URI: https://piglet.me/RequestQuestion
 * Description: A Request A Question Tiny For Woocommerce
 * Version: 0.2.3
 * Author: heiblack
 * Author URI: https://piglet.me
 * License:  GPL 3.0
 * Domain Path: /languages
 * License URI: http://www.gnu.org/licenses/gpl-3.0.txt
*/


class hb_request_a_question_for_woocommerce_admin
{
    public function __construct()
    {
        if (!defined('ABSPATH')) {
            http_response_code(404);
            die();
        }
        if (!function_exists('plugin_dir_url')) {
            return;
        }
        if (!function_exists('is_plugin_active')) {
            require_once(ABSPATH . 'wp-admin/includes/plugin.php');
        }
        if (!is_plugin_active('woocommerce/woocommerce.php')) {
            return;
        }
        $this->init();
    }

    public   function init()
    {

        global  $wpdb;

        define("HBRequestPriceURL","HBRequestPriceURL");
        define("HBRequestId","hbRequestId");

        $table_name = "wp_heiblack_wc_requestprice";

        //Initialization Plugin
        //Create Table ,When a plugin is activated.
        $this->HBinitIalizationRequestPrice();

        if ( $wpdb->get_var( $wpdb->prepare( "SHOW TABLES LIKE %s", $table_name ) ) == $table_name ) {
            //Add 'Setting' link  Plugin
            $this->HBAddpluginlink();
            $this->HBAddRequestPriceEnable();
            //Add RequestPriceButton in Product Page
            $this->HBAddRequestPriceButton();
            //Add New Tab in my Account Page(hb-request-price-details)
            $this->HBAddRequestPriceTab();
            //Add New Page(hb-requestprice-page)
            $this->HBAddRequestPriceSendPage();
            //Add ListTable (extends WP_List_Table)
            $this->HBTableListPage();
            //Add Setting (extends WC_Settings_Page)
            $this->HBAddAdminRequestSetting();

            //Ajax Event

            //Done Ajax Event
            $this->HBRequestPriceDone();
            //Send Reply Ajax Event
            $this->HBRequestPriceSend();
            //Update Note and Status
            $this->HBRequestPriceNote();

        }



    }

    private function HBAddpluginlink(){
        add_filter('plugin_action_links_'.plugin_basename(__FILE__), function ( $links ) {
            $links[] = '<a href="' .
                admin_url( 'admin.php?page=wc-settings&tab=hbrequest' ) .
                '">' . esc_html(__('Settings')) . '</a>';
            return $links;
        });


    }
    //Create Table**
    private  function HBinItializationRequestPrice(){
            register_activation_hook( __FILE__, function (){
                global $wpdb;
                $table_name = "wp_heiblack_wc_requestprice";
                    if ( $wpdb->get_var( $wpdb->prepare( "SHOW TABLES LIKE %s", $table_name ) ) != $table_name ) {
                        $charset_collate = $wpdb->get_charset_collate();
                        try {
                            $sql = "CREATE TABLE IF NOT EXISTS $table_name (
                                  hb_id bigint(20) NOT NULL AUTO_INCREMENT,
                                  user_id bigint(20) NOT NULL,
                                  hb_name text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
                                  hb_orderid bigint(20) NOT NULL,  
                                  hb_connection longtext CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
                                  hb_message longtext CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
                                  hb_status tinyint(4) UNSIGNED NOT NULL,
                                  hb_reply longtext CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
                                  hb_remark longtext CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,   
                                  hb_data longtext CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL, 
                                  hb_date timestamp NOT NULL DEFAULT current_timestamp(),
                                  PRIMARY KEY (hb_id)
                                ) $charset_collate";
                            require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
                            dbDelta($sql);

                        } catch (Exception $e) {
                            return fasle;
                        }
                    }

            });



    }

    private function  HBAddRequestPriceEnable(){
        add_action('woocommerce_product_options_advanced', function ()
        {
            echo '<div class="product_custom_field">';
            // Custom Product Text Field
            woocommerce_wp_checkbox(
                array(
                    'id' => '_hb_requestprice_enable',
                    'description' => esc_html(__('Enable', 'hb-request-a-question-for-woocommerce')),
                    'label' => esc_html(__('Request Price' )),
                )
            );
            echo '</div>';
        });

        add_action( 'woocommerce_process_product_meta', function ( $id, $post ){
            //Checkbox
            $woocommerce_checkbox = isset( $_POST['_hb_requestprice_enable'] ) ? 'yes' : 'no';
            update_post_meta( $id, '_hb_requestprice_enable', $woocommerce_checkbox );


        }, 10, 2 );
    }
    //Add RequestPriceButton in Product Page
    private  function HBAddRequestPriceButton(){
        add_action( 'woocommerce_product_meta_start', function (){
            global $product;

            $hb_product_id = $product->get_id();

            if(get_post_meta($hb_product_id,'_hb_requestprice_enable',true)=='yes'){

                $RequestPrice = __('RequestPrice','hb-request-a-question-for-woocommerce');

                $url = wp_nonce_url('?hb-request-price='.$hb_product_id,'hb-request-price');

                $url = get_permalink( get_option('woocommerce_myaccount_page_id') ).$url;

                echo "<a href=".esc_url($url)." class=\"button hb-button\">".esc_html($RequestPrice)."</a>";
            }

        });
    }
    //Add New Tab in my Account Page
    private  function HBAddRequestPriceTab(){
        add_action( 'init', function (){
            add_rewrite_endpoint( 'request-price', EP_ROOT | EP_PAGES );
        } );
        add_filter( 'query_vars', function($vars){
            $vars[] = 'request-price';
            return $vars;
        }, 0 );
        add_filter( 'woocommerce_account_menu_items', function ($items){
            $items['request-price'] = __('Question Record','hb-request-a-question-for-woocommerce');
            return $items;
        });
        add_action( 'woocommerce_account_request-price_endpoint', function (){
            //Details Page
            if ( isset( $_GET['hb-requestprice-details'], $_GET['_wpnonce'] ) && is_user_logged_in() && wp_verify_nonce( wp_unslash( $_GET['_wpnonce'] ), 'hb-requestprice-details' ) ) {
                wp_enqueue_style( 'HEIBLACK-ATM-SIMPLECSSR', plugin_dir_url( __FILE__ ) . 'assets/style.css' );
                //Verify order belongs
                global $wpdb;
                $currentUser = get_current_user_id();
                $verify = sanitize_text_field($_GET['hb-requestprice-details']);

                $total_query = "SELECT COUNT(*) FROM wp_heiblack_wc_requestprice WHERE hb_id =".esc_attr($verify)." AND user_id=".esc_attr($currentUser);
                $count = $wpdb->get_var( $total_query );
                //Quest Order does not match user
                if (empty($count)) {
                    esc_html_e('invalid value!', 'hb-request-a-question-for-woocommerce');
                    return;
                }
                //match
                wp_enqueue_script('HEIBLACK-request-price-user-js', plugin_dir_url(__FILE__) . 'assets/hb-requestprice-user.js');
                wp_enqueue_style( 'HEIBLACK-request-price-css', plugin_dir_url( __FILE__ ) . 'assets/style.css' );
                require_once dirname(__FILE__) . '/page/hb-request-price-details.php';

                return;
            }
            //Home Page
            wp_enqueue_style( 'HEIBLACK-request-price-css', plugin_dir_url( __FILE__ ) . 'assets/style.css' );
            require_once dirname(__FILE__) . '/page/hb-request-price-table.php';
        });

    }
    //Add New Page
    private  function HBAddRequestPriceSendPage(){
        add_action( 'init', function () {
            add_rewrite_endpoint( 'hb-request-price', EP_ROOT | EP_PAGES );
        });
        add_action( 'woocommerce_account_hb-request-price_endpoint', function ($post) {
            if ( isset( $_GET['hb-request-price'], $_GET['_wpnonce'] ) && is_user_logged_in() && wp_verify_nonce( wp_unslash( $_GET['_wpnonce'] ), 'hb-request-price' ) ) {
                wp_enqueue_script('HEIBLACK-request-price-user-js', plugin_dir_url(__FILE__) . 'assets/hb-requestprice-user.js');
                require_once dirname(__FILE__) . '/page/hb-requestprice-page.php';

            }

        });


    }
    //Add ListTable
    private  function HBTableListPage(){

        require_once dirname(__FILE__) . '/page/hb-request-price-list-table.php';

        if(!empty($_GET["page"]) && $_GET["page"]==HBRequestPriceURL){
            wp_enqueue_script('HEIBLACK-request-price-js', plugin_dir_url(__FILE__) . 'assets/hb-requestprice.js');

            wp_enqueue_style( 'HEIBLACK-request-price-css', plugin_dir_url( __FILE__ ) . 'assets/style.css' );


        }

    }

    private function HBAddAdminRequestSetting(){
        add_filter( 'woocommerce_get_settings_pages', function ( $settings ) {
            $settings[] = require_once dirname(__FILE__) . '/page/hb-settings.php';
            return $settings;
        } );


    }




    //Done Ajax Event(Use Confirm)
    private  function HBRequestPriceDone(){
        add_action('wp_ajax_hb_done_requestPrice_action', function (){
            if ( current_user_can( 'administrator' ) ) {
                global $wpdb;

                if ( isset( $_POST['hbRequestId'], $_POST['wpnonce'] ) && wp_verify_nonce( wp_unslash( $_POST['wpnonce'] ), 'HB-Request-Price-List-Table-done' ) ) {

                    $hbRequestId    = sanitize_text_field($_POST['hbRequestId']);

                    $result = $wpdb->query(
                        $wpdb->prepare(
                            "UPDATE wp_heiblack_wc_requestprice SET hb_status = %d WHERE hb_id = %d;",
                            2, sanitize_text_field($hbRequestId)
                        )
                    );

                    return $result;
                }

                return false;

            }





            die();
        });
    }
    //Done Ajax Send Reply (Use Prompt)
    private  function HBRequestPriceSend(){
        add_action('wp_ajax_hb_send_requestPrice_action', function (){
            if ( current_user_can( 'administrator' ) ) {
                global $wpdb;

                if ( isset( $_POST['hbRequestId'], $_POST['wpnonce'] ) && wp_verify_nonce( wp_unslash( $_POST['wpnonce'] ), 'HB-Request-Price-List-Table-send' ) ) {

                    $hbRequestId    = sanitize_text_field($_POST['hbRequestId']);
                    $hbreply        = sanitize_text_field($_POST['message']);

                    $result             = $wpdb->get_results($wpdb->prepare("
                                            SELECT
                                            `hb_data`
                                            FROM `wp_heiblack_wc_requestprice`  WHERE hb_id =%d",esc_html($hbRequestId)));


                    $hb_data = sanitize_text_field($result->hb_data);

                    $hb_data = unserialize($hb_data);

                    $hb_data['Customerread'] = 3;



                    if(get_option('hb_request_a_price_auto')=='yes'){

                        $wpdb->query(
                            $wpdb->prepare(
                                "UPDATE wp_heiblack_wc_requestprice SET hb_reply = %s ,hb_status = %d,hb_data = %s WHERE hb_id = %d;",
                                sanitize_text_field($hbreply),1,serialize($hb_data),sanitize_text_field($hbRequestId)
                            )
                        );

                        die('100');
                    }

                    $wpdb->query(
                        $wpdb->prepare(
                            "UPDATE wp_heiblack_wc_requestprice SET hb_reply = %s ,hb_data = %s WHERE hb_id = %d;",
                            sanitize_text_field($hbreply),serialize($hb_data), sanitize_text_field($hbRequestId)
                        )
                    );
                    die('0');
                }
                die('1');
            }
            die('2');
        });
    }

    private  function HBRequestPriceNote(){
        add_action('wp_ajax_hb_Note_requestPrice_action', function (){
            if ( current_user_can( 'administrator' ) ) {

                if ( isset( $_POST['hbRequestId'])) {

                    global $wpdb;

                    $hbStatus       = sanitize_text_field($_POST['hbRequestStatus']);
                    $hbNote         = sanitize_text_field($_POST['hbRequestNote']);
                    $hbRequestId    = sanitize_text_field($_POST['hbRequestId']);
                    $hborderid      = sanitize_text_field($_POST['hborderid']);
                    $hbuserid       = sanitize_text_field($_POST['hbuserid']);


                    $total_query    = "SELECT COUNT(*) FROM wp_heiblack_wc_requestprice WHERE hb_id =".esc_attr($hbRequestId). " AND hb_orderid=".esc_attr($hborderid). " AND user_id=".esc_attr($hbuserid);
                    $count          = $wpdb->get_var( $total_query );

                    if(!$count){

                        die('900');
                    }

                    $result = $wpdb->query(
                        $wpdb->prepare(
                            "UPDATE wp_heiblack_wc_requestprice SET hb_status = %d , hb_remark = %s WHERE hb_id = %d;",
                            sanitize_text_field($hbStatus),sanitize_text_field($hbNote), sanitize_text_field($hbRequestId)
                        )
                    );

                    die('0');

                }

            }





            die();
        });
    }

}




new hb_request_a_question_for_woocommerce_admin();



