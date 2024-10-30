<?php echo '<h3>'.esc_html(__('Record', 'hb-request-a-question-for-woocommerce')).'</h3>';?>
<table>
    <tr>
        <th style="width: 20%;"><?php esc_html_e(__('NUMBER', 'hb-request-a-question-for-woocommerce')) ?></th>
        <th><?php esc_html_e('PRODUCT', 'hb-request-a-question-for-woocommerce') ?></th>
        <th><?php esc_html_e('DATE', 'hb-request-a-question-for-woocommerce') ?></th>
        <th><?php esc_html_e('STATE', 'hb-request-a-question-for-woocommerce') ?></th>
        <th><?php esc_html_e('ACTIONS', 'hb-request-a-question-for-woocommerce') ?></th>
    </tr>
    <?php
    global $wpdb;
    $currentUser = get_current_user_id();


    $total_query = "SELECT COUNT(*) FROM wp_heiblack_wc_requestprice WHERE user_id = $currentUser";
    $count = $wpdb->get_var( $total_query );

    $per = 5;

    //get the next integer not less than the value
    $pages = ceil($count/$per);

    if (!isset($_GET["pages"])){
        $page=1;
    } else {
        //Confirm that the number of pages can only be numerical data
        $page = intval(sanitize_text_field($_GET["pages"]));
    }
    if($page==0){
        $page=1;
    };
    if($page>$pages){
        $page= $pages;
    }
   //Data serial number at the beginning of each page
    $start = ($page-1)*$per;



    $result             = $wpdb->get_results($wpdb->prepare("
                                            SELECT 
                                                `hb_id`,
                                                `hb_orderid`,
                                                `hb_date`,
                                                `hb_status`,
                                                `hb_reply`,
                                                `hb_data`,
                                                `hb_date`    
                                            FROM 
                                                wp_heiblack_wc_requestprice 
                                            WHERE user_id = %d  
                                            ORDER BY `hb_date` 
                                            DESC LIMIT %d,%d ",esc_html($currentUser),esc_html($start),esc_html($per)));



    foreach ($result as $value){


        $hb_id = $value->hb_id;

        $hb_orderid = $value->hb_orderid;

        $hb_date = $value->hb_date;

        if($value->hb_status==1){
            $hb_status = __('Replied', 'hb-request-a-question-for-woocommerce');
        }elseif ($value->hb_status==2){
            $hb_status = __('Completed', 'hb-request-a-question-for-woocommerce');
        }else{
            $hb_status = __('Pending', 'hb-request-a-question-for-woocommerce');
        }


        $view_url = wp_nonce_url( add_query_arg( 'hb-requestprice-details', $hb_id ), 'hb-requestprice-details' );

        $order_url =  get_permalink( $hb_orderid );

        $order_title = get_the_title($hb_orderid);

        $order_title = mb_substr( $order_title, 0, 5, "UTF-8");

        echo '<tr>';
        echo "<td>#".esc_html($hb_id)."</td>";
        echo '<td><a href="'.esc_url($order_url).'">'.esc_html($order_title).'</a></td>';
        echo "<td>".esc_html($hb_date)."</td>";
        echo "<td>".esc_html($hb_status)."</td>";


        $hb_data = $value->hb_data;

        $hb_data = unserialize($hb_data);

        if($hb_data['Customerread']=='3'){
            echo '<td><span class="hb-relative"><a class="hb-btn hb-btn-primary hb-has-reply" href='.esc_url($view_url).'>'.esc_html(__('View', 'hb-request-a-question-for-woocommerce')).'</a></span></td>';
        }else{
            echo '<td><span class="hb-relative"><a class="hb-btn hb-btn-primary" href='.esc_url($view_url).'>'.esc_html(__('View', 'hb-request-a-question-for-woocommerce')).'</a></span></td>';
        }


        echo '<tr>';
    }



    ?>

</table>

<?php

$HBrequest_price_url = get_permalink().'request-price';

for( $i=1 ; $i<=$pages ; $i++ ) {
    if ( $page-5 < $i && $i < $page+5 ) {
        echo "<a href=".esc_url($HBrequest_price_url)."/?pages=".esc_attr($i).">".esc_attr($i)."</a> ";
    }
}