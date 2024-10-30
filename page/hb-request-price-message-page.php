<?php

if (!defined('ABSPATH') || !current_user_can('administrator')) {
    http_response_code(404);
    die();
}


global  $wpdb;
$hbRequestId        = sanitize_text_field($_GET['hbRequestId']);
$result             = $wpdb->get_results($wpdb->prepare("
                                            SELECT
                                               *
                                            FROM `wp_heiblack_wc_requestprice`  WHERE hb_id =%d",esc_html($hbRequestId)));



$hb_back = __( '←back', 'hb-request-a-question-for-woocommerce' );




if (!$result) return;

$result             =  $result[0];
$hb_id              =  $result->hb_id;

$url3               = wp_nonce_url('?' . HBRequestId . '=' . esc_textarea($hb_id) . '&hbsend=true&reload=true', 'HB-Request-Price-List-Table-send');


?>



<br>
<br>
<h3>
    <a href="javascript:history.back()"><?php echo esc_html($hb_back) ?></a>
</h3>
<div id="HB-product">
    <table>
        <tr>
            <th><?php esc_html_e( 'Name', 'hb-request-a-question-for-woocommerce' ); ?></th>
            <th><?php esc_html_e( 'User', 'hb-request-a-question-for-woocommerce' );?></th>
            <th><?php esc_html_e( 'Product Id', 'hb-request-a-question-for-woocommerce' );?></th>
            <th><?php esc_html_e( 'Status', 'hb-request-a-question-for-woocommerce' );?></th>
            <th><?php esc_html_e( 'Reply Read', 'hb-request-a-question-for-woocommerce' );?></th>
            <th><?php esc_html_e( 'Date Time', 'hb-request-a-question-for-woocommerce' );?></th>
        </tr>
        <tr>
            <td><?php echo esc_textarea($result->hb_name);?></td>
            <td><?php echo esc_textarea($result->user_id);?></td>
            <td><?php echo esc_textarea($result->hb_orderid);?></td>
            <td>
                <?php
                    if($result->hb_status==1){
                        esc_html_e('Replied', 'hb-request-a-question-for-woocommerce');
                    }elseif ($result->hb_status==2){
                        esc_html_e('Completed', 'hb-request-a-question-for-woocommerce');
                    }else{
                        esc_html_e('Pending', 'hb-request-a-question-for-woocommerce');
                    }
                ?>
            </td>
            <td>
                <?php

                $hb_data = sanitize_text_field($result->hb_data);

                $hb_data = unserialize($hb_data);

                if($hb_data['Customerread']==3){

                    esc_html_e('Unread', 'hb-request-a-question-for-woocommerce');


                }else if ($hb_data['Customerread']==1){

                    esc_html_e('Have Read', 'hb-request-a-question-for-woocommerce');

                }else{
                    esc_html_e('-', 'hb-request-a-question-for-woocommerce');
                }



                ?>
            </td>
            <td><?php echo esc_textarea($result->hb_date);?></td>
        </tr>
    </table>
</div>
<h2><?php esc_html_e( 'Connection:', 'hb-request-a-question-for-woocommerce' ); ?></h2>
<textarea disabled name="" id="HBconnection" ><?php echo esc_textarea($result->hb_connection); ?></textarea>
<h2><?php esc_html_e( 'Message:', 'hb-request-a-question-for-woocommerce' ); ?></h2>
<textarea disabled name="" id="hbmessage" ><?php echo esc_textarea($result->hb_message); ?></textarea>
<h2><?php esc_html_e( 'Admin Reply:', 'hb-request-a-question-for-woocommerce' ); ?></h2>
<p><?php esc_html_e( '(Customer can see)', 'hb-request-a-question-for-woocommerce' ); ?></p>


<a class="button HB-Send" href="<?php echo esc_url($url3);?>"><?php esc_html_e( 'Reply', 'hb-request-a-question-for-woocommerce' ); ?></a>
<br><br>
<textarea disabled name="" id="hbreply" ><?php echo esc_textarea($result->hb_reply); ?></textarea>
<hr>
<form action="" method="post" id="HBStatusSave">
    <input type="hidden" name="page" value="<?php echo esc_textarea($_REQUEST['page']) ?>">
    <input type="hidden" name="hbRequestId" id="hbRequestId" value="<?php echo esc_textarea($_REQUEST['hbRequestId']) ?>">
    <input type="hidden" name="_wpnonce" id="hb_wpnonce" value="<?php echo esc_textarea($_REQUEST['_wpnonce']) ?>">
    <input type="hidden" name="hbuserid" id="hbuserid" value="<?php echo esc_textarea($result->user_id) ?>">
    <input type="hidden" name="hborderid" id="hborderid" value="<?php echo esc_textarea($result->hb_orderid) ?>">

    <h2>Status:</h2>
    <select name="HBstatus" id="Status">
        <?php
        if($result->hb_status==1):?>
            <option value="0"><?php esc_html_e( 'Pending', 'hb-request-a-question-for-woocommerce' ); ?></option>
            <option value="1" selected="selected"><?php esc_html_e( 'Replied', 'hb-request-a-question-for-woocommerce' ); ?></option>
            <option value="2"><?php esc_html_e( 'Completed', 'hb-request-a-question-for-woocommerce' ); ?></option>
       <?php elseif($result->hb_status==2):?>
            <option value="0"><?php esc_html_e( 'Pending', 'hb-request-a-question-for-woocommerce' ); ?></option>
            <option value="1"><?php esc_html_e( 'Replied', 'hb-request-a-question-for-woocommerce' ); ?></option>
            <option value="2" selected="selected"><?php esc_html_e( 'Completed', 'hb-request-a-question-for-woocommerce' ); ?></option>
        <?php else:?>
            <option value="0" selected="selected"><?php esc_html_e( 'Pending', 'hb-request-a-question-for-woocommerce' ); ?></option>
            <option value="1"><?php esc_html_e( 'Replied', 'hb-request-a-question-for-woocommerce' ); ?></option>
            <option value="2"><?php esc_html_e( 'Completed', 'hb-request-a-question-for-woocommerce' ); ?></option>
        <?php endif;?>
    </select>
    <h2>Note:</h2>
    <p><?php esc_html_e( '(Only admin can read)', 'hb-request-a-question-for-woocommerce' ); ?></p>
    <textarea name="HBNote" style="width: 50%;height: 100px" id="HBNote"><?php echo esc_textarea($result->hb_remark) ?></textarea>
    <p></p>
    <input type="submit" class="button button-primary"  value="Save">
</form>
<hr>
<h3>
    <a href="javascript:history.back()"><?php echo esc_html($hb_back) ?></a>
</h3>