<p>
    <b><?= $language->note ?>
        : </b><i><?= $language->condition_order_category_note ?></i>
</p>
<div class="full-width">
    <table>
        <thead>
        <tr>
            <th style="width: 20%"><?= $language->condition_must ?></th>
            <th><?= $language->condition_item_categories ?></th>
            <th style="width: 5%"><?= $language->action ?></th>
        </tr>
        </thead>
        <tbody class="condition-order-category-clone-container include-multi-select">
        <?php
        $i = 1;
        if (isset($values['row']) && !empty($values['row'])) {
            foreach ($values['row'] as $key => $value) {
                if (is_int($key)) {
                    $default_operator = isset($value['operator']) ? $value['operator'] : 'must_in';
                    $default_value = isset($value['value']) ? $value['value'] : array();
                    ?>
                    <tr class="condition-order-category-clone-row-<?= $i ?>">
                        <td>
                            <?= $form->dropdown('conditions[order_categories][row][' . $i . '][operator]', $in_list_condition_arr, $default_operator, array('style' => ' width: 30%;')) ?>
                        </td>
                        <td>
                            <?= $form->multiselect('conditions[order_categories][row][' . $i . '][value][]', $category_list, $default_value, array('class' => 'has-multi-select', 'data-selecttype' => 'normal')) ?>
                        </td>
                        <td>
                            <?= $form->button('', $language->remove, array('class' => 'remove-clone-btn warning', 'data-action' => 'remove', 'data-remove' => 'condition-order-category-clone-row-' . $i, 'data-row' => $i)); ?>
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
            <td colspan="4" class="align-right">
                <?= $form->button('', $language->add, array('class' => 'info clone-content-btn', 'data-clonefrom' => 'condition-order-category-cloneable-container', 'data-cloneto' => 'condition-order-category-clone-container', 'data-action' => 'add', 'data-nextrow' => $i++)); ?>
            </td>
        </tr>
        </tfoot>
    </table>
    <table class="hidden">
        <tbody class="condition-order-category-cloneable-container">
        <tr class="condition-order-category-clone-row-{i}">
            <td>
                <?= $form->dropdown('conditions[order_categories][row][{i}][operator]', $in_list_condition_arr, '') ?>
            </td>
            <td>
                <?= $form->multiselect('conditions[order_categories][row][{i}][value][]', $category_list, array(), array('class' => 'has-multi-select', 'data-selecttype' => 'normal')) ?>
            </td>
            <td>
                <?= $form->button('', $language->remove, array('class' => 'remove-clone-btn warning', 'data-action' => 'remove', 'data-remove' => 'condition-order-category-clone-row-{i}', 'data-row' => '{i}')); ?>
            </td>
        </tr>
        </tbody>
    </table>
</div>