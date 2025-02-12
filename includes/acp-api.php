<?php
if (!defined('ABSPATH')) exit;

if (!session_id()) {
    session_start();
}

// Đăng ký API endpoints
add_action('rest_api_init', function() {
    register_rest_route('ppc/v1', '/check-source', array(
        'methods' => 'GET',
        'callback' => 'ppc_check_source',
    ));
    register_rest_route('ppc/v1', '/generate-password', array(
        'methods' => 'GET',
        'callback' => 'ppc_api_generate_password',
    ));
    register_rest_route('ppc/v1', '/check-password', array(
        'methods' => 'POST',
        'callback' => 'ppc_api_check_password',
    ));
});

function ppc_api_generate_password(WP_REST_Request $request) {
    $password_length = get_option('ppc_password_length', 8);
    $random_password = ppc_generate_random_password($password_length);
    $_SESSION['ppc_random_password'] = $random_password;
    return new WP_REST_Response(array('password' => $random_password), 200);
}

function ppc_api_check_password(WP_REST_Request $request) {
    $params = $request->get_json_params();
    $input_password = isset($params['password']) ? sanitize_text_field($params['password']) : '';

    $password_type = get_option('ppc_password_type', 'fixed');
    if ($password_type === 'random') {
        $stored_password = $_SESSION['ppc_random_password'];
    } else {
        $stored_password = get_option('ppc_fixed_password');
    }

    if ($input_password === $stored_password) {
        $_SESSION['ppc_authenticated'] = true;
        return new WP_REST_Response(array('status' => 'success'), 200);
    } else {
        return new WP_REST_Response(array('status' => 'error'), 403);
    }
}

function ppc_check_source() {
    $cookie_name = 'source';
    $source = isset($_COOKIE[$cookie_name]) ? $_COOKIE[$cookie_name] : '';
    return new WP_REST_Response(array('source' => $source), 200);
}
?>