<?php

namespace EAddonsDisplay\Modules\Display\Triggers;

use EAddonsForElementor\Core\Utils;
use EAddonsForElementor\Base\Base_Trigger;
use Elementor\Controls_Manager;

/**
 * Description of Woocommerce
 *
 * @author fra
 */
class Woocommerce extends Base_Trigger {

    public function get_trigger_controls($element) {
        if (Utils::is_plugin_active('woocommerce')) {

            $element->add_control(
                    'e_display_woo_product_cart',
                    [
                        'label' => __('Product in Cart', 'elementor'),
                        'type' => 'e-query',
                        'placeholder' => __('Search Product', 'elementor'),
                        'label_block' => true,
                        'query_type' => 'posts',
                        'object_type' => 'product',
                        'separator' => 'before',
                    ]
            );
            
            $element->add_control(
                    'e_display_woo_product_purchased',
                    [
                        'label' => __('Purchased Product', 'elementor'),
                        'type' => 'e-query',
                        'placeholder' => __('Search Product', 'elementor'),
                        'label_block' => true,
                        'query_type' => 'posts',
                        'object_type' => 'product',
                        'separator' => 'before',
                    ]
            );
            
            $element->add_control(
                    'e_display_woo_cart_amount',
                    [
                        'label' => __('Cart Total', 'elementor'),
                        'type' => Controls_Manager::NUMBER,
                        'separator' => 'before',
                        'description' => __('Total amount >= this value', 'elementor'),
                    ]
            );
            /*$element->add_control(
                    'e_display_woo_cart_amount_compare',
                    [
                        'label' => __('Cart Total Compare', 'elementor'),
                        'type' => Controls_Manager::SELECT,
                        'options' => $this->operator_options,
                        'default' => 'gte',
                        'condition' => [
                            'e_display_woo_cart_amount!' => '',
                        ]
                    ]
            );*/


            if (Utils::is_plugin_active('woocommerce-memberships')) {
                $plans = get_posts([
                    'post_type' => 'wc_membership_plan',
                    'post_status' => 'publish',
                    'numberposts' => -1
                ]);
                if (!empty($plans)) {

                    $element->add_control(
                            'e_display_woo_membership_post', [
                        'label' => __('Use Post Membership settings', 'elementor'),
                        'type' => Controls_Manager::SWITCHER,
                        'separator' => 'before',
                            ]
                    );

                    $plan_options = array(0 => __('NOT Member', 'elementor'));
                    foreach ($plans as $aplan) {
                        //$plan_options[$aplan->post_name] = $aplan->post_title;
                        $plan_options[$aplan->ID] = $aplan->post_title;
                    }
                    $element->add_control(
                            'e_display_woo_membership', [
                        'label' => __('Membership', 'elementor'),
                        'type' => Controls_Manager::SELECT2,
                        'options' => $plan_options,
                        'multiple' => true,
                        'label_block' => true,
                        'condition' => [
                            'e_display_woo_membership_post' => '',
                        ],
                            ]
                    );
                }
            }
        } else {
            $element->add_control(
                            'e_display_woo_no', [
                        'label' => __('WooCommerce not enabled', 'elementor'),
                        'type' => Controls_Manager::HEADING,
                            ]
                    );
        }
    }

    public function is_triggered($element, $settings) {
        if (empty($settings['e_display_triggers']) || in_array('woocommerce', $settings['e_display_triggers'])) {
            // WOOCOMMERCE
            if (Utils::is_plugin_active('woocommerce')) {

                if (!empty($settings['e_display_woo_product_cart'])) {
                    $this->add_triggered('e_display_woo_product_cart');

                    $product_id = $settings['e_display_woo_product_cart'];

                    $product_cart_id = WC()->cart->generate_cart_id($product_id);
                    $in_cart = WC()->cart->find_product_in_cart($product_cart_id);
                    if ($in_cart) {
                        $this->add_conditions('e_display_woo_product_cart');
                    }
                    //WC()->cart->remove_cart_item( $product_cart_id  );
                }
                
                if (!empty($settings['e_display_woo_product_purchased'])) {
                    $this->add_triggered('e_display_woo_product_purchased');

                    $product_id = $settings['e_display_woo_product_purchased'];
                    $user = wp_get_current_user();
                    if ($user) {
                        if (wc_customer_bought_product($user->user_email, $user->ID, $product_id)) {
                            $this->add_conditions('e_display_woo_product_purchased');
                        }
                    }
                }
                
                if (isset($settings['e_display_woo_cart_amount']) && $settings['e_display_woo_cart_amount'] != '') {
                    $this->add_triggered('e_display_woo_cart_amount');
                    $cart_amount = intval($settings['e_display_woo_cart_amount']);
                    $cart_total = floatval(WC()->cart->total);
                    //if ($this->check_condition($cart_total, $settings['e_display_woo_cart_amount_compare'], $cart_amount)) {
                    if ($cart_total >= $cart_amount) {
                        $this->add_conditions('e_display_woo_cart_amount');
                    }
                }
                

                if (Utils::is_plugin_active('woocommerce-memberships')) {

                    if ($settings['e_display_woo_membership_post']) {
                        $this->add_triggered('e_display_woo_membership_post');

                        if (function_exists('wc_memberships_is_user_active_or_delayed_member')) {
                            $user_id = get_current_user_id();
                            $has_access = true;
                            $rules = wc_memberships()->get_rules_instance()->get_post_content_restriction_rules($post_ID);
                            if (!empty($rules)) {
                                $has_access = false;
                                if ($user_id) {
                                    foreach ($rules as $rule) {
                                        if (wc_memberships_is_user_active_or_delayed_member($user_id, $rule->get_membership_plan_id())) {
                                            $has_access = true;
                                            break;
                                        }
                                    }
                                }
                            }
                            if (\Elementor\Plugin::$instance->editor->is_edit_mode()) {
                                $has_access = true;
                            }
                            if ($has_access) {
                                $this->add_conditions('e_display_woo_membership_post');
                            }
                        }
                    } else {

                        //roles
                        if (isset($settings['e_display_woo_membership']) && !empty($settings['e_display_woo_membership'])) {


                            $this->add_triggered('e_display_woo_membership');

                            $current_user_id = get_current_user_id();
                            if ($current_user_id) {
                                //var_dump($current_user->ID);
                                $member_plans = get_posts(array(
                                    'author' => $current_user_id,
                                    'post_type' => 'wc_user_membership',
                                    'post_status' => 'wcm-active',
                                    'posts_per_page' => -1,
                                ));
                                $user_members = array();
                                if (empty($member_plans)) {
                                    // not member
                                    if (in_array(0, $settings['e_display_woo_membership'])) {
                                        $this->add_conditions('e_display_woo_membership');
                                    }
                                } else {
                                    // find all user membership plan
                                    foreach ($member_plans as $member) {
                                        $user_members[] = $member->post_parent;
                                    }
                                    $tmp_members = array_intersect($user_members, $settings['e_display_woo_membership']);
                                    if (!empty($tmp_members)) {
                                        $this->add_conditions('e_display_woo_membership');
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
    }

}
