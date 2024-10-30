<?php
if (!defined('ABSPATH') || !is_user_logged_in()) {
    http_response_code(404);
    die();
}
global $wpdb;

if(wp_verify_nonce( @$_POST['_wpnonce'], '_hb-request-a-question') && @$_POST['requestpriceid']){

    $currentUser    = get_current_user_id();

    $hb_id          = sanitize_text_field($_POST['requestpriceid']);

    //$currentUser

    $total_query    = "SELECT COUNT(*) FROM wp_heiblack_wc_requestprice WHERE user_id = ".esc_attr($currentUser). " AND hb_id =".esc_attr($hb_id);
    $count          = $wpdb->get_var( $total_query );

    if (empty($count)) {
        esc_html_e('invalid value!', 'hb-request-a-question-for-woocommerce');
        return;
    }

    $hb_requestpricemessage     = mb_substr( sanitize_text_field($_POST['requestpricemessage']), 0, 250, "UTF-8");
    $hb_requestpricename     = mb_substr( sanitize_text_field($_POST['requestpricename']), 0, 10, "UTF-8");
    $hb_requestpriceconnection     = mb_substr( sanitize_text_field($_POST['requestpriceconnection']), 0, 30, "UTF-8");

    $result         = $wpdb->query(
        $wpdb->prepare(
            "UPDATE wp_heiblack_wc_requestprice SET hb_message = %s , hb_name = %s , hb_connection = %s WHERE hb_id = %d;",
            sanitize_textarea_field($hb_requestpricemessage),
            sanitize_textarea_field($hb_requestpricename),
            sanitize_textarea_field($hb_requestpriceconnection),
            sanitize_text_field($_POST['requestpriceid'])
        )
    );

}

$hbRequestId        = sanitize_text_field($_GET['hb-requestprice-details']);
$result             = $wpdb->get_results($wpdb->prepare("
                                            SELECT
                                            `hb_id`,
                                            `hb_orderid`,
                                            `hb_name`,
                                            `hb_connection`,
                                            `hb_message`,
                                            `hb_reply`,
                                            `hb_status`,
                                            `hb_data`
                                            FROM `wp_heiblack_wc_requestprice`  WHERE hb_id =%d",esc_html($hbRequestId)));







if (!$result) return;
$result = $result[0];

//read
$order = wc_get_product( sanitize_text_field($result->hb_orderid));

$hb_data = sanitize_text_field($result->hb_data);

$hb_data = unserialize($hb_data);


if( $hb_data['Customerread']=='3'){
    $hb_data['Customerread'] = 1;

    $wpdb->query(
        $wpdb->prepare(
            "UPDATE wp_heiblack_wc_requestprice SET hb_data = %s WHERE hb_id = %d;",
            serialize($hb_data), sanitize_text_field($hbRequestId)
        )
    );
}

?>
<figure>
    <?php if(!empty($order->id)&& $order->status=='publish'): ?>
    <?php $imgurl = wp_get_attachment_url( $order->get_image_id());?>
    <?php echo "<img src=".esc_url($imgurl).") style='width: 100px;height: auto'>";?>
    <figcaption>
        <?php esc_html_e('Product:','hb-request-a-question-for-woocommerce');?>
        <?php echo esc_textarea($order->name);?>
    </figcaption>
    <figcaption>
        <?php esc_html_e('Price:','hb-request-a-question-for-woocommerce');?>
        <?php echo esc_textarea($order->price);?>
    </figcaption>
</figure>
<?php else: ?>
    <?php return;?>
<?php endif; ?>
<?php if($result->hb_reply): ?>
    <div class="alertinfo" role="alert">
        <?php echo  esc_textarea($result->hb_reply);?>
    </div>
<?php endif;?>
<?php if(get_option('hb_request_a_price_update')=='yes' && ($result->hb_status !='2' || get_option('hb_request_a_price_update_complete')=='yes')):?>
    <form action="" method="post" id="hbrequestprice">
        <input type="hidden" name="requestpriceid" value="<?php echo esc_html($result->hb_id); ?>">
        <h5> <?php esc_html_e('Name','hb-request-a-question-for-woocommerce');?></h5>
        <input id="hb-requestprice-name" type="text" name="requestpricename" value="<?php echo esc_textarea($result->hb_name);?>">
        <p><span  id="requestpricename_word">0</span>/10</p>
        <h5> <?php esc_html_e('Contact Method','hb-request-a-question-for-woocommerce');?></h5>
        <input id="hb-requestprice-connection" type="text" name="requestpriceconnection" value="<?php echo esc_textarea($result->hb_connection);?>">
        <p><span  id="requestpriceconnection_word">0</span>/30</p>
        <h5> <?php esc_html_e('Message','hb-request-a-question-for-woocommerce');?></h5>
        <textarea id="hb-requestprice-message" name="requestpricemessage"><?php echo esc_textarea($result->hb_message);?></textarea>
        <p><span  id="requestpricemessage_word">0</span>/250</p>
        <?php wp_nonce_field( '_hb-request-a-question');?>
        <input type="submit" value="<?php esc_html_e('Update','hb-request-a-question-for-woocommerce'); ?>">
    </form>
<?php else: ?>
    <h5> <?php esc_html_e('Name','hb-request-a-question-for-woocommerce');?></h5>
    <?php echo esc_textarea($result->hb_name);?>
    <h5> <?php esc_html_e('Contact Method','hb-request-a-question-for-woocommerce');?></h5>
    <?php echo esc_textarea($result->hb_connection);?>
    <h5> <?php esc_html_e('Message','hb-request-a-question-for-woocommerce');?></h5>
    <textarea name="requestpricemessage" disabled="disabled"><?php echo esc_textarea($result->hb_message);?></textarea>

<?php endif;?>
