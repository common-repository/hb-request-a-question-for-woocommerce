<?php
    if (!defined('ABSPATH') || !is_user_logged_in()) {
        http_response_code(404);
        die();
    }

    if(wp_verify_nonce( @$_POST['_wpnonce'], '_hb-request-a-question')
        && @$_GET['hb-request-price']
        && @$_POST['requestpricename']
        && @$_POST['requestpriceconnection']
        && @$_POST['requestpricemessage']){
        $hb_requestpricename = sanitize_text_field($_POST['requestpricename']);
        $hb_requestpriceconnection = sanitize_text_field($_POST['requestpriceconnection']);
        $hb_requestpricemessage = sanitize_text_field($_POST['requestpricemessage']);
        $hb_requestprice = sanitize_text_field($_GET['hb-request-price']);
        $hb_currentUser = get_current_user_id();


        global $wpdb;
        try {
            $hb_data    = array();
            $hb_data['Customerread'] = 0;
            $hb_data['Adminread'] = 0;

            $hb_data = serialize($hb_data);


            $hb_requestpricename = mb_substr($hb_requestpricename, 0, 10, 'UTF-8');
            $hb_requestpriceconnection = mb_substr($hb_requestpriceconnection, 0, 30, 'UTF-8');
            $hb_requestpricemessage = mb_substr($hb_requestpricemessage, 0, 250, 'UTF-8');



            $result = $wpdb->insert('wp_heiblack_wc_requestprice',
                array(
                    'hb_orderid'=>sanitize_text_field($hb_requestprice),
                    'user_id'=>sanitize_text_field($hb_currentUser),
                    'hb_name'=>sanitize_text_field($hb_requestpricename),
                    'hb_connection'=>sanitize_text_field($hb_requestpriceconnection),
                    'hb_message'=>sanitize_text_field($hb_requestpricemessage),
                    'hb_status'=>0,
                    'hb_data'=>$hb_data,
                ),
                array('%d','%d','%s','%s','%s','%d','%s'));

            $hb_request_a_price_lINE_notify     = get_option('hb_request_a_price_lINE_notify');
            $hb_request_a_price_wc_mail_notify  = get_option('hb_request_a_price_wc_mail_notify');
            if($hb_request_a_price_lINE_notify == 'yes' || $hb_request_a_price_wc_mail_notify == 'yes'){
                $hb_request_a_price_lINE_notify_token       = get_option('hb_request_a_price_lINE_notify_token');
                $hb_request_a_price_notice_content    = get_option('hb_request_a_price_notice_content');
                $HB_userdata                = get_userdata($hb_currentUser);
                $HB_nicename                = $HB_userdata->user_nicename;

                $hb_requestpricemessage = mb_substr($hb_requestpricemessage, 0, 10, 'UTF-8');

                $message = str_replace('[[user]]', esc_textarea($HB_nicename), esc_textarea($hb_request_a_price_notice_content));
                $message = str_replace('[[name]]', esc_textarea($hb_requestpricename), $message);
                $message = str_replace('[[connection]]',  esc_textarea($hb_requestpriceconnection), $message);
                $message = str_replace('[[message]]',  esc_textarea($hb_requestpricemessage), $message);

                if ($hb_request_a_price_lINE_notify == 'yes') {
                    $request_params = array(
                        "headers" => "Authorization: Bearer ".esc_textarea($hb_request_a_price_lINE_notify_token),
                        "body" => array(
                            "message" => esc_textarea($message)
                        )
                    );
                    $result = wp_remote_post('https://notify-api.line.me/api/notify', $request_params);
                }

                if ($hb_request_a_price_wc_mail_notify == 'yes') {

                    $hb_request_a_price_mail_Address   = get_option('$hb_request_a_price_mail_Address');
                    $hb_request_a_price_Mail_Title_Address        = get_option('hb_request_a_price_Mail_Title_Address');

                    wp_mail(esc_attr($hb_request_a_price_mail_Address), esc_attr($hb_request_a_price_Mail_Title_Address), esc_textarea($message));
                }
            }

            echo "<script>alert('success');</script>";

            $url = get_permalink( get_option('woocommerce_myaccount_page_id') ).'request-price/';

             echo "<script>window.location.replace('".esc_url($url)."');</script>";
        }catch (Exception $e){
        }

    }

    $hb_requestprice = sanitize_text_field($_GET['hb-request-price']);



    $product = wc_get_product( esc_attr($hb_requestprice) );




    ?>
<figure>
    <?php if(!empty($product->id)&& $product->status=='publish'): ?>
    <?php $imgurl = wp_get_attachment_url( $product->get_image_id());?>
    <?php echo "<img src=".esc_url($imgurl).") style='width: 100px;height: auto'>";?>
    <figcaption>
        <?php esc_html_e('Product:','hb-request-a-question-for-woocommerce');?>
        <?php echo esc_textarea($product->name);?>
    </figcaption>
    <figcaption>
        <?php esc_html_e('Price:','hb-request-a-question-for-woocommerce');?>
        <?php echo esc_textarea($product->price);?>
    </figcaption>
</figure>
<?php else: ?>
    <?php return;?>
<?php endif; ?>


<form action="" method="post" id="hbrequestprice">
    <h5> <?php esc_html_e('Name','hb-request-a-question-for-woocommerce');?></h5>
    <input id="hb-requestprice-name" type="text" name="requestpricename" value="">
    <p><span id="requestpricename_word"></span>/10</p>
    <h5> <?php esc_html_e('Contact Method','hb-request-a-question-for-woocommerce');?></h5>
    <input id="hb-requestprice-connection" type="text" name="requestpriceconnection" value="">
    <p><span id="requestpriceconnection_word"></span>/30</p>
    <h5> <?php esc_html_e('Remark','hb-request-a-question-for-woocommerce');?></h5>
    <textarea id="hb-requestprice-message" name="requestpricemessage"></textarea>
    <p><span id="requestpricemessage_word"></span>/250</p>
    <?php wp_nonce_field( '_hb-request-a-question');?>
    <input type="submit" value="<?php esc_html_e('Send','hb-request-a-question-for-woocommerce'); ?>">

</form>
