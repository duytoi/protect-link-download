<?php
if (!defined('ABSPATH')) exit;

global $ppc_db_version;
$ppc_db_version = '1.0';

// Tạo bảng database khi cài đặt plugin
function ppc_install() {
    global $wpdb;
    global $ppc_db_version;
    
    $table_name = $wpdb->prefix . 'ppc_protected_content';
    
    $charset_collate = $wpdb->get_charset_collate();
    
    $sql = "CREATE TABLE $table_name (
      id mediumint(9) NOT NULL AUTO_INCREMENT,
      type varchar(55) NOT NULL,
      summary text NOT NULL,
      content text NOT NULL,
      password varchar(55) NOT NULL,
      countdown_time int NOT NULL,
      countdown_repeat int NOT NULL,
      created_at datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
      PRIMARY KEY  (id)
    ) $charset_collate;";
    
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
    
    add_option('ppc_db_version', $ppc_db_version);
}

// Hook vào kích hoạt plugin
register_activation_hook(__FILE__, 'ppc_install');
?>