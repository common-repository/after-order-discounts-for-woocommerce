<main class="waod">
    <article>
        <?= $form->open('', 'save_settings', array('data-path' => admin_url('admin-ajax.php'), 'id' => 'waod-settings')); ?>
        <div class="fix-top-right">
            <h1 class="header-breadcrumbs">
                <span>
                    <a href="<?= admin_url('admin.php?page=' . AODFW_PLUGIN_SLUG) ?>"><?= $language->plugin_name ?></a>
                </span>
                <span><?= $language->settings ?></span>
            </h1>
            <span class="header-right-panel">
            <?= $form->submit('save_settings', $language->save, array('class' => 'button')) ?>
            <a class="button secondary"
               href="<?= admin_url('admin.php?page=' . AODFW_PLUGIN_SLUG); ?>"><?= $language->close ?></a>
            </span>
        </div>

        <?php
        $tab_btn = $tab_container = '';
        if (!empty($rule_fields)) {
            $i = 1;
            foreach ($rule_fields as $key => $fields) {
                if (array_key_exists($key, $rules)) {
                    $class = ($i == 1) ? 'active' : '';
                    $display = ($i == 1) ? 'block' : 'none';
                    $tab_btn .= '<li class="' . $class . '" id="' . $key . '-tab-btn"><span data-tab="' . $key . '">' . $rules[$key]['label'] . '</span></li>';
                    $tab_container .= '<div style="display:' . $display . ';" class="tab-container" id="tab-' . $key . '"><div class="card-section">';
                    if (!empty($fields)) {
                        foreach ($fields as $field) {
                            if (is_array($field)) {
                                $desc = isset($field['description']) ? $field['description'] : '';
                                $label = isset($field['label']) ? $field['label'] : '';
                                $field = isset($field['field']) ? $field['field'] : '';
                                $tab_container .= '<div class="row">' . $label . $field . $desc . '</div>';
                            } else {
                                $tab_container .= $field;
                            }
                        }
                    }
                    $tab_container .= '</div></div>';
                    $i++;
                }
            }
            ?>
            <?php
        }
        ?>
        <section>
            <div class="columns twelve ">
                <ul class="tabs">
                    <?= $tab_btn ?>
                </ul>
            </div>
        </section>
        <p></p>
        <section class="card">
            <div class="columns twelve">
                <?= $tab_container ?>
            </div>
        </section>
        <?= $form->close() ?>
    </article>
</main>