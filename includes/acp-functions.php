<?php
if (!defined('ABSPATH')) exit;

function ppc_generate_random_password($length = 8) {
    return substr(str_shuffle('abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'), 0, $length);
}

// Shortcode bảo vệ nội dung và link download
function ppc_protected_shortcode($atts) {
    ob_start();
    global $wpdb;

    if (!session_id()) session_start();

    $id = intval($atts['id']);
    $table_name = $wpdb->prefix . 'ppc_protected_content';
    $content = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", $id));

    if (!$content) {
        return 'Invalid ID';
    }

    $password_type = get_option('ppc_password_type', 'fixed');
    $password = '';

    if ($password_type === 'random') {
        if (!isset($_SESSION['ppc_random_password'])) {
            $_SESSION['ppc_random_password'] = ppc_generate_random_password(get_option('ppc_password_length', 8));
        }
        $password = $_SESSION['ppc_random_password'];
    } else {
        $password = get_option('ppc_fixed_password');
    }

    if (isset($_POST['ppc_password']) && $_POST['ppc_password'] === $password) {
        $_SESSION['ppc_authenticated'] = $id;
    }

    if (isset($_SESSION['ppc_authenticated']) && $_SESSION['ppc_authenticated'] === $id) {
        if ($content->type === 'content') {
            echo '<div class="ppc_full_content">' . $content->content . '</div>';
        } else {
            echo '<a href="' . esc_url($content->content) . '">Download</a>';
        }
    } else {
        echo '<div class="ppc_summary">' . $content->summary . '</div>';
        echo '<form method="post">';
        echo '<label for="ppc_password">Enter password to view full content or download:</label>';
        echo '<input type="password" name="ppc_password" id="ppc_password"/>';
        echo '<input type="submit" value="Submit"/>';
        echo '</form>';
    }

    return ob_get_clean();
}
add_shortcode('ppc_protect', 'ppc_protected_shortcode');

// Shortcode đếm ngược hiển thị mật khẩu và kiểm tra Google source
function ppc_countdown_shortcode() {
    ob_start();

    $password_type = get_option('ppc_password_type', 'fixed');
    if ($password_type) {
        if ($password_type === 'random') {
            if (!isset($_SESSION['ppc_random_password'])) {
                $_SESSION['ppc_random_password'] = ppc_generate_random_password(get_option('ppc_password_length', 8));
            }
            $password = $_SESSION['ppc_random_password'];
        } else {
            $password = get_option('ppc_fixed_password');
        }

        ?>
        <div class="countdown-wrapper" style="display:none;">
          <p>Countdown: <span id="countdown_timer"><?php echo get_option('ppc_countdown_time', 60); ?></span> seconds</p>
          <button id="countdown_button" style="display:none;">Get Password</button>
          <p id="random_password_display" style="display:none;">Password: <span id="password_value"></span></p>
        </div>
        <script type="text/javascript">
          document.addEventListener('DOMContentLoaded', function() {
              function checkGoogleSource() {
                  console.log("Checking source");
                  fetch('/wp-json/ppc/v1/check-source')
                      .then(response => response.json())
                      .then(data => {
                          console.log("Source:", data.source);
                          if (data.source === 'google') {
                              console.log("Source is google, showing countdown");
                              showCountdown();
                          } else {
                              console.log("Source is not google, countdown won't show");
                          }
                      })
                      .catch(error => {
                          console.error('Error checking source:', error);
                      });
              }

              function showCountdown() {
                  document.querySelector('.countdown-wrapper').style.display = 'block';
                  startCountdown();
              }

              function startCountdown() {
                  var countdownTime = <?php echo get_option('ppc_countdown_time', 60); ?>;
                  var countdown = countdownTime;
                  var repeat = <?php echo get_option('ppc_countdown_repeat', 1); ?>;
                  var interval;
                  var counted = 0;
                  var timer = document.getElementById('countdown_timer');
                  var password = "<?php echo esc_js($password); ?>";
                  var passwordValue = document.getElementById('password_value');
                  var countdownButton = document.getElementById('countdown_button');
                  var randomPasswordDisplay = document.getElementById('random_password_display');

                  function countdownFunction() {
                      interval = setInterval(function() {
                          countdown--;
                          timer.innerText = countdown;
                          if (countdown <= 0) {
                              counted++;
                              clearInterval(interval);
                              if (counted < repeat) {
                                  countdown = countdownTime;
                                  countdownFunction();
                              } else {
                                  passwordValue.innerText = password;
                                  countdownButton.style.display = 'block';
                                  randomPasswordDisplay.style.display = 'block';
                              }
                          }
                      }, 1000);
                  }

                  countdownFunction();
              }

              document.getElementById('countdown_button').addEventListener('click', function() {
                  var passwordDisplay = document.getElementById('random_password_display');
                  passwordDisplay.style.display = 'block';

                  // Sao chép mật khẩu vào clipboard để người dùng dễ dàng nhập
                  var password = document.getElementById('password_value').innerText;
                  navigator.clipboard.writeText(password).then(function() {
                      alert('Password copied to clipboard! Please paste it into the password field for download or content access.');
                  },
                  function(err) {
                      console.error('Could not copy password: ', err);
                  });
              });

              checkGoogleSource();
          });
        </script>
        <?php
    }
    return ob_get_clean();
}
add_shortcode('ppc_countdown', 'ppc_countdown_shortcode');
?>