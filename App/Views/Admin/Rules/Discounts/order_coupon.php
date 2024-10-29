<div class="columns twelve">
    <div class="row">
        <?= $form->label($language->discount_coupon_type, 'coupon-type') ?>
        <?=
        $form->dropdown('discounts[order_coupon][type]', array(
            'flat' => __('Flat', AODFW_TEXT_DOMAIN),
            'percentage' => __('Percentage', AODFW_TEXT_DOMAIN)
        ), $coupon_type, array('id' => 'coupon-type'))
        ?>
        <p><b><?= $language->note ?> : </b>
            <i><?= $language->discount_coupon_type_description ?></i></p>
    </div>
    <div class="row">
        <?= $form->label($language->discount_coupon_value, 'coupon-value') ?>
        <?= $form->input('discounts[order_coupon][value]', $coupon_value, array('id' => 'coupon-value', 'placeholder' => '10', 'class' => 'float-only-field')) ?>
        <p><b><?= $language->note ?> : </b>
            <i><?= $language->discount_coupon_value_description ?></i></p>
    </div>
</div>