(function ($) {
    /**
     * drag and drop priority maker
     */
    $("#sortable").sortable({
        axis: 'y',
        stop: function (event, ui) {
            var data = $(this).sortable('serialize');
            console.log(data);
            $.ajax({
                data: data,
                type: 'POST',
                url: rule_discount.ajax + '?action=aodfw_order_the_rules'
            });
        }
    });
    /**
     * Conditionorder total
     */
    $(document).on('change', '.order-total-operator', function () {
        var value = $(this).val();
        if (value === "between") {
            $(".order-min-max-val").addClass('show');
            $(".order-total-value").addClass('hidden-field');
        } else {
            $(".order-min-max-val").removeClass('show');
            $(".order-total-value").removeClass('hidden-field');
        }
    });
    /**
     * tabs
     */
    $(document).on('click', '.tabs li span', function () {
        $(".tab-container").hide();
        var tab = $(this).data('tab');
        $("#tab-" + tab).show();
        var tabs = $(this).parents('.tabs').find('li');
        tabs.removeClass('active');
        $(this).parent('li').addClass('active');
    });
    $(document).on('click', '.navigate-btn', function () {
        $(".tab-container").hide();
        var tab = $(this).data('show');
        $("#tab-" + tab).show();
        var tabs = $('.tabs').find('li');
        tabs.removeClass('active');
        $('#' + tab + '-tab-btn').addClass('active');
    });
    /**
     * Changing the status of rule
     */
    $(document).on("change", ".status-btn", function () {
        var id = $(this).data("rule");
        var status = 0;
        if ($(this).is(':checked')) {
            status = 1;
        }
        $.ajax({
            url: rule_discount.ajax,
            type: 'post',
            data: {id: id, status: status, action: 'update_rule_status'},
            success: function (result) {
                if (result.error) {
                    notyAlert('error', result.message);
                } else {
                    notyAlert('success', result.message);
                }
            }
        })
    });
    /**
     * remove rule
     */
    $(document).on("click", ".delete-rule-btn", function () {
        var id = $(this).data("rule");
        $.ajax({
            url: rule_discount.ajax,
            type: 'post',
            data: {id: id, action: 'remove_rule'},
            success: function (result) {
                if (result.error) {
                    notyAlert('error', result.message);
                } else {
                    notyAlert('success', result.message);
                }
                $("#rule-no-" + id).remove();
            }
        })
    });
    /**
     * Notify the message
     */
    function notyAlert(type, message) {
        new Noty({
            text: message,
            theme: 'mint',
            type: type, layout: "bottomRight", timeout: 4000,
        }).show();
    }
    /**
     * Save Rule
     */
    $(document).on("submit", "#waod-rule", function (e) {
        e.preventDefault();
        var path = $(this).data("path");
        var button = $('input[name="save_rule"]');
        button.attr("disabled", true);
        button.text(rule_discount.saving);
        $.ajax({
            url: path,
            type: 'post',
            data: $(this).serialize(),
            success: function (result) {
                $("input[name='ID']").val(result.id);
                if (result.error) {
                    notyAlert('error', result.message);
                } else {
                    notyAlert('success', result.message);
                }
                button.text(rule_discount.save);
                button.attr("disabled", false);
            }
        })
    });
    /**
     * Save settings
     *
     */
    $(document).on("submit", "#waod-settings", function (e) {
        e.preventDefault();
        var path = $(this).data("path");
        var button = $('input[name="save_settings"]');
        button.attr("disabled", true);
        button.text(rule_discount.saving);
        $.ajax({
            url: path,
            type: 'post',
            data: $(this).serialize(),
            success: function (result) {
                if (result.error) {
                    notyAlert('error', result.message);
                } else {
                    notyAlert('success', result.message);
                }
                button.text(rule_discount.save);
                button.attr("disabled", false);
            }
        })
    });
    /**
     * Show date picker
     */
    $(document).ready(function () {
        let dateInput = $('input[data-datepicker="init"]');
        dateInput.daterangepicker({
            "autoUpdateInput": false,
            "timePicker": true,
            "singleDatePicker": true,
            "locale": {
                "format": "YYYY-MM-DD hh:mm:ss",
            }
        });
        dateInput.on('apply.daterangepicker', function (ev, picker) {
            $(this).val(picker.startDate.format('YYYY-MM-DD hh:mm:ss'));
        });
    });
    /**
     * get conditions
     */
    $(document).on('click', '.rule-type', function () {
        let rule_class = $(this).data('class');
        let path = rule_discount.ajax;
        let action = 'get_rule_conditions';
        let checkbox_container = $(".condition-checkboxes");
        let discount_container = $(".discount-container");
        checkbox_container.html('');
        discount_container.html('');
        $('input[name="class"]').val(rule_class);
        $('.condition').html('');
        showLoader();
        $.ajax({
            url: path,
            type: 'post',
            data: {
                "action": action,
                "class": rule_class
            },
            success: function (result) {
                if (!result.error) {
                    checkbox_container.html(result.message.conditions);
                    discount_container.html(result.message.discounts);
                }
                hideLoader();
            }
        });
    });
    /**
     * Show loader
     */
    function showLoader() {
        $(".wcd-preloader").show();
    }
    /**
     * Hide loader
     */
    function hideLoader() {
        $(".wcd-preloader").hide();
    }
    /**
     * Check any conditions choosed
     */
    function isAnyConditionsChoosed() {
        var anyBoxesChecked = false;
        $("input[type='checkbox'][name='used_conditions[]']").each(function () {
            if ($(this).is(":checked")) {
                anyBoxesChecked = true;
            }
        });
        if (anyBoxesChecked === false) {
            $(".no-condition-message").addClass('show');
        } else {
            $(".no-condition-message").removeClass('show');
        }
        if ($('.condition-checkboxes input[type="checkbox"]:checked').length === $('.condition-checkboxes input[type="checkbox"]').length) {
            $(".no-more-conditions").addClass('show');
        } else {
            $(".no-more-conditions").removeClass('show');
        }
    }
    isAnyConditionsChoosed();
    /**
     * render discounts and conditions based on rule type
     */
    $(document).on("change", ".available-conditions", function () {
        let condition = $(this).val();
        showLoader();
        isAnyConditionsChoosed();
        if ($(this).prop("checked") === true) {
            $("#label-" + condition).addClass('hidden');
            let condition_class = $(this).data('class');
            let path = rule_discount.ajax;
            let action = 'get_condition';
            let field_container = $(".condition");
            $.ajax({
                url: path,
                type: 'post',
                data: {
                    "action": action,
                    "class": condition_class
                },
                success: function (result) {
                    field_container.append(result.message);
                    hideLoader();
                }
            });
        } else {
            $("#label-" + condition).removeClass('hidden');
            $(".box-" + condition).remove();
            hideLoader();
        }
    });
    /**
     * Remove conditions
     */
    $(document).on('click', '.remove-condition', function () {
        let condition = $(this).data('remove');
        $(".box-" + condition).remove();
        $("#label-" + condition).removeClass('hidden');
        $("#input-" + condition).prop('checked', false);
        isAnyConditionsChoosed();
        hideLoader();
    });
    $(document).on('click', '.clone-btn', function () {
        var section = $(this).data('clone');
        var html_content = $(".cloneable-" + section);
        var html = html_content.html();
        var id = $(this).data('next');
        html = html.replace(/{i}/g, id);
        var content = '<div class="row side-elements" id="remove-' + section + '-' + id + '">' + html + '</div>';
        $("." + section + "-clone-container").append(content);
        $(this).data('next', parseInt(id) + 1);
        var select = html_content.data('select');
        initSelect(select, section);
    });
    function initSelect(method, section) {
        if (method === "ajax") {
            $("." + section + "-clone-container .select-2").select2({
                placeholder: rule_discount.select_values,
                minimumInputLength: 1,
                ajax: {
                    type: 'POST',
                    url: ajaxurl,
                    dataType: 'json',
                    data: function (details, page) {
                        return {
                            query: details.term,
                            method: $(this).data('method'),
                            action: 'select2_search'
                        };
                    },
                    processResults: function (response) {
                        return {results: response.data || []};
                    },
                    cache: true
                }
            });
        } else {
            $("." + section + "-clone-container .select-2").select2();
        }
    }
    $(document).on('click', '.remove-clone-btn', function () {
        var id = $(this).data('remove');
        var section = $(this).data('section');
        $("#remove-" + section + "-" + id).remove();
    });
    $(document).on('keypress', '.float-only-field', function (event) {
        if ((event.which !== 46 || $(this).val().indexOf('.') !== -1) && (event.which < 48 || event.which > 57) || (event.which === 46 && $(this).caret().start === 0)) {
            event.preventDefault();
        }
    });
    $(document).on('keypress', '.int-only-field', function (event) {
        var charCode = (event.which) ? event.which : event.keyCode;
        if (charCode > 31 && (charCode < 48 || charCode > 57)) {
            event.preventDefault();
        }
    });
    initSelect('simple', 'order-categories');
    initSelect('simple', 'payment-method');
    initSelect('ajax', 'order-items');
    initSelect('ajax', 'order-customers');
    /**
     * stick to top conditions holder
     */
    function stick_to_top() {
        if ($("#conditions-tab-btn").hasClass('active')) {
            var window_top = $(window).scrollTop();
            var offset_div = $('#stick-here');
            var top = offset_div.offset().top - 80;
            var check_box_container = $('.condition-checkboxes');
            var max_width = $(".condition-checkboxes-parent").width();
            if (window_top > top) {
                check_box_container.addClass('stick');
                offset_div.height(check_box_container.outerHeight());
                check_box_container.width(max_width);
            } else {
                check_box_container.removeClass('stick');
                offset_div.height(0);
                check_box_container.width('100%');
            }
        }
    }
    $(function () {
        $(window).scroll(stick_to_top);
        stick_to_top();
    });
    $(".select-2").select2();
    /**
     * clone management
     */
    $(document).on('click', '.clone-content-btn,.remove-clone-btn', function () {
        $(this).manageClone({})
    });
    $.fn.manageClone = function (options) {
        var $this_var = $(this);
        var action = $this_var.data('action');
        var settings = $.extend({
            clone_from: $this_var.data('clonefrom'),
            clone_to: $this_var.data('cloneto'),
            row_id: $this_var.data('row'),
            next_row_id: $this_var.data('nextrow'),
            remove_clone: $this_var.data('remove')
        }, options);
        if (action === 'add') {
            var html = $('.' + settings.clone_from).html();
            html = html.replace(/{i}/g, settings.next_row_id);
            $('.' + settings.clone_to).append(html);
            var next_row = parseInt(settings.next_row_id) + 1;
            $this_var.data('nextrow', next_row);
            initMultiSelect();
            return next_row;
        } else if (action === 'remove') {
            var remove_class = settings.remove_clone.replace(/{i}/g, settings.row_id);
            $('.' + remove_class).remove();
            return settings.row_id;
        } else {
            throw new Error('unable to handle action');
        }
    };
    initMultiSelect();
    function initMultiSelect() {
        $('.include-multi-select select').each(function () {
            var select_box = $(this);
            if (select_box.hasClass('has-multi-select')) {
                var method = select_box.data('selecttype');
                if (method === 'normal') {
                    select_box.select2();
                } else if (method === 'ajax') {
                    select_box.select2({
                        placeholder: rule_discount.select_values,
                        minimumInputLength: 1,
                        ajax: {
                            type: 'POST',
                            url: ajaxurl,
                            dataType: 'json',
                            data: function (details, page) {
                                return {
                                    query: details.term,
                                    method: $(this).data('selectmethod'),
                                    action: 'select2_search'
                                };
                            },
                            processResults: function (response) {
                                return {results: response.data || []};
                            },
                            cache: true
                        }
                    });
                } else {
                    throw new Error('sorry can not processed!');
                }
            }
        });
    }
    $(document).on('click', '.need-dynamic-spin-points', function () {
        var val = parseInt($(this).val());
        $('.static-spin-points').toggle();
        $('.dynamic-spin-points').toggle();
    })
})(jQuery);