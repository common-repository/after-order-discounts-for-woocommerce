<main class="waod">
    <article>
        <?= $form->open('', 'save_the_rule', array('data-path' => admin_url('admin-ajax.php'), 'id' => 'waod-rule')); ?>
        <div class="fix-top-right">
            <h1 class="header-breadcrumbs">
                <span>
                    <a href="<?= admin_url('admin.php?page=' . AODFW_PLUGIN_SLUG) ?>"><?= $language->plugin_name ?></a>
                </span>
                <span><?= (isset($_REQUEST['task']) && $_REQUEST['task'] == "edit") ? $language->edit_rule : $language->create_rule ?></span>
            </h1>
            <span class="header-right-panel">
            <?= $form->submit('save_rule', $language->save, array('class' => 'button')) ?>
            <a class="button secondary"
               href="<?= admin_url('admin.php?page=' . AODFW_PLUGIN_SLUG); ?>"><?= $language->close ?></a>
            </span>
        </div>
        <section>
            <div class="columns twelve ">
                <ul class="tabs">
                    <li class="active" id="general-tab-btn"><span data-tab="general"><?= $language->general ?></span>
                    </li>
                    <li id="conditions-tab-btn"><span data-tab="conditions"><?= $language->condition ?></span></li>
                    <li id="discounts-tab-btn"><span data-tab="discounts"><?= $language->discount ?></span></li>
                </ul>
            </div>
        </section>
        <p></p>
        <section class="card">
            <div class="columns twelve">
                <div class="tab-container" style="display: block" id="tab-general">
                    <div class="card-section">
                        <?= $form->hidden('ID', (isset($values['ID'])) ? $values['ID'] : 0) ?>
                        <?= $form->hidden('priority', (isset($values['priority'])) ? $values['priority'] : 0) ?>
                        <?= $form->hidden('class', $rule_class) ?>
                        <section>
                            <div class="columns six">
                                <div class="row">
                                    <?= $form->label($language->condition_name_label, 'rule-name') ?>
                                    <?= $form->input('title', (isset($values['title'])) ? $values['title'] : '', array('id' => 'rule-name', 'placeholder' => $language->condition_name_placeholder)) ?>
                                    <i><b><?= $language->example ?>
                                            : </b> <?= $language->condition_name_description ?>
                                    </i>
                                </div>
                                <div class="row">
                                    <?= $form->label($language->valid_from_label, 'rule-valid-from') ?>
                                    <?= $form->input('start_from', (isset($values['start_from'])) ? $values['start_from'] : '', array('id' => 'rule-valid-from', 'placeholder' => $language->valid_from_placeholder, 'data-datepicker' => 'init', 'autocomplete' => 'off')) ?>
                                    <i><b><?= $language->note ?> : </b> <?= $language->valid_from_description ?></i>
                                </div>
                                <div class="row">
                                    <?= $form->label($language->valid_to_label, 'rule-valid-to') ?>
                                    <?= $form->input('end_on', (isset($values['end_on'])) ? $values['end_on'] : '', array('id' => 'rule-valid-to', 'placeholder' => $language->valid_to_placeholder, 'data-datepicker' => 'init', 'autocomplete' => 'off')) ?>
                                    <i><b><?= $language->note ?> : </b> <?= $language->valid_to_description ?></i>
                                </div>
                                <div class="row">
                                    <?= $form->label($language->rule_is_active) ?>
                                    <?php
                                    $is_active = isset($values['status']) ? $values['status'] : 1;
                                    ?>
                                    <?= $form->radio('status', '1', ($is_active == 1) ? true : false) ?><?= $language->yes ?>
                                    &nbsp;&nbsp;
                                    <?= $form->radio('status', '0', ($is_active == 0) ? true : false) ?><?= $language->no ?>
                                    <br><i><b><?= $language->note ?> : </b> <?= $language->rule_status_description ?>
                                    </i>
                                </div>
                            </div>
                            <div class="columns six">
                                <div class="row">
                                    <?= $form->label($language->condition_description_label, 'rule-description') ?>
                                    <?= $form->textarea('description', (isset($values['description'])) ? $values['description'] : '', array('id' => 'rule-description', 'placeholder' => $language->condition_description_placeholder)) ?>
                                    <i><b><?= $language->example ?>
                                            : </b> <?= $language->condition_description_description ?></i>
                                </div>
                            </div>
                        </section>
                        <h6><?= $language->choose_rule_type ?></h6>
                        <section>
                            <div class="column twelve">
                                <div class="row radio-inline">
                                    <?php
                                    foreach ($rule_types as $rule_name => $rule_type) {
                                        ?>
                                        <label>
                                            <?= $form->radio('rule_type', $rule_name, ($rule_name == $choosed_rule_type), array('class' => 'rule-type', 'data-class' => $rule_type['class'])) ?>
                                            <?= $rule_type['label'] ?>
                                        </label>
                                        <?php
                                    }
                                    ?>
                                    <div class="margin-bottom-10"><i><b><?= $language->note ?>
                                                : </b><?= $language->rule_type_description ?></i></div>
                                </div>
                            </div>
                        </section>
                        <div class="align-right">
                            <button type="button" class="navigate-btn"
                                    data-show="conditions"><?= $language->next ?></button>
                        </div>
                    </div>
                </div>
                <div class="tab-container" id="tab-conditions">
                    <div class="card-section">
                        <section>
                            <div class="columns three condition-checkboxes-parent">
                                <div id="stick-here"></div>
                                <div class="condition-checkboxes">
                                    <?= $condition_btn; ?>
                                    <p class="red align-center mt-20 hidden no-more-conditions"><?= $language->no_more_conditions_found ?></p>
                                </div>
                            </div>
                            <div class="columns nine">
                                <div class="conditions-relationship">
                                    <div class="row side-elements">
                                        <?php
                                        $default_relation = isset($conditions['relation']) ? $conditions['relation'] : 'and';
                                        ?>
                                        <?= $form->label($language->condition_relationship) ?>
                                        <?= $form->radio('conditions[relation]', 'and', ($default_relation == 'and') ? true : false) ?><?= $language->and ?>
                                        &nbsp;&nbsp;
                                        <?= $form->radio('conditions[relation]', 'or', ($default_relation == 'or') ? true : false) ?><?= $language->or ?>
                                    </div>
                                    <p><b><?= $language->note; ?>:</b>
                                        <i><?= $language->condition_relation_description ?></i>
                                    </p>
                                </div>
                                <p class="align-center no-condition-message red hidden-field <?= empty($used_conditions) ? 'show' : ''; ?>"><?= $language->no_condition_used ?></p>
                                <div class="condition">
                                    <?= $condition_fields; ?>
                                </div>
                                <div class="wcd-preloader align-center " style="display: none;">
                                    <img height="50px" src="<?php echo AODFW_URL; ?>Assets/Admin/Js/preloader.gif">
                                </div>
                            </div>
                        </section>
                        <div class="align-right">
                            <button type="button" class="secondary navigate-btn"
                                    data-show="general"><?= $language->previous ?></button>
                            <button type="button" class=" navigate-btn"
                                    data-show="discounts"><?= $language->next ?></button>
                        </div>
                    </div>
                </div>
                <div class="tab-container" id="tab-discounts">
                    <div class="discount-container">
                        <?= $discount_fields ?>
                    </div>
                    <div class="align-right">
                        <button type="button" class="secondary navigate-btn"
                                data-show="conditions"><?= $language->previous ?></button>
                    </div>
                </div>
            </div>
        </section>
        <?php $form->close(); ?>
    </article>
</main>