<?php

namespace EAddonsDisplay\Modules\Display\Skins;

use EAddonsForElementor\Base\Base_Skin;
use EAddonsForElementor\Core\Utils;
use Elementor\Controls_Manager;
use Elementor\Widget_Base;

if (!defined('ABSPATH'))
    exit; // Exit if accessed directly

class Hidden extends Base_Skin {

    public function __construct($parent = []) {
        if (!empty($parent)) {
            parent::__construct($parent);
        }
    }

    public function get_id() {
        return 'hidden';
    }

    /*
      public function get_icon() {
      return 'eadd-posts-skin-template';
      }

      public function get_pid() {
      return 297;
      }
     */

    public function get_title() {
        return __('Hidden', 'e-addons');
    }
    
    public function show_in_settings() {
        return false;
    }

    public function register_controls(Widget_Base $widget) {
        $this->parent = $widget;
    }

    public function register_style_sections() {
        
    }

    public function render() {
        //echo 'Hidden';
        return;
    }

}
