<?php
$order_value = isset($value["value"]) ? $value["value"] : '';
$order_min_value = isset($value["min_value"]) ? $value["min_value"] : '';
$order_max_value = isset($value["max_value"]) ? $value["max_value"] : '';
$order_value_operator = isset($value["operator"]) ? $value["operator"] : 'greater_then';
?>
<p><b><?= $language->note ?>
        : </b><i><?= $language->condition_order_total_note ?></i>
</p>
<p><b><?= $language->example ?>
        : </b><i><?= $language->condition_order_total_note_example ?></i>
</p>
<div class="row side-elements">
    <?= $form->label($language->condition_order_total) ?>
    <?= $form->dropdown('conditions[order_total][operator]', array(
        'greater_than' => $language->greater_than,
        'greater_than_or_equal' => $language->greater_than_or_equal,
        'lesser_than' => $language->lesser_than,
        'lesser_than_or_equal' => $language->lesser_than_or_equal,
        'equal_to' => $language->equal_to,
        'between' => $language->between,
    ), $order_value_operator, array('class' => 'order-total-operator')) ?>
    <?php
    $hide_field = ($order_value_operator == "between") ? "hidden-field" : "";
    $show_field = ($order_value_operator == "between") ? "show" : "";
    ?>
    <?= $form->input('conditions[order_total][value]', $order_value, array('class' => 'value-field order-total-value float-only-field ' . $hide_field, 'placeholder' => 1000)) ?>
    <?= $form->input('conditions[order_total][min_value]', $order_min_value, array('class' => 'hidden-field order-min-max-val ' . $show_field, 'placeholder' => 1000)) ?>
    <span class="hidden-field order-min-max-val <?= ($order_value_operator == "between") ? "show" : "" ?>">&nbsp;<?= $language->and; ?>&nbsp;</span>
    <?= $form->input('conditions[order_total][max_value]', $order_max_value, array('class' => 'hidden-field order-min-max-val ' . $show_field, 'placeholder' => 2000)) ?>
</div>