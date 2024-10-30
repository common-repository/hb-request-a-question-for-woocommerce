<?php


class HB_Wp_List_Table
{
    public function __construct()
    {
        if( ! class_exists( 'WP_List_Table' ) ) {
            require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
        }
        $this->init();
    }
    public function init()
    {
        $this->HBAddAdminMenuInWoocommerce();
    }

    //<form method="post">
    private function HBAddAdminMenuInWoocommerce(){
        add_action( 'admin_menu', function(){
            add_submenu_page(
                'woocommerce',
                __('HB Question', 'hb-request-a-question-for-woocommerce'),
                __('HB Question', 'hb-request-a-question-for-woocommerce'),
                'administrator',
                HBRequestPriceURL,
                function (){
                    $exampleListTable = new HB_Request_Price_List_Table();
                    $exampleListTable->prepare_items();
                    if(empty($_GET[HBRequestId])){
                        echo '<form method="post">';
                        echo '<div class="wrap">';
                        echo '<h2>';
                        esc_html_e('Request','hb-request-a-question-for-woocommerce');
                        echo '</h2>';
                        $exampleListTable->display();
                        echo '</div>';
                        echo '</form>';
                    }elseif (is_admin() && wp_verify_nonce( wp_unslash( $_REQUEST['_wpnonce'] ), 'HB-Request-Price-List-Table' )){

                        require_once dirname(__FILE__) . '/hb-request-price-message-page.php';

                    }
                }
            );
        } );

    }

}

new HB_Wp_List_Table();


class HB_Request_Price_List_Table extends WP_List_Table
{



    public function prepare_items()
    {

        if (!defined('ABSPATH') || !current_user_can('administrator')) {
            http_response_code(404);
            die();
        }

        $per_page               = 10;
        $columns                = $this->get_columns();
        $hidden                 = array();
        //$sortable               = $this->get_sortable_columns();
        $this->_column_headers  = array($columns, $hidden);

        $currentPage            = $this->get_pagenum();
        $total_items            = $this->get_total_count();

        $offset                 = $per_page>0 ? (($currentPage-1)*$per_page) : 0;

        $data                   = $this->get_data($per_page,$offset);

        $this->requestPrice_action();



        $this->items = $data;
        $this->set_pagination_args(
            array(
                'total_items'   =>  $total_items,
                'per_page'      =>  $per_page,
                'total_pages'   =>  ceil($total_items/$per_page)
            )
        );
    }

    function get_data($per_page,$offset=0){
        global $wpdb;
        $data = array();

        $result = $wpdb->get_results($wpdb->prepare("
                                            SELECT
                                                `hb_id`,
                                                `user_id`,
                                                `hb_name`,
                                                `hb_orderid`,
                                                `hb_connection`,
                                                `hb_message`,
                                                `hb_status`,
                                                `hb_date`
                                            FROM `wp_heiblack_wc_requestprice`  ORDER BY `wp_heiblack_wc_requestprice`.`hb_date` DESC LIMIT %d,%d",$offset,$per_page));


        foreach ($result as $value){

            $hb_id          = $value->hb_id;
            $hb_orderid     = $value->hb_orderid;
            $hb_message     = $value->hb_message;
            $hb_date        = $value->hb_date;
            $hb_name        = $value->hb_name;
            $hb_status      = $value->hb_status;
            $hb_connection  = $value->hb_connection;

            $hb_message     = mb_substr( $hb_message, 0, 5, "UTF-8");


            $url            = wp_nonce_url('?page='.HBRequestPriceURL.'&'.HBRequestId.'='.esc_textarea($hb_id),'HB-Request-Price-List-Table');
            $url2           = wp_nonce_url('?'.HBRequestId.'='.esc_textarea($hb_id),'HB-Request-Price-List-Table-done');
            $url3           = wp_nonce_url('?'.HBRequestId.'='.esc_textarea($hb_id).'&hbsend=true','HB-Request-Price-List-Table-send');

            $actions        = "<a class=\"button\" href=".esc_url($url).">".esc_html('View','hb-request-a-question-for-woocommerce')."</a> ";


            if($hb_status==0){
                $hb_status_reply = __('NO Replied','hb-request-a-question-for-woocommerce');
                $status         = '<mark class="hb-order-status HBNOReplied"><span>'.esc_html($hb_status_reply).'</span></mark>';
            }elseif ($hb_status==1){
                $hb_status_reply = __('Replied','hb-request-a-question-for-woocommerce');
                $status         = '<mark class="hb-order-status HBComReplied"><span>'.esc_html($hb_status_reply).'</span></mark>';
            }else{
                $hb_status_reply = __('Completed','hb-request-a-question-for-woocommerce');
                $status         = '<mark class="hb-order-status HBCompleted" ><span>'.esc_html($hb_status_reply).'</span></mark>';

            }


            if($hb_status<=1){
                $actions       .= "<a class=\"button HB-done\" href=".esc_url($url2).">".esc_html('Done','hb-request-a-question-for-woocommerce')."</a> ";
                $actions       .= "<a class=\"button HB-Send\" href=".esc_url($url3).">".esc_html('Sned','hb-request-a-question-for-woocommerce')."</a> ";
            }









            $data[]         = array(
                'HBPRC'         => '<input type="checkbox" name="hbrequest[]" value="'.esc_textarea($hb_id).'">',
                'HB-request-id'            => esc_textarea($hb_id),
                'HB-request-orderid'       => esc_textarea($hb_orderid),
                'HB-request-name'          => esc_textarea($hb_name),
                'HB-request-message'       => esc_textarea($hb_message),
                'HB-request-connection'    => esc_textarea($hb_connection),
                'HB-request-date'          => esc_textarea($hb_date),
                'HB-request-status'        => wp_kses_post($status),
                'HB-request-actions'       => wp_kses_post($actions)
            );
        }





        return $data;

    }
    function get_bulk_actions(){
        $actions = array(
            'done'      => __('Done','hb-request-a-question-for-woocommerce'),
            'replied'   => __('Replied','hb-request-a-question-for-woocommerce'),
            'delete'    => __('Delete','hb-request-a-question-for-woocommerce'),

        );
        return $actions;
    }
    function requestPrice_action(){
        if('done' === $this->current_action() && isset($_POST['action']) && $_POST['action']=='done'){


            $count = 0;
            if(isset($_POST['hbrequest'])){
                $size = count($_POST['hbrequest']);
                if($size){
                    global $wpdb;
                    for($i = 0; $i < $size; $i++){
                        $wpdb->query(
                            $wpdb->prepare(
                                "UPDATE wp_heiblack_wc_requestprice SET hb_status= %d WHERE hb_id = %d;",
                                2,sanitize_text_field($_POST['hbrequest'][$i])
                            )
                        );
                        echo "<script>location.reload();</script>";
                    }
                }
            }


        } elseif('delete' === $this->current_action() && isset($_POST['action']) && $_POST['action']=='delete'){
            $count = 0;
            if(isset($_POST['hbrequest'])){
                $size = count($_POST['hbrequest']);
                if($size){
                    global $wpdb;
                    $table_name = 'wp_heiblack_wc_requestprice';
                    for($i = 0; $i < $size; $i++){
                        $wpdb->query(
                            $wpdb->prepare(
                                "DELETE FROM wp_heiblack_wc_requestprice WHERE hb_id = %d;",
                                sanitize_text_field($_POST['hbrequest'][$i])
                            )
                        );
                        echo "<script>location.reload();</script>";

                    }

                }
            }

        }elseif('replied' === $this->current_action() && isset($_POST['action']) && $_POST['action']=='replied'){
            $count = 0;
            if(isset($_POST['hbrequest'])){
                $size = count($_POST['hbrequest']);
                if($size){
                    global $wpdb;
                    $table_name = 'wp_heiblack_wc_requestprice';
                    for($i = 0; $i < $size; $i++){
                        $wpdb->query(
                            $wpdb->prepare(
                                "UPDATE wp_heiblack_wc_requestprice SET hb_status= %d WHERE hb_id = %d;",
                                1,sanitize_text_field($_POST['hbrequest'][$i])
                            )
                        );
                        echo "<script>location.reload();</script>";

                    }

                }
            }

        }
    }
    function get_total_count(){
        global $wpdb;
        $total_query = "SELECT COUNT(*) FROM wp_heiblack_wc_requestprice";
        $count = $wpdb->get_var( $total_query );
        return $count;
    }
    public function get_columns()
    {
        $columns = array(
            'HBPRC'         =>'<input type="checkbox" class="hb-request-a-question-all">',
            'HB-request-id'            => __('ID','hb-request-a-question-for-woocommerce'),
            'HB-request-orderid'       => __('Productid','hb-request-a-question-for-woocommerce'),
            'HB-request-name'          => __('Name','hb-request-a-question-for-woocommerce'),
            'HB-request-message'       => __('Message','hb-request-a-question-for-woocommerce'),
            'HB-request-connection'    => __('Connection','hb-request-a-question-for-woocommerce'),
            'HB-request-date'          => __('Date','hb-request-a-question-for-woocommerce'),
            'HB-request-status'        => __('Status','hb-request-a-question-for-woocommerce'),
            'HB-request-actions'       => __('Actions','hb-request-a-question-for-woocommerce')
        );

        return $columns;
    }

    public function get_sortable_columns()
    {
        //return array('date' => array('date', false));
        return array();
    }

    public function column_default( $item, $column_name )
    {
        switch( $column_name ) {
            case 'HBPRC':
            case 'HB-request-id':
            case 'HB-request-orderid':
            case 'HB-request-name':
            case 'HB-request-message':
            case 'HB-request-connection':
            case 'HB-request-date':
            case 'HB-request-status':
            case 'HB-request-actions':
                return $item[ $column_name ];
            default:
                return  false;
        }
    }

    private function sort_data()
    {


    }
}




