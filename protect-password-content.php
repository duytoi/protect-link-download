<?php
/*
Plugin Name: Protect Password Content
Description: Protect content with passwords, countdowns, and Google referral checks.
Version: 1.0
Author: Your Name
*/

if (!defined('ABSPATH')) exit;

define('PPC_DIR', plugin_dir_path(__FILE__));

include_once PPC_DIR . 'includes/acp-settings.php';
include_once PPC_DIR . 'includes/acp-functions.php';
include_once PPC_DIR . 'includes/acp-api.php';
include_once PPC_DIR . 'includes/acp-database.php';

register_activation_hook(__FILE__, 'ppc_install');
register_activation_hook(__FILE__, 'ppc_activate_plugin');
register_deactivation_hook(__FILE__, 'ppc_deactivate_plugin');

function ppc_activate_plugin() {
    // Actions on plugin activation
}

function ppc_deactivate_plugin() {
    // Actions on plugin deactivation
}
?>