<?php

namespace EAddonsDisplay\Modules\Display\Tabs;

use \EAddonsForElementor\Base\Base_Tab;

/**
 * Description of display
 *
 * @author fra
 */
class Display extends Base_Tab {

    public function get_id() {
        return 'e_' . $this->get_name();
    }

    public function get_title() {
        return __(ucfirst($this->get_name()), 'elementor');
    }

    protected function register_tab_controls() {
        
        $element = $this;
        if (is_subclass_of($this, 'Elementor\Element_Base')) {
            $element = $this->parent;
        }

        $element->start_controls_section(
                'e_section_' . $this->get_name() . '_advanced', [
            'tab' => 'e_' . $this->get_name(),
            'label' => __(ucfirst($this->get_name()), 'elementor'),
                ]
        );
        $element->end_controls_section();

        $icon = '<i class="fa fa-life-ring pull-right ml-1" aria-hidden="true"></i> ';
        $element->start_controls_section(
                'e_section_' . $this->get_name() . '_fallback', [
            'tab' => 'e_' . $this->get_name(),
            'label' => $icon . __('Fallback', 'elementor'),
            'condition' => [
                'e_' . $this->get_name() . '_mode!' => '',
            ]
                ]
        );
        $element->end_controls_section();
    }

}
