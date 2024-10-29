<div class="full-width">
    <div class="row side-elements">
        <label>
            <?php
            echo $language->dynamically_add_points;
            ?>
        </label>
        <?php
        echo $form->radio('discounts[' . $name . '][need_dynamic_point]', 1, !empty($need_dynamic_point), array('class' => 'need-dynamic-spin-points'));
        echo $language->yes;
        ?>
        &nbsp;&nbsp;
        <?php
        echo $form->radio('discounts[' . $name . '][need_dynamic_point]', 0, empty($need_dynamic_point), array('class' => 'need-dynamic-spin-points'));
        echo $language->no;
        ?>
    </div>
    <div class="row side-elements dynamic-spin-points"
         style=" <?= !empty($need_dynamic_point) ? '' : 'display:none' ?>;">
        <?php
        echo $form->label($language->every_bug_spent);
        echo $form->input('discounts[' . $name . '][amount_spent]', $amount_spent, array('placeholder' => 100, 'class' => 'float-only-field'));
        echo $form->label($language->every_bug_spent_amount_give_additional);
        echo $form->input('discounts[' . $name . '][dynamic_spin_points]', $dynamic_spin_points, array('placeholder' => 1, 'class' => 'int-only-field'));
        echo $form->label($language->give_points);
        ?>
    </div>
    <div class="row static-spin-points" style="display: <?= empty($need_dynamic_point) ? 'block' : 'none' ?>;">
        <?php
        echo $language->discount_spin_wheel_roulette_total_spins;
        echo $form->input('discounts[' . $name . '][count]', $spin_value, array('placeholder' => 3, 'class' => 'int-only-field'));
        ?>
    </div>
</div>
<div class="full-width">
    <h6 class="align-center"><?= $language->discount_spin_wheel_roulette_prizes; ?></h6>
    <table>
        <thead>
        <tr>
            <th><?= $language->discount_spin_wheel_bg_color ?></th>
            <th><?= $language->discount_spin_wheel_label ?></th>
            <th><?= $language->discount_spin_wheel_value ?></th>
            <th><?= $language->discount_spin_wheel_message ?></th>
            <th><?= $language->action ?></th>
        </tr>
        </thead>
        <tbody class="spin-discounts-clone-container">
        <?php
        $i = 1;
        if (!empty($rows)) {
            foreach ($rows as $key => $row) {
                if (is_int($key)) {
                    $label = isset($row['label']) ? $row['label'] : '';
                    $color = isset($row['color']) ? $row['color'] : '';
                    $value = isset($row['value']) ? $row['value'] : '';
                    $message = isset($row['message']) ? $row['message'] : '';
                    $type = isset($row['type']) ? $row['type'] : '';
                    ?>
                    <tr class="spin-discounts-clone-row-<?= $i ?>">
                        <td>
                            <?= $form->dropdown('discounts[' . $name . '][row][' . $i . '][color]', $colors_list, $color); ?>
                        </td>
                        <td>
                            <?= $form->input('discounts[' . $name . '][row][' . $i . '][label]', $label); ?>
                        </td>
                        <td>
                            <div class="row side-elements">
                                <?php
                                echo $form->input('discounts[' . $name . '][row][' . $i . '][value]', $value, array('placeholder' => $language->discount_spin_wheel_placeholder_value, 'class' => 'float-only-field'));
                                echo $form->dropdown('discounts[' . $name . '][row][' . $i . '][type]', array(
                                    'flat' => $language->flat,
                                    'percentage' => $language->percentage
                                ), $type);
                                ?>
                            </div>
                            <p>
                                <b><?php echo $language->note; ?>: </b>
                                <i><?php echo $language->zero_for_no_prize; ?></i>
                            </p>
                        </td>
                        <td>
                            <?php
                            echo $form->input('discounts[' . $name . '][row][' . $i . '][message]', $message);
                            ?>
                            <p>
                                <b><?php echo $language->note; ?>: </b>
                                <i><?php echo $language->message_after_success_spin; ?></i>
                            </p>
                        </td>
                        <td>
                            <?= $form->button('', $language->remove, array('class' => 'remove-clone-btn warning', 'data-action' => 'remove', 'data-remove' => 'spin-discounts-clone-row-' . $i, 'data-row' => $i)); ?>
                        </td>
                    </tr>
                    <?php
                    $i++;
                }
            }
        }
        ?>
        </tbody>
        <tfoot>
        <tr>
            <td colspan="5" class="align-right">
                <?= $form->button('', $language->add, array('class' => 'info clone-content-btn', 'data-clonefrom' => 'spin-discounts-cloneable-container', 'data-cloneto' => 'spin-discounts-clone-container', 'data-action' => 'add', 'data-nextrow' => $i++)); ?>
            </td>
        </tr>
        </tfoot>
    </table>
    <table class="hidden">
        <tbody class="spin-discounts-cloneable-container">
        <tr class="spin-discounts-clone-row-{i}">
            <td>
                <?= $form->dropdown('discounts[' . $name . '][row][{i}][color]', $colors_list, ''); ?>
            </td>
            <td>
                <?= $form->input('discounts[' . $name . '][row][{i}][label]'); ?>
            </td>
            <td>
                <div class="row side-elements">
                    <?php
                    echo $form->input('discounts[' . $name . '][row][{i}][value]', '', array('placeholder' => $language->discount_spin_wheel_placeholder_value, 'class' => 'float-only-field'));
                    echo $form->dropdown('discounts[' . $name . '][row][{i}][type]', array(
                        'flat' => $language->flat,
                        'percentage' => $language->percentage
                    ), '');
                    ?>
                </div>
                <p>
                    <b><?php echo $language->note; ?>: </b>
                    <i><?php echo $language->zero_for_no_prize; ?></i>
                </p>
            </td>
            <td>
                <?php
                echo $form->input('discounts[' . $name . '][row][{i}][message]');
                ?>
                <p>
                    <b><?php echo $language->note; ?>: </b>
                    <i><?php echo $language->message_after_success_spin; ?></i>
                </p>
            </td>
            <td>
                <?= $form->button('', $language->remove, array('class' => 'remove-clone-btn warning', 'data-action' => 'remove', 'data-remove' => 'spin-discounts-clone-row-{i}', 'data-row' => '{i}')); ?>
            </td>
        </tr>
        </tbody>
    </table>
</div>