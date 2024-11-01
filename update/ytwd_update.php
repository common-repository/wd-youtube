<?php
function ytwd_update($version){
    global $wpdb;
    $wpdb->query("UPDATE `" . $wpdb->prefix . "ytwd_youtube` SET width_unit='percent' WHERE width_unit='%'");
}

