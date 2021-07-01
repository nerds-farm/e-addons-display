<?php

namespace EAddonsDisplay\Modules\Display\Triggers;

use EAddonsForElementor\Core\Utils;
use EAddonsForElementor\Core\Managers\Assets;
use EAddonsForElementor\Base\Base_Trigger;
use Elementor\Controls_Manager;

/**
 * Description of Events
 *
 * @author fra
 */
class Events extends Base_Trigger {

    public static $enqueued = [];

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

                //echo '<pre>';var_dump($settings);echo '</pre>'; die();
                if (!empty($settings['e_display_click'])) {
                    $jkey = $element->get_id() . '_click';
                    if (!in_array($jkey, self::$enqueued)) {                        
                        $element_id = $this->get_element_id($element);
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
                        ob_start();
                        ?>
                        <script>
                            window.addEventListener('load', (event) => {
                                jQuery('<?php echo $settings['e_display_click']; ?>').on('click', function () {
                                    //console.log('<?php echo $settings['e_display_click_other']; ?>');
                                    //console.log('<?php echo $settings['e_display_click_show']; ?>');
                        <?php if ($settings['e_display_click_other']) { ?>
                                        jQuery('<?php echo $settings['e_display_click_other']; ?>').stop();
                                        jQuery('<?php echo $settings['e_display_click_other']; ?>').not('.elementor-element-<?php echo $element->get_id(); ?>').<?php echo $jFunctionToggle; ?>(<?php echo ($settings['e_display_click_show']) ? '400, function() {' : ');'; ?>
                        <?php } ?>
                                    //console.log('fine <?php echo $settings['e_display_click_other']; ?> fadeout');
                                    jQuery('<?php echo $element_id; ?>')<?php echo ($settings['e_display_click_show']) ? '.delay(400)' : ''; ?>.<?php echo $jFunction; ?>();
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
                        $js = ob_get_clean();
                        $js = Assets::enqueue_script($jkey, $js);
                        self::$enqueued[] = $jkey;
                    }
                }
                if (!empty($settings['e_display_load'])) {
                    $jkey = $element->get_id() . '_load';
                    if (!in_array($jkey, self::$enqueued)) {                        
                        $element_id = $this->get_element_id($element);
                        if ($settings['e_display_load_show']) {
                            $jFunctionToggle = $settings['e_display_load_show'] . 'Toggle';
                        } else {
                            $jFunctionToggle = 'toggle';
                        }
                        ob_start();
                        ?>
                        <script>
                            window.addEventListener('load', (event) => {
                                //alert('<?php echo $jFunctionToggle; ?>');
                                setTimeout(function () {
                                    //console.log('toggle');
                                    jQuery('<?php echo $element_id; ?>').<?php echo $jFunctionToggle; ?>();
                                }, <?php echo $settings['e_display_load_delay'] ? $settings['e_display_load_delay'] : '0'; ?>);
                            });
                        </script>
                        <?php
                        $js = ob_get_clean();
                        $js = Assets::enqueue_script($jkey, $js);
                        self::$enqueued[] = $jkey;
                    }
                }
            }
        }
    }

    public function get_element_id($element) {
        $element_settings = $element->get_settings_for_display();
        $element_id = '.elementor-element-' . $element->get_id();
        if (!empty($element_settings['css_classes'])) {
            $css_classes = trim($element_settings['css_classes']);
            if ($css_classes) {
                $css_classes = str_replace(' ', '.', $css_classes);
                $element_id .= '.' . $css_classes;
            }
        }
        if (!empty($element_settings['_element_id'])) {
            $element_id .= '#' . $element_settings['_element_id'];
        }
        return $element_id;
    }

}
