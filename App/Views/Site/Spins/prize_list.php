<table class="shop_table shop_table_responsive">
    <thead>
    <tr>
        <th><?= $language->sno ?></th>
        <th><?= $language->coupon_details ?></th>
        <th><?= $language->coupon_code ?></th>
        <th><?= $language->is_used ?></th>
    </tr>
    </thead>
    <tbody>
    <?php
    if (!empty($gifts)) {
        $i = 1;
        foreach ($gifts as $gift) {
            $coupon_value = ($gift->coupon_type == "flat") ? wc_price($gift->coupon_value) : $gift->coupon_value . '%';
            ?>
            <tr>
                <td><?= $i ?></td>
                <td><?= $coupon_value ?> <?= $language->worth_code ?></td>
                <td><?= $gift->coupon ?></td>
                <td><?= ($gift->is_used == 1) ? $language->coupon_used : $language->coupon_not_used ?></td>
            </tr>
            <?php
            $i++;
        }
    } else {
        ?>
        <tr>
            <td colspan="4"><p style="color: red;text-align: center"><?= $language->no_prizes_found ?></p></td>
        </tr>
        <?php
    }
    ?>
    </tbody>
</table>