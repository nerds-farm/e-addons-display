<?php
namespace EAddonsDisplay\Modules\Display\Triggers;

use EAddonsForElementor\Base\Base_Trigger;
use Elementor\Controls_Manager;

/**
 * Description of Dynamic_Tag
 *
 * @author fra
 */
class Dynamic_Tag extends Base_Trigger {

    public function get_trigger_controls($element) {
        $refl = new \ReflectionClass('\Elementor\Modules\DynamicTags\Module');
        //print_r($refl->getConstants());
        $element->add_control(
                'e_display_dynamic_tag',
                [
                    'label' => __('Dynamic Tag', 'elementor'),
                    'type' => Controls_Manager::MEDIA,
                    'dynamic' => [
                        'active' => true,
                        'categories' => $refl->getConstants(),
                    ],
                    'placeholder' => __('Select condition field', 'elementor'),
                    'separator' => 'before',
                    'condition' => [
                        'e_display_trigger' => 'dynamic_tag',
                    ]
                ]
        );
        $element->add_control(
                'e_display_dynamic_tag_status',
                [
                    'label' => __('Status', 'elementor'),
                    'type' => Controls_Manager::SELECT,
                    'label_block' => true,
                    'options' => $this->operator_options,
                    'default' => 'valued',
                ]
        );
        $element->add_control(
                'e_display_dynamic_tag_value',
                [
                    'type' => Controls_Manager::TEXT,
                    'label' => __('Value', 'elementor'),
                    'label_block' => true,
                    'condition' => [
                        'e_display_dynamic_tag_status!' => ['empty', 'not_empty'],
                    ],
                ]
        );
    }
    
    public function is_triggered($element, $settings) {
        if (empty($settings['e_display_triggers']) || in_array('dynamic_tag', $settings['e_display_triggers'])) {
            if (!empty($settings['__dynamic__']) && !empty($settings['__dynamic__']['e_display_dynamic_tag'])) {
                $this->add_triggered('e_display_dynamic_tag');
                $my_val = $settings['e_display_dynamic_tag'];                
                $condition_result = self::check_condition($my_val, $settings['e_display_dynamic_tag_status'], $settings['e_display_dynamic_tag_value']);
                if ($condition_result) {
                    $this->add_conditions('e_display_dynamic_tag');
                }
            }
        }
    }

}
