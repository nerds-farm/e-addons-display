<?php

namespace EAddonsDisplay\Modules\Display\Triggers;

use EAddonsForElementor\Core\Utils;
use EAddonsForElementor\Base\Base_Trigger;
use Elementor\Controls_Manager;

/**
 * Description of Random
 *
 * @author fra
 */
class Random extends Base_Trigger {

    public function get_trigger_controls($element) {
        $element->add_control(
                'e_display_random',
                [
                    'label' => __('Random', 'elementor'),
                    'type' => Controls_Manager::SLIDER,
                    'size_units' => ['%'],
                    'range' => [
                        '%' => [
                            'min' => 0,
                            'max' => 100,
                        ],
                    ],
                    'separator' => 'before',
                ]
        );
    }

    public function is_triggered($element, $settings) {
        if (!empty($settings['e_display_random']['size'])) {
            $this->add_triggered('e_display_random');
            $rand = mt_rand(1, 100);
            if ($rand <= $settings['e_display_random']['size']) {
                $this->add_conditions('e_display_random');
            }
        }
    }

}
