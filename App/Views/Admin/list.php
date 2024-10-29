<main class="waod">
    <article>
        <div class="fix-top-right">
            <h1 class="header-breadcrumbs">
                <span>
                    <a href="<?= admin_url('admin.php?page=' . AODFW_PLUGIN_SLUG) ?>"><?= $language->plugin_name ?></a>
                </span>
                <span><?= $language->manage_rules ?></span>
            </h1>
        </div>
        <section>
            <a class="button secondary" href="<?= $create_new_link ?>"><?= $language->create_rule ?></a>&nbsp;
            <a class="button" href="<?= $settings_link ?>"><?= $language->settings ?></a>
        </section>
        <section class="card">
            <table>
                <thead>
                <tr>
                    <th><?= $language->rule_title ?></th>
                    <th><?= $language->rule_start_on ?></th>
                    <th><?= $language->rule_end_on ?></th>
                    <th><?= $language->rule_type ?></th>
                    <th><?= $language->rule_is_active ?></th>
                    <th><?= $language->action ?></th>
                </tr>
                </thead>
                <tbody id="sortable">
                <?php
                if (isset($rules)) {
                    if (!empty($rules)) {
                        $s_no = 1;
                        foreach ($rules as $rule) {
                            $type = $rule->type();
                            ?>
                            <tr id="rule-no-<?= $rule->id() ?>">
                                <td>
                                    <span class="dashicons dashicons-move rules-re-order-btn"></span><a
                                            href="<?= admin_url('admin.php?page=' . AODFW_PLUGIN_SLUG . '&' . http_build_query(array('task' => 'edit', 'id' => $rule->id()))); ?>"><?= $rule->title() ?></a>
                                </td>
                                <td><?= !empty($rule->startsOn()) ? $rule->startsOn() : '-' ?></td>
                                <td><?= !empty($rule->endsOn()) ? $rule->endsOn() : '-' ?></td>
                                <td>
                                    <?php
                                    echo isset($available_rule_types[$type]) ? $available_rule_types[$type]['label'] : $type;
                                    ?>
                                </td>
                                <td>
                                    <label class="switch">
                                        <input <?= ($rule->isActive()) ? "checked" : ""; ?> type="checkbox" value="1"
                                                                                            class="status-btn"
                                                                                            data-rule="<?= $rule->id(); ?>">
                                        <span class="slider round"></span>
                                    </label>
                                </td>
                                <td>
                                    <a href="<?= admin_url('admin.php?page=' . AODFW_PLUGIN_SLUG . '&' . http_build_query(array('task' => 'edit', 'id' => $rule->id()))); ?>"
                                       class="button"><span class="dashicons dashicons-edit"></span></a>
                                    <button class="warning button delete-rule-btn"
                                            data-rule="<?= $rule->id() ?>"><span
                                                class="dashicons dashicons-trash"></span></button>
                                </td>
                            </tr>
                            <?php
                            $s_no++;
                        }
                    } else {
                        ?>
                        <tr>
                            <td colspan="6" class="align-center">
                                <p class="red"><?= $language->no_rules_found ?></p>
                                <a class="button" href="<?= $create_new_link ?>"><?= $language->create_new_rule ?></a>
                            </td>
                        </tr>
                        <?php
                    }
                }
                ?>
                </tbody>
            </table>
        </section>
    </article>
</main>