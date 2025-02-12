<?php
if (!defined('ABSPATH')) exit;

function ppc_register_settings() {
    register_setting('ppc_options_group', 'ppc_password_type');
    register_setting('ppc_options_group', 'ppc_fixed_password');
    register_setting('ppc_options_group', 'ppc_password_length');
    register_setting('ppc_options_group', 'ppc_countdown_time');
    register_setting('ppc_options_group', 'ppc_countdown_repeat');
}
add_action('admin_init', 'ppc_register_settings');

function ppc_settings_page() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'ppc_protected_content';
    
    if (isset($_POST['ppc_add_content'])) {
        $type = sanitize_text_field($_POST['ppc_content_type']);
        $summary = sanitize_text_field($_POST['ppc_summary']);
        $content = sanitize_textarea_field($_POST['ppc_content']);
        $password_type = get_option('ppc_password_type', 'fixed');
        $password = ($password_type === 'random') ? ppc_generate_random_password(get_option('ppc_password_length', 8)) : get_option('ppc_fixed_password');
        $countdown_time = intval($_POST['ppc_countdown_time']);
        $countdown_repeat = intval($_POST['ppc_countdown_repeat']);
        
        $wpdb->insert(
            $table_name,
            array(
                'type' => $type,
                'summary' => $summary,
                'content' => $content,
                'password' => $password,
                'countdown_time' => $countdown_time,
                'countdown_repeat' => $countdown_repeat,
                'created_at' => current_time('mysql')
            )
        );
    }

    if (isset($_POST['ppc_delete_content'])) {
        $id = intval($_POST['ppc_content_id']);
        $wpdb->delete($table_name, array('id' => $id));
    }

    $protected_contents = $wpdb->get_results("SELECT * FROM $table_name");
    ?>
    <div class="wrap">
        <h1>Protect Password Content Settings</h1>
        <form method="post" action="options.php">
            <?php
            settings_fields('ppc_options_group');
            do_settings_sections('ppc_options_group');
            ?>
            <table class="form-table">
                <tr valign="top">
                    <th scope="row">Password Type</th>
                    <td>
                        <select name="ppc_password_type">
                            <option value="fixed" <?php selected(get_option('ppc_password_type'), 'fixed'); ?>>Fixed</option>
                            <option value="random" <?php selected(get_option('ppc_password_type'), 'random'); ?>>Random</option>
                        </select>
                    </td>
                </tr>
                <tr valign="top" id="fixed_password_row" style="<?php if (get_option('ppc_password_type', 'fixed') === 'random') echo 'display:none;'; ?>">
                    <th scope="row">Fixed Password</th>
                    <td><input type="password" name="ppc_fixed_password" value="<?php echo esc_attr(get_option('ppc_fixed_password')); ?>" /></td>
                </tr>
                <tr valign="top" id="random_password_row" style="<?php if (get_option('ppc_password_type', 'fixed') === 'fixed') echo 'display:none;'; ?>">
                    <th scope="row">Random Password Length</th>
                    <td><input type="number" name="ppc_password_length" value="<?php echo esc_attr(get_option('ppc_password_length', 8)); ?>" /></td>
                </tr>
                <tr valign="top">
                    <th scope="row">Countdown Time (seconds)</th>
                    <td><input type="number" name="ppc_countdown_time" value="<?php echo esc_attr(get_option('ppc_countdown_time', 60)); ?>" /></td>
                </tr>
                <tr valign="top">
                    <th scope="row">Repeat Countdown</th>
                    <td>
                        <select name="ppc_countdown_repeat">
                            <option value="1" <?php selected(get_option('ppc_countdown_repeat'), '1'); ?>>One time</option>
                            <option value="2" <?php selected(get_option('ppc_countdown_repeat'), '2'); ?>>Two times</option>
                        </select>
                    </td>
                </tr>
            </table>
            <?php submit_button(); ?>
        </form>
        
        <!-- Form thêm nội dung bảo vệ -->
        <h2>Add Protected Content or Download Link</h2>
        <form method="post">
            <table class="form-table">
                <tr valign="top">
                    <th scope="row">Content Type</th>
                    <td>
                        <input type="radio" name="ppc_content_type" value="content" checked> Content
                        <input type="radio" name="ppc_content_type" value="download"> Download Link
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row">Summary</th>
                    <td><input type="text" name="ppc_summary" value="" /></td>
                </tr>
                <tr valign="top">
                    <th scope="row" id="content_label">Protected Content</th>
                    <td><textarea name="ppc_content" rows="5" cols="50"></textarea></td>
                </tr>
                <tr valign="top">
                    <td colspan="2">
                        <input type="hidden" name="ppc_countdown_time" value="<?php echo esc_attr(get_option('ppc_countdown_time', 60)); ?>" />
                        <input type="hidden" name="ppc_countdown_repeat" value="<?php echo esc_attr(get_option('ppc_countdown_repeat', 1)); ?>" />
                        <input type="submit" name="ppc_add_content" value="Add Content" class="button button-primary" />
                    </td>
                </tr>
            </table>
        </form>

        <!-- Danh sách nội dung bảo vệ -->
        <h2>Protected Contents and Links</h2>
        <table class="wp-list-table widefat fixed striped table-view-list">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Type</th>
                    <th>Summary</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($protected_contents as $content): ?>
                <tr>
                    <td><?php echo $content->id; ?></td>
                    <td><?php echo $content->type; ?></td>
                    <td><?php echo $content->summary; ?></td>
                    <td>
                        <form method="post" style="display:inline-block;">
                            <input type="hidden" name="ppc_content_id" value="<?php echo $content->id; ?>" />
                            <input type="submit" name="ppc_delete_content" value="Delete" class="button button-secondary" />
                        </form>
                        <code>[ppc_protect id="<?php echo $content->id; ?>"]</code>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <script type="text/javascript">
            document.addEventListener('DOMContentLoaded', function() {
                var passwordType = document.querySelector('select[name="ppc_password_type"]');
                var fixedPasswordRow = document.getElementById('fixed_password_row');
                var randomPasswordRow = document.getElementById('random_password_row');
                var contentTypeRadios = document.querySelectorAll('input[name="ppc_content_type"]');
                var contentLabel = document.getElementById('content_label');

                passwordType.addEventListener('change', function() {
                    if (this.value === 'fixed') {
                        fixedPasswordRow.style.display = '';
                        randomPasswordRow.style.display = 'none';
                    } else {
                        fixedPasswordRow.style.display = 'none';
                        randomPasswordRow.style.display = '';
                    }
                });

                contentTypeRadios.forEach(function(radio) {
                    radio.addEventListener('change', function() {
                        contentLabel.textContent = this.value === 'content' ? 'Protected Content' : 'Download Link';
                    });
                });
            });
        </script>
    </div>
    <?php
}

function ppc_add_admin_menu() {
    add_options_page('PPC Settings', 'Content Protection', 'manage_options', 'ppc-settings', 'ppc_settings_page');
}
add_action('admin_menu', 'ppc_add_admin_menu');
?>