<?php

namespace EAddonsDisplay\Modules\Display\Triggers;

use EAddonsForElementor\Core\Utils;
use EAddonsForElementor\Base\Base_Trigger;
use Elementor\Controls_Manager;

/**
 * Description of Events
 *
 * @author fra
 */
class Events extends Base_Trigger {

    public function get_title() {
        return __('Frontend Events', 'e-addons');
    }

    public function get_trigger_controls($element) {
        $element->add_control(
                'e_display_events_note', [
            'label' => '<strong><i class="eicon-warning"></i> ' . __('ATTENTION', 'elementor') . '</strong>',
            'type' => \Elementor\Controls_Manager::RAW_HTML,
            'raw' => '<small><br>' . __('In orderd to use an Event trigger is necessary to activate ', 'elementor') . '<strong>' . __('Keep HTML', 'elementor') . '</strong>' . __(' from base settings', 'elementor') . '</small>',
            'content_classes' => 'e-editor-notice',
            'separator' => 'before',
                ]
        );

        $element->add_control(
                'e_display_click',
                [
                    'label' => __('On Click', 'elementor'),
                    'type' => \Elementor\Controls_Manager::TEXT,
                    'description' => __('Write here the Selector in jQuery format of the Button which will toggle selected Element.', 'elementor'),
                    'label_block' => true,
                    'separator' => 'before',
                ]
        );
        $element->add_control(
                'e_display_click_show', [
            'label' => __('Show Animation', 'elementor'),
            'type' => Controls_Manager::SELECT,
            'options' => array(
                '' => __('None', 'e-addons'),
                'slide' => __('Slide', 'e-addons'),
                'fade' => __('Fade', 'e-addons')
            ),
            'condition' => [
                'e_display_click!' => '',
            ],
                ]
        );
        $element->add_control(
                'e_display_click_other',
                [
                    'label' => __('Hide other elements', 'elementor'),
                    'type' => \Elementor\Controls_Manager::TEXT,
                    'description' => __('Write here the Selector in jQuery format.', 'elementor'),
                    'condition' => [
                        'e_display_click!' => '',
                    ],
                ]
        );
        /* $element->add_control(
          'e_display_click_hide', [
          'label' => __('Hide Animation', 'elementor'),
          'type' => Controls_Manager::SELECT,
          'options' => Utils::get_jquery_display_mode(),
          'condition' => [
          'e_display_dom!' => '',
          'e_display_click!' => '',
          ],
          ]
          ); */
        $element->add_control(
                'e_display_click_toggle', [
            'label' => __('Toggle', 'elementor'),
            'type' => Controls_Manager::SWITCHER,
            'condition' => [
                'e_display_click!' => '',
            ],
                ]
        );

        $element->add_control(
                'e_display_load', [
            'label' => __('On Page Load', 'elementor'),
            'type' => Controls_Manager::SWITCHER,
            'separator' => 'before'
                ]
        );
        $element->add_control(
                'e_display_load_delay', [
            'label' => __('Delay time', 'elementor'),
            'type' => Controls_Manager::NUMBER,
            'min' => 0,
            'default' => 0,
            'condition' => [
                'e_display_load!' => '',
            ],
                ]
        );
        $element->add_control(
                'e_display_load_show', [
            'label' => __('Show Animation', 'elementor'),
            'type' => Controls_Manager::SELECT,
            'options' => array(
                '' => __('None', 'e-addons'),
                'slide' => __('Slide', 'e-addons'),
                'fade' => __('Fade', 'e-addons'),
            ),
            'condition' => [
                'e_display_load!' => '',
            ],
                ]
        );
    }

    public function is_triggered($element, $settings) {
        if (!empty($settings['e_display_click'])) {
            $this->add_triggered('e_display_click');
        }
        if (isset($settings['e_display_load']) && $settings['e_display_load']) {
            $this->add_triggered('e_display_load');
        }
    }

    public function print_trigger_scripts($element, $repeater_settings = array()) {
        if (!empty($repeater_settings['e_display_repeater'])) {
            foreach ($repeater_settings['e_display_repeater'] as $settings) {
                //echo '<pre>';var_dump($settings);echo '</pre>';
                if (!empty($settings['e_display_click'])) {

                    switch ($settings['e_display_click_show']) {
                        case 'slide':
                            $jFunction = 'slideDown';
                            $jFunctionHide = 'slideUp';
                            break;
                        case 'fade':
                            $jFunction = 'fadeIn';
                            $jFunctionHide = 'fadeOut';
                            break;
                        default:
                            $jFunction = 'show';
                            $jFunctionHide = 'hide';
                    }
                    $show = true;
                    if ($repeater_settings['e_display_mode'] == 'hide') {
                        $show = false;
                        $jFunction = $jFunctionHide;
                    }

                    if ($settings['e_display_click_toggle']) {
                        if ($settings['e_display_click_show']) {
                            $jFunctionToggle = $settings['e_display_click_show'] . 'Toggle';
                        } else {
                            $jFunctionToggle = 'toggle';
                        }
                        $jFunction = $jFunctionToggle;
                    } else {
                        if ($show) {
                            $jFunctionToggle = $jFunctionHide;
                        } else {
                            $jFunctionToggle = $jFunction;
                        }
                    }
                    ?>
                    <script>
                        jQuery(document).ready(function () {
                            jQuery('<?php echo $settings['e_display_click']; ?>').on('click', function () {
                                //console.log('<?php echo $settings['e_display_click_other']; ?>');
                                //console.log('<?php echo $settings['e_display_click_show']; ?>');
                    <?php if ($settings['e_display_click_other']) { ?>
                                    jQuery('<?php echo $settings['e_display_click_other']; ?>').stop();
                                            jQuery('<?php echo $settings['e_display_click_other']; ?>').not('.elementor-element-<?php echo $element->get_id(); ?>').<?php echo $jFunctionToggle; ?>(<?php echo ($settings['e_display_click_show']) ? '400, function() {' : ');'; ?>
                    <?php } ?>
                                //console.log('fine <?php echo $settings['e_display_click_other']; ?> fadeout');
                                jQuery('.elementor-element-<?php echo $element->get_id(); ?>')<?php echo ($settings['e_display_click_show']) ? '.delay(400)' : ''; ?>.<?php echo $jFunction; ?>();
                                //console.log(jQuery(this).attr('href'));
                    <?php
                    if ($settings['e_display_click_other'] && $settings['e_display_click_show']) {
                        echo '});';
                    }
                    ?>
                                if (jQuery(this).attr('href') == '#') {
                                    return false;
                                }
                            });
                        });
                    </script>
                    <?php
                }
                if (!empty($settings['e_display_load'])) {
                    if ($settings['e_display_load_show']) {
                        $jFunctionToggle = $settings['e_display_load_show'] . 'Toggle';
                    } else {
                        $jFunctionToggle = 'toggle';
                    }
                    ?>
                    <script>
                        jQuery(document).ready(function () {
                            //alert('<?php echo $jFunctionToggle; ?>');
                            jQuery(window).on('load', function () {
                                setTimeout(function () {
                                    jQuery('.elementor-element-<?php echo $element->get_id(); ?>').<?php echo $jFunctionToggle; ?>();
                                }, <?php echo $settings['e_display_load_delay'] ? $settings['e_display_load_delay'] : '0'; ?>);
                            });
                        });
                    </script>
                    <?php
                }
            }
        }
    }

}
