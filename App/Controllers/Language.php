<?php

namespace Waod\App\Controllers;

use stdClass;

class Language
{
    static function jsStrings()
    {
        $language_strings = array(
            'ajax' => admin_url('admin-ajax.php'),
            'save' => __('Save', AODFW_TEXT_DOMAIN),
            'saving' => __('Saving...', AODFW_TEXT_DOMAIN),
            'select_values' => __('Choose values...', AODFW_TEXT_DOMAIN),
        );
        return $language_strings;
    }

    static function siteStrings()
    {
        $language_strings = new stdClass();
        $language_strings->login_to_spin = __('Login to spin the wheel', AODFW_TEXT_DOMAIN);
        $language_strings->login_to_view = __('Click here', AODFW_TEXT_DOMAIN);
        $language_strings->spin_to_win_btn_text = __('Spin to win', AODFW_TEXT_DOMAIN);
        $language_strings->you_have_unlocked_your_spin = __('You have a chance to win a nice big fat discount. Are you feeling lucky? Give it a spin', AODFW_TEXT_DOMAIN);
        $language_strings->you_have_unlocked_your_spin_login = __('You have a chance to win a nice big fat discount. Are you feeling lucky? Login to give a spin', AODFW_TEXT_DOMAIN);
        $language_strings->you_have_unlocked_your_spin_msg1 = __('If you win, you can claim your coupon anytime', AODFW_TEXT_DOMAIN);
        $language_strings->you_have_unlocked_your_spin_msg2 = __('You can view your prizes under your account', AODFW_TEXT_DOMAIN);
        $language_strings->you_have_unlocked_your_spin_msg3 = __('Spam strictly not allowed', AODFW_TEXT_DOMAIN);
        $language_strings->sno = __('S.No', AODFW_TEXT_DOMAIN);
        $language_strings->coupon_code = __('Coupon', AODFW_TEXT_DOMAIN);
        $language_strings->coupon_details = __('Value', AODFW_TEXT_DOMAIN);
        $language_strings->is_used = __('Is used?', AODFW_TEXT_DOMAIN);
        $language_strings->no_prizes_found = __('Sorry, you had no prizes', AODFW_TEXT_DOMAIN);
        $language_strings->worth_code = __(' discount on cart subtotal', AODFW_TEXT_DOMAIN);
        $language_strings->coupon_used = __(' Used', AODFW_TEXT_DOMAIN);
        $language_strings->coupon_not_used = __(' Not used', AODFW_TEXT_DOMAIN);
        $language_strings->spin_to_win = __('Spin', AODFW_TEXT_DOMAIN);
        //$language_strings->coupon_code = __('Coupon', AODFW_TEXT_DOMAIN);
        return $language_strings;
    }

    /**
     * Contains all the required language translation need for admin pages
     * @return stdClass
     */
    static function adminStrings()
    {
        $language_strings = new stdClass();
        $language_strings->example = __('Example', AODFW_TEXT_DOMAIN);
        $language_strings->note = __('Note', AODFW_TEXT_DOMAIN);
        $language_strings->count_down_timer = __('Count-down timer', AODFW_TEXT_DOMAIN);
        $language_strings->product_categories = __('Product categories', AODFW_TEXT_DOMAIN);
        $language_strings->store_products = __('Store products', AODFW_TEXT_DOMAIN);
        $language_strings->store_days = __('Days', AODFW_TEXT_DOMAIN);
        $language_strings->condition_store_days_note = __('note', AODFW_TEXT_DOMAIN);
        $language_strings->condition_store_days = __('Days', AODFW_TEXT_DOMAIN);
        $language_strings->condition_store_items_note = __('Store items notes', AODFW_TEXT_DOMAIN);
        $language_strings->condition_store_items = __('Items', AODFW_TEXT_DOMAIN);
        $language_strings->monday = __('Monday', AODFW_TEXT_DOMAIN);
        $language_strings->tuesday = __('Tuesday', AODFW_TEXT_DOMAIN);
        $language_strings->wednesday = __('Wednesday', AODFW_TEXT_DOMAIN);
        $language_strings->thursday = __('Thursday', AODFW_TEXT_DOMAIN);
        $language_strings->friday = __('Friday', AODFW_TEXT_DOMAIN);
        $language_strings->saturday = __('Saturday', AODFW_TEXT_DOMAIN);
        $language_strings->sunday = __('Sunday', AODFW_TEXT_DOMAIN);
        $language_strings->discount_spin_wheel_message = __('Message', AODFW_TEXT_DOMAIN);
        $language_strings->message_after_success_spin = __('Message after successful spin of Roulette.', AODFW_TEXT_DOMAIN);
        $language_strings->zero_for_no_prize = __('Enter "0" for NO prize', AODFW_TEXT_DOMAIN);
        $language_strings->create_discount = __('Create rule', AODFW_TEXT_DOMAIN);
        $language_strings->manage_discount = __('Manage rules', AODFW_TEXT_DOMAIN);
        $language_strings->general = __('General', AODFW_TEXT_DOMAIN);
        $language_strings->condition_name_label = __('Discount Rule Name', AODFW_TEXT_DOMAIN);
        $language_strings->condition_name_placeholder = __('Enter rule name here...', AODFW_TEXT_DOMAIN);
        $language_strings->condition_name_description = __('New year celebration.', AODFW_TEXT_DOMAIN);
        $language_strings->condition_description_label = __('Rule Description', AODFW_TEXT_DOMAIN);
        $language_strings->condition_description_placeholder = __('Enter rule description here...', AODFW_TEXT_DOMAIN);
        $language_strings->condition_description_description = __('Get upto 80% OFF on Mobiles, Fashion,Electronics and more. Save on all products categories with top offers from new year Sale.', AODFW_TEXT_DOMAIN);
        $language_strings->valid_from_label = __('Starts On', AODFW_TEXT_DOMAIN);
        $language_strings->valid_from_placeholder = __('Start date here...', AODFW_TEXT_DOMAIN);
        $language_strings->valid_from_description = __('If you leave empty, This discount rule will be  available in all store days.', AODFW_TEXT_DOMAIN);
        $language_strings->valid_to_label = __('Ends On', AODFW_TEXT_DOMAIN);
        $language_strings->valid_to_placeholder = __('End date here...', AODFW_TEXT_DOMAIN);
        $language_strings->valid_to_description = __('If you leave empty, This discount rule never expire.', AODFW_TEXT_DOMAIN);
        $language_strings->choose_rule_type = __('Choose rule type', AODFW_TEXT_DOMAIN);
        $language_strings->condition = __('Conditions', AODFW_TEXT_DOMAIN);
        $language_strings->rule_type_description = __('By changing the rule type, Conditions and Discounts will change according to rule type.', AODFW_TEXT_DOMAIN);
        $language_strings->no_rule_types = __('No rule Types Found!', AODFW_TEXT_DOMAIN);
        $language_strings->discount = __('Discount', AODFW_TEXT_DOMAIN);
        $language_strings->save = __('Save', AODFW_TEXT_DOMAIN);
        $language_strings->save_and_close = __('Save And Close', AODFW_TEXT_DOMAIN);
        $language_strings->close = __('Close', AODFW_TEXT_DOMAIN);
        $language_strings->sno = __('S.No', AODFW_TEXT_DOMAIN);
        $language_strings->rule_title = __('Discount Name', AODFW_TEXT_DOMAIN);
        $language_strings->rule_is_active = __('Is Discount Active?', AODFW_TEXT_DOMAIN);
        $language_strings->action = __('Action', AODFW_TEXT_DOMAIN);
        $language_strings->rule_start_on = __('Starts On', AODFW_TEXT_DOMAIN);
        $language_strings->rule_end_on = __('Ends On', AODFW_TEXT_DOMAIN);
        $language_strings->no_condition_used = __('No conditions chooses,So conditions are always true...', AODFW_TEXT_DOMAIN);
        $language_strings->yes = __('Yes ', AODFW_TEXT_DOMAIN);
        $language_strings->no = __('No ', AODFW_TEXT_DOMAIN);
        $language_strings->no_rules_found = __('No discount rules found! Create your 1st after order discount', AODFW_TEXT_DOMAIN);
        $language_strings->create_rule = __('Create New Discount', AODFW_TEXT_DOMAIN);
        $language_strings->create_new_rule = __('Create New Rule', AODFW_TEXT_DOMAIN);
        $language_strings->next = __('Next', AODFW_TEXT_DOMAIN);
        $language_strings->previous = __('Previous', AODFW_TEXT_DOMAIN);
        $language_strings->rule_status = __('Status', AODFW_TEXT_DOMAIN);
        $language_strings->rule_type = __('Type', AODFW_TEXT_DOMAIN);
        $language_strings->rule_status_description = __('If you choose "No", This discount rule will disable.', AODFW_TEXT_DOMAIN);
        $language_strings->rule_active = __('Active', AODFW_TEXT_DOMAIN);
        $language_strings->rule_expired = __('Expired', AODFW_TEXT_DOMAIN);
        $language_strings->rule_inactive = __('Inactive', AODFW_TEXT_DOMAIN);
        $language_strings->settings = __('Settings', AODFW_TEXT_DOMAIN);
        $language_strings->plugin_name = __('After Order Discounts', AODFW_TEXT_DOMAIN);
        $language_strings->condition_relationship = __('Conditions relationship', AODFW_TEXT_DOMAIN);
        $language_strings->and = __('And', AODFW_TEXT_DOMAIN);
        $language_strings->or = __('Or', AODFW_TEXT_DOMAIN);
        $language_strings->condition_relation_description = __('Discount could be provided based on “And” and “Or” condition. For Example, if you want to provide a discount for the customers whose order total is 1000 AND payment method is PayPal. ', AODFW_TEXT_DOMAIN);
        $language_strings->plugin_settings = __('Settings', AODFW_TEXT_DOMAIN);
        $language_strings->manage_rule = __('Manage rule', AODFW_TEXT_DOMAIN);
        $language_strings->no_more_conditions_found = __('Sorry, No more conditions found!', AODFW_TEXT_DOMAIN);
        $language_strings->add = __('Add New', AODFW_TEXT_DOMAIN);
        $language_strings->title_order_coupon = __('Order Coupon', AODFW_TEXT_DOMAIN);
//        $language_strings->choose_rule_type = __('General', AODFW_TEXT_DOMAIN);
        //Discounts
        $language_strings->discount_coupon_type = __('Coupon type', AODFW_TEXT_DOMAIN);
        $language_strings->edit_rule = __('Edit Rule', AODFW_TEXT_DOMAIN);
        $language_strings->create_rule = __('Create Rule', AODFW_TEXT_DOMAIN);
        $language_strings->woocommerce = __('WooCommerce', AODFW_TEXT_DOMAIN);
        $language_strings->manage_rules = __('Manage Rules', AODFW_TEXT_DOMAIN);
        $language_strings->discount_coupon_value = __('Coupon value', AODFW_TEXT_DOMAIN);
        $language_strings->discount_coupon_type_description = __('Coupon type could be Flat or Percent basis. Flat would provide a specific amount defined in the coupon value. The “Percent” will give a specified percentage specified in coupon value. ');
        $language_strings->discount_coupon_value_description = __('If the above condition matches, what would be the flat amount or percentage?');
        $language_strings->discount_color_yellow = __('Yellow', AODFW_TEXT_DOMAIN);
        $language_strings->discount_color_green = __('Green', AODFW_TEXT_DOMAIN);
        $language_strings->discount_color_blue = __('Blue', AODFW_TEXT_DOMAIN);
        $language_strings->discount_color_red = __('Red', AODFW_TEXT_DOMAIN);
        $language_strings->discount_color_orange = __('Orange', AODFW_TEXT_DOMAIN);
        $language_strings->discount_spin_wheel_bg_color = __('Background color', AODFW_TEXT_DOMAIN);
        $language_strings->discount_spin_wheel_label = __('Roulette label', AODFW_TEXT_DOMAIN);
        $language_strings->discount_spin_wheel_value = __('Roulette value', AODFW_TEXT_DOMAIN);
        $language_strings->discount_spin_wheel_placeholder_value = __('value', AODFW_TEXT_DOMAIN);
        $language_strings->flat = __('Flat', AODFW_TEXT_DOMAIN);
        $language_strings->percentage = __('Percentage', AODFW_TEXT_DOMAIN);
        $language_strings->in_list = __('in list', AODFW_TEXT_DOMAIN);
        $language_strings->not_in_list = __('not in list', AODFW_TEXT_DOMAIN);
        $language_strings->remove = __('Remove', AODFW_TEXT_DOMAIN);
        $language_strings->discount_spin_wheel_roulette_prizes = __('Roulette Prizes', AODFW_TEXT_DOMAIN);
        $language_strings->discount_spin_wheel_roulette_total_spins = __('Number of spins', AODFW_TEXT_DOMAIN);
        $language_strings->dynamically_add_points = __('Dynamically increase point based on order total?', AODFW_TEXT_DOMAIN);
        $language_strings->every_bug_spent = __('For every ', AODFW_TEXT_DOMAIN);
        $language_strings->every_bug_spent_amount_give_additional = __(' spent amount, Give  ', AODFW_TEXT_DOMAIN);
        $language_strings->give_points = __(' spin points.', AODFW_TEXT_DOMAIN);
        //Conditions
        $language_strings->condition_order_category_note = __('Just like Order Item, the coupon code will not be provided until items from below category are not purchased. Category can be chosen which is in the list or not in the list. This will also be checked on “AND” condition.', AODFW_TEXT_DOMAIN);
        $language_strings->condition_order_payment_method_note = __('Coupon will be provided only to specified customers for specific payment method. All the conditions will be checked based on “AND” condition. ', AODFW_TEXT_DOMAIN);
        $language_strings->condition_order_items_note = __('Coupon Code will not be provided until the customer will purchase below items. You can either specify the products which in the list or not in the list. All these conditions will be evaluated based on “AND” condition.', AODFW_TEXT_DOMAIN);
        $language_strings->condition_order_customers_note = __('This condition could be specified only if you want the coupon to be generated for specific customers or their category. All the conditions will be checked on “AND” condition. Customers could be chosen who are on the list or not on the list. ', AODFW_TEXT_DOMAIN);
        $language_strings->condition_order_total_note = __('The coupon code will be provided only to those customers whose order total will match on the below condition.', AODFW_TEXT_DOMAIN);
        $language_strings->condition_order_total_note_example = __('Coupon would be provided whose order total is below 1000. ', AODFW_TEXT_DOMAIN);
        $language_strings->condition_must = __('Check', AODFW_TEXT_DOMAIN);
        $language_strings->condition_item_categories = __('Categories', AODFW_TEXT_DOMAIN);
        $language_strings->condition_order_payment_methods = __('Payment methods', AODFW_TEXT_DOMAIN);
        $language_strings->condition_order_items = __('Products', AODFW_TEXT_DOMAIN);
        $language_strings->condition_order_customers = __('Customers', AODFW_TEXT_DOMAIN);
        $language_strings->greater_than = __('greater than', AODFW_TEXT_DOMAIN);
        $language_strings->greater_than_or_equal = __('greater than or equal to', AODFW_TEXT_DOMAIN);
        $language_strings->lesser_than = __('less than', AODFW_TEXT_DOMAIN);
        $language_strings->lesser_than_or_equal = __('less than or equal to', AODFW_TEXT_DOMAIN);
        $language_strings->equal_to = __('equal to', AODFW_TEXT_DOMAIN);
        $language_strings->between = __('between', AODFW_TEXT_DOMAIN);
        $language_strings->condition_order_total = __('Order total', AODFW_TEXT_DOMAIN);
        $language_strings->order_categories = __('Order Categories', AODFW_TEXT_DOMAIN);
        $language_strings->order_customers = __('Order Customers', AODFW_TEXT_DOMAIN);
        $language_strings->order_items = __('Order Items', AODFW_TEXT_DOMAIN);
        $language_strings->order_payment_methods = __('Payment Methods', AODFW_TEXT_DOMAIN);
        $language_strings->order_total = __('Order Total', AODFW_TEXT_DOMAIN);
//        $language_strings->discount_spin_wheel_bg_color = __('White', AODFW_TEXT_DOMAIN);
        //settings
        $language_strings->setting_attach_coupon_at = __('Attach coupon details', AODFW_TEXT_DOMAIN);
        $language_strings->setting_attach_after_order_details = __('after order details', AODFW_TEXT_DOMAIN);
        $language_strings->setting_attach_before_order_details = __('before order details', AODFW_TEXT_DOMAIN);
        $language_strings->setting_coupon_message_label = __('Coupon message', AODFW_TEXT_DOMAIN);
        $language_strings->setting_coupon_message_description = __('Use {{coupon_value}},{{coupon_code}} and {{coupon_apply_url}} to render coupon details', AODFW_TEXT_DOMAIN);
        $language_strings->setting_spin_unlimited_label = __('Unlimited', AODFW_TEXT_DOMAIN);
        $language_strings->setting_spin_wheel_text_label = __('Luck wheel text', AODFW_TEXT_DOMAIN);
        $language_strings->setting_spin_menu_title_label = __('My-account menu title', AODFW_TEXT_DOMAIN);
        $language_strings->setting_spin_at_label = __('Show spinner at', AODFW_TEXT_DOMAIN);
        $language_strings->setting_spin_at_desctption = __('Leave this empty to show it in all pages.', AODFW_TEXT_DOMAIN);
        $language_strings->setting_spin_pre_day = __("How How many times the user can spin wheel per day.", AODFW_TEXT_DOMAIN);
        $language_strings->setting_spin_attatach_mail_at = __('Attach coupon details', AODFW_TEXT_DOMAIN);
        $language_strings->setting_spin_show_for_non_loggedin_label = __('Show lucky wheel for non logged in users', AODFW_TEXT_DOMAIN);
        $language_strings->setting_spin_wheel_message_label = __('Lucky wheel message', AODFW_TEXT_DOMAIN);
        $language_strings->setting_spin_wheel_message_description = __('Use {{spin_point}} and {{spin_details}} to render Lucky wheel details', AODFW_TEXT_DOMAIN);
        $language_strings->spin_wheel_title = __('Coupon Roulette', AODFW_TEXT_DOMAIN);
        $language_strings->spin_wheel_prize_created = __('Your lucky prize has created!', AODFW_TEXT_DOMAIN);
        $language_strings->spin_wheel_no_prize = __('No lucky prize!', AODFW_TEXT_DOMAIN);
        $language_strings->spin_wheel_no_prize_found = __('No lucky prizes found!', AODFW_TEXT_DOMAIN);
        $language_strings->invalid_request = __('Invalid request found!', AODFW_TEXT_DOMAIN);
        $language_strings->rules_saved_successfully = __('Rule saved successfully!', AODFW_TEXT_DOMAIN);
        $language_strings->settings_saved_successfully = __('Settings saved successfully!', AODFW_TEXT_DOMAIN);
        $language_strings->settings_already_saved_successfully = __('Settings already saved!', AODFW_TEXT_DOMAIN);
        $language_strings->unable_to_rules = __('Unable to save rule! Please try again later!', AODFW_TEXT_DOMAIN);
        $language_strings->successfully_processed = __('Request successfully processed', AODFW_TEXT_DOMAIN);
        $language_strings->not_successfully_processed = __('Unable to process your request', AODFW_TEXT_DOMAIN);
        $language_strings->rule_type_not_found = __('rule type not found!', AODFW_TEXT_DOMAIN);
        $language_strings->thanks_for_using_plugin = __('Thank you for using woocommerce after order discounts', AODFW_TEXT_DOMAIN);
        $language_strings->version = __('Version', AODFW_TEXT_DOMAIN);
        $language_strings->and = __('And', AODFW_TEXT_DOMAIN);
        $language_strings->install_woocommerce = __('Woocommerce must installed and activated for proper functioning of Woocommerce after order discounts!', AODFW_TEXT_DOMAIN);
//        $language_strings->discount_spin_wheel_bg_color = __('White', AODFW_TEXT_DOMAIN);
        return $language_strings;
    }
}