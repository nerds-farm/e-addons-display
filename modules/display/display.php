<?php
namespace EAddonsDisplay\Modules\Display;

use EAddonsForElementor\Core\Utils;
use EAddonsForElementor\Base\Module_Base;
use Elementor\Controls_Manager;
use Elementor\Modules\DynamicTags\Module as Module_DynamicTags;

if (!defined('ABSPATH'))
    exit; // Exit if accessed directly

/**
 * Hide extenstion
 *
 * @since 1.0.1
 */
class Display extends Module_Base {

    public function __construct() {
            parent::__construct();
    }
    
    public function get_name() {
            return 'display';
    }

}
