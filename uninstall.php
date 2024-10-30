<?php
if( ! defined ('WP_UNINSTALL_PLUGIN') )
    exit();
function wc_hb_requestprice_delete_plugin(){
    global $wpdb;

    $table_name = $wpdb->prefix . "heiblack_wc_requestprice";
    //delete option settings
    $wpdb->query( "DROP TABLE IF EXISTS $table_name" );
    delete_option("_HBinItializationRequestPrice");
}

wc_hb_requestprice_delete_plugin();