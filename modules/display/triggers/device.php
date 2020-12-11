<?php

namespace EAddonsDisplay\Modules\Display\Triggers;

use EAddonsForElementor\Core\Utils;
use EAddonsForElementor\Base\Base_Trigger;
use Elementor\Controls_Manager;

/**
 * Description of Device
 *
 * @author fra
 */
class Device extends Base_Trigger {

    public function get_trigger_controls($element) {
        $element->add_control(
                'e_display_responsive', [
            'label' => __('Responsive', 'elementor'),
            'type' => Controls_Manager::CHOOSE,
            'options' => [
                'desktop' => [
                    'title' => __('Desktop and Tv', 'elementor'),
                    'icon' => 'fa fa-desktop',
                ],
                'mobile' => [
                    'title' => __('Mobile and Tablet', 'elementor'),
                    'icon' => 'fa fa-mobile',
                ]
            ],
            'description' => __('Not really responsive, remove the element from the code based on the user\'s device. This trigger use native WP device detenction.', 'elementor') . ' <a href="https://codex.wordpress.org/Function_Reference/wp_is_mobile" target="_blank">' . __('Read more.', 'elementor') . '</a>',
            'separator' => 'before',
                ]
        );
        $element->add_control(
                'e_display_browser', [
            'label' => __('Browser', 'elementor'),
            'type' => Controls_Manager::SELECT2,
            'options' => array(
                'is_chrome' => 'Google Chrome',
                'is_gecko' => 'FireFox',
                'is_safari' => 'Safari',
                'is_IE' => 'Internet Explorer',
                'is_edge' => 'Microsoft Edge',
                'is_NS4' => 'Netscape',
                'is_opera' => 'Opera',
                'is_lynx' => 'Lynx',
                'is_iphone' => 'iPhone Safari'
            ),
            'description' => __('Trigger for a specific user browser.', 'elementor'),
            'multiple' => true,
                ]
        );
    }

    public function is_triggered($element, $settings) {
        // DEVICE
        if (empty($settings['e_display_triggers']) || in_array('device', $settings['e_display_triggers'])) {

                // responsive
                if (isset($settings['e_display_responsive']) && $settings['e_display_responsive']) {

                    $this->add_triggered('e_display_responsive');

                    if (wp_is_mobile()) {
                        if ($settings['e_display_responsive'] == 'mobile') {
                            $this->add_conditions('e_display_responsive');
                        }
                    } else {
                        if ($settings['e_display_responsive'] == 'desktop') {
                            $this->add_conditions('e_display_responsive');
                        }
                    }
                }

                // browser
                if (!empty($settings['e_display_browser']) && is_array($settings['e_display_browser'])) {

                    $this->add_triggered('e_display_browser');

                    $is_browser = false;
                    foreach ($settings['e_display_browser'] as $browser) {
                        global $$browser;
                        //var_dump($$browser);
                        if (isset($$browser) && $$browser) {
                            $is_browser = true;
                        }
                    }
                    //$hidden_browser = false;
                    if ($is_browser) {
                        $this->add_conditions('e_display_browser');                       
                    }
                }
        }
    }

}
