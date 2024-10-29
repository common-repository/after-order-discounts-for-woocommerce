<?php
if (isset($spin_details->details) && !empty($spin_details->details)) {
    if ($spin_details->details != '[]' && $spin_details->details != '{}') {
        $spins = array();
        if (is_string($spin_details->details)) {
            $spins = json_decode($spin_details->details, true);
        } else {
            $spins = (array)$spin_details->details;
        }
        if (!empty($spins)) {
            ?>
            <div class="fortune-wheel"></div>
            <div class="spin-wheel-message">
                <h4><?= $language->you_have_unlocked_your_spin ?></h4>
                <ul>
                    <li><?= $language->you_have_unlocked_your_spin_msg1 ?></li>
                    <li><?= $language->you_have_unlocked_your_spin_msg2 ?></li>
                    <li><?= $language->you_have_unlocked_your_spin_msg3 ?></li>
                </ul>
            </div>
            <div class="text-center">
                <div class="spinner-message"
                     style="width: 400px; max-width: 100%; margin: 10px auto;padding: 20px;font-family:arial;font-weight: bold;">
                    &nbsp;
                </div>
            </div>
            <a href="#" class="button spin-to-win"><?= $language->spin_to_win_btn_text; ?></a>
            <div class="spacer"></div>
            <script>
                jQuery(document).ready(function ($) {
                    <?php
                    $detail = array();
                    foreach ($spins as $key => $row) {
                        if (is_int($key)) {
                            $label = isset($row['label']) ? $row['label'] : '';
                            $color = isset($row['color']) ? $row['color'] : '';
                            $message = isset($row['message']) ? $row['message'] : __('You have won', AODFW_TEXT_DOMAIN) . ' ' . $label;
                            $value = isset($row['value']) ? $row['value'] : 0;
                            $detail[] = array(
                                'id' => $key,
                                'name' => $label,
                                'message' => $message,
                                'color' => '#' . $color,
                                'win' => ($value > 0) ? true : false
                            );
                        }
                    }
                    ?>
                    $(document).on('click', '.spin-to-win', function () {
                        $(this).remove();
                    });
                    var message_container = $('.spinner-message');
                    $('.fortune-wheel').premiumWooWheel({
                        items: <?php echo json_encode($detail) ?>,
                        button: '.spin-to-win',
                        onStart: function (results, spinCount, now) {
                            message_container.css('color', '#90A4AE');
                            message_container.html('Spinning...');
                        },
                        ajax: {
                            url: '<?php echo admin_url("admin-ajax.php") ?>',
                            type: 'POST',
                            nonce: false,
                            data: {action: 'create_lucky_prizes', spin_id:<?php echo $spin_details->ID ?>}
                        },
                        onComplete: function (results, count, now) {
                            $('.spinner-message').show();
                            if (results.win) {
                                $(".prize-won").show();
                            }
                            message_container.css('color', results.color).html(results.message);
                            setTimeout(function () {
                                window.location.reload();
                            }, 5000);
                        }
                    });
                });
            </script>
            <div class="prize-won snowflakes hidden" aria-hidden="true">
                <div class="snowflake">
                    ❅
                </div>
                <div class="snowflake">
                    ❆
                </div>
                <div class="snowflake">
                    ❅
                </div>
                <div class="snowflake">
                    ❆
                </div>
                <div class="snowflake">
                    ❅
                </div>
                <div class="snowflake">
                    ❆
                </div>
                <div class="snowflake">
                    ❅
                </div>
                <div class="snowflake">
                    ❆
                </div>
                <div class="snowflake">
                    ❅
                </div>
                <div class="snowflake">
                    ❆
                </div>
                <div class="snowflake">
                    ❅
                </div>
                <div class="snowflake">
                    ❆
                </div>
            </div>
            <?php
        }
    }
}