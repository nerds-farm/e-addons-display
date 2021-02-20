<?php

namespace EAddonsDisplay\Modules\Display\Extensions;

use EAddonsForElementor\Core\Utils;
use EAddonsForElementor\Base\Base_Extension;
use Elementor\Controls_Manager;

if (!defined('ABSPATH'))
    exit; // Exit if accessed directly

/**
 * Hide extenstion
 *
 * @since 1.0.1
 */
class Display extends Base_Extension {
    
    public $common = true;
    public $common_sections_actions = array(
        'widget' => array(
            'element' => 'widget',
            'action' => '_section_style',
        ),
        'section' => array(
            'element' => 'section',
            'action' => 'section_advanced',
        ),
        'column' => array(
            'element' => 'column',
            'action' => 'section_advanced',
        )
    );    

    public static $triggers = [];
    
    public $triggered = [];
    public $conditions = [];
    
    public function __construct() {
        parent::__construct();
        $this->add_actions();
    }

    public function get_name() {
        return 'display';
    }
    
    public function get_icon() {
        return 'eadd-extension-display';
    }
    
    public function get_pid() {
        return 210;
    }

    /**
     * Add Actions
     *
     * @access private
     */
    protected function add_actions() {        
        
        add_action('elementor/editor/after_enqueue_scripts', [$this, 'enqueue_editor_assets']);
        add_action('elementor/preview/enqueue_styles', [$this, 'enqueue_preview_assets']);
        
        add_action('elementor/element/before_section_end', function($element, $section_id, $args) {
            if ($section_id == 'e_section_' . $this->get_name() . '_advanced') {
                $args['section'] = 'advanced';
                $this->add_controls($element, $args);
            }
            if ($section_id == 'e_section_' . $this->get_name() . '_fallback') {
                $args['section'] = 'fallback';
                $this->add_controls($element, $args);
            }
        }, 10, 3);
        
        // create Tab for empty Template
        $tab = $this->register_tab();
        $tab->_register_tab();
        
        // fix form fields
        add_action('elementor/element/form/section_form_fields/after_section_end', function($element, $args) {
            $form_fields = $element->get_controls('form_fields');
            foreach($form_fields['fields'] as $key => $control) {
                if (substr($key, 0, 10) == 'e_display_') {
                    unset($form_fields['fields'][$key]);
                }
            }
            $element->update_control('form_fields', $form_fields);
            //echo '<pre>';var_dump($element->get_controls('form_fields'));echo '</pre>'; die();
        }, 10, 2);

        //if (!Utils::is_preview()) {
            
            /*
            add_filter( 'elementor/frontend/section/should_render', [$this, 'should_render'], 10, 3);
            add_filter( 'elementor/frontend/column/should_render', [$this, 'should_render'], 10, 3);
            add_filter( 'elementor/frontend/widget/should_render', [$this, 'should_render'], 10, 3);
            */
            
            add_action("elementor/frontend/widget/before_render", [$this, '_before']);
            add_action("elementor/frontend/widget/after_render", [$this, '_after']);
            add_action("elementor/frontend/section/before_render", [$this, '_before']);
            add_action("elementor/frontend/section/after_render", [$this, '_after']);
            add_action("elementor/frontend/column/before_render", [$this, '_before']);
            add_action("elementor/frontend/column/after_render", [$this, '_after']);
            add_action("elementor/frontend/section/before_render", function($element) {
                $columns = $element->get_children();
                if (!empty($columns)) {
                    $cols_visible = count($columns);
                    $cols_hidden = 0;
                    foreach ($columns as $acol) {
                        if ($this->is_hidden($acol)) {
                            $fallback = $acol->get_settings('e_display_fallback');
                            if (empty($fallback)) {
                                $cols_visible--;
                                $cols_hidden++;
                            }
                        }
                    }
                    if ($cols_hidden) {
                        if ($cols_visible) {
                            switch ($cols_visible) {
                                case 10: $_column_size = 10;
                                    break;
                                case 9: $_column_size = 11;
                                    break;
                                case 8: $_column_size = 12;
                                    break;
                                case 7: $_column_size = 14;
                                    break;
                                case 6: $_column_size = 16;
                                    break;
                                case 5: $_column_size = 20;
                                    break;
                                case 4: $_column_size = 25;
                                    break;
                                case 3: $_column_size = 33;
                                    break;
                                case 2: $_column_size = 50;
                                    break;
                                case 1: default: $_column_size = 100;
                            }
                            foreach ($columns as $acol) {
                                if ($acol->set_settings('_column_size', $_column_size)) {

                                }
                            }
                        } else {
                            $element->add_render_attribute('_wrapper', 'class', 'elementor-hidden');
                            $element->add_render_attribute('_wrapper', 'class', 'e-display-original-content');
                        }
                    }
                }
            }, 10, 1);
        //}
        
        /*if (!class_exists('Elementor\Widget_Common')) {
            include_once(ELEMENTOR_PATH.'includes'.DIRECTORY_SEPARATOR.'widgets'.DIRECTORY_SEPARATOR.'common.php');
        }*/
        //$common = new \EAddonsDisplay\Modules\Display\Widget_Common();
    }

    /**
     * Enqueue admin styles
     *
     * @since 0.7.0
     *
     * @access public
     */
    public function enqueue_editor_assets() {
        wp_enqueue_style('e-addons-editor-display');
        wp_enqueue_script('e-addons-editor-display');
    }

    public function enqueue_preview_assets() {
        wp_enqueue_style('e-addons-preview-display');
    }

    public final function add_common_sections($element, $args) {

        // Check if this section exists
        $section_exists = \Elementor\Plugin::instance()->controls_manager->get_control_from_stack($element->get_unique_name(), 'e_section_' . $this->get_name() . '_advanced');
        if (!is_wp_error($section_exists)) {
            return false;
        }
        
        //$element->get_name()
        //if ($element->get_name() == 'form') {
        //var_dump($element);
            //echo '-n:'.$element->get_name(); echo '-un:'.$element->get_unique_name(); echo '-t:'.$element->get_type();
            $tab = $this->register_tab($element);
            $tab->register_controls();
        //}
    }
    
    public function register_tab($element = null) {
        $tab = new \EAddonsDisplay\Modules\Display\Tabs\Display($element);
        return $tab;
    }
    
    public function has_inspector() {
        if (class_exists('EAddonsInspector\Modules\Inspector\Globals\Frontend_Inspector')) {
            //echo 'FI';
            return true;
        }
        return false;
    }
    
    public function set_hidden_skin($element) {
        if ($element->get_type() == 'widget') {
            $element->add_skin(new \EAddonsDisplay\Modules\Display\Skins\Hidden($element));
            $element->set_settings('_skin', 'hidden');
        } else {
            foreach ( $element->get_children() as $child ) {
                $this->set_hidden_skin($child);
            }
        }
    }

    public function _ob($settings) {
        if ($this->has_inspector()) {
            if (Utils::get_elementor_capability() && isset($_GET[\EAddonsInspector\Modules\Inspector\Globals\Frontend_Inspector::GET_VAR])) {
                return false;
            }
        }
        if (empty($settings['e_display_dom'])) {
            return true;
        }
        return false;
    }

    public function _before($element) {
        $settings = $element->get_settings();
        if (!empty($settings['e_display_mode'])) {
            $hidden = $this->is_hidden($element);
            if ($hidden) {
                if (empty($settings['e_display_dom'])) { 
                    $this->set_hidden_skin($element);
                }                 
                echo WP_DEBUG ? '<!-- E-ADDONS DISPLAY NONE ' . $element->get_type() . ' (' . $element->get_id() . ')-->' : '';
                if ($this->_ob($settings)) {
                    ob_start();
                } else {
                    $element->add_render_attribute('_wrapper', 'class', 'elementor-hidden');
                    $element->add_render_attribute('_wrapper', 'class', 'e-display-original-content');
                }
            }
            //$this->set_element_view_counters($element, $hidden);
        }
    }

    public function _after($element) {
        $settings = $element->get_settings_for_display();
        if (!empty($settings['e_display_mode'])) {
            if ($this->is_hidden($element)) {
                if ($this->_ob($settings)) {
                    $content = ob_get_clean();
                    //echo $content;
                }
                $fallback = $this->get_fallback($settings, $element);
                if ($fallback) {
                    $fallback = str_replace('elementor-hidden', '', $fallback);
                    $fallback = str_replace('e-display-original-content', 'e-display-fallback-content', $fallback);
                    echo $fallback;
                }
            }
            $this->print_scripts($element, $settings);
        }
    }

    public function get_triggers() {        
        if (empty(self::$triggers)) {
            foreach (\EAddonsForElementor\Plugin::instance()->get_addons(true) as $addon) {
                if (!empty($addon['active']) && $addon['active'] == true) {
                    $triggers = glob($addon['path'].'/modules/*/triggers/*.php');       
                    foreach($triggers as $atrigger) {
                        //include_once($atrigger);
                        $trigger_slug = pathinfo($atrigger, PATHINFO_FILENAME);   
                        $trigger_name = Utils::slug_to_camel(str_replace('_', '-', $trigger_slug), '_');
                        $tmp = explode('/', $atrigger);
                        $module_name = Utils::slug_to_camel($tmp[count($tmp)-3]);
                        $trigger = '\\'.Utils::slug_to_camel($addon['TextDomain']).'\Modules\\'.$module_name.'\Triggers\\' . $trigger_name;
                        self::$triggers[$trigger_slug] = new $trigger($this);
                    }
                }
            }
            //do_action('e_addons/init_triggers', $this);
            //var_dump(array_keys(self::$triggers)); die();
        }        
        return self::$triggers;
    }

    /**
     * Add Controls
     *
     * @since 0.5.5
     *
     * @access private
     */
    private function add_controls($element, $args) {

        $section = (empty($args['section'])) ? 'advanced' : $args['section'];
        $element_type = $element->get_type();
        
        $current_section = $element->get_current_section();
        if ( !in_array($current_section['section'], array('e_section_display_advanced', 'e_section_display_fallback'))
                || $current_section['tab'] != 'e_display') {
                echo 'tab: '; var_dump($element->get_current_tab()); 
                echo 'section: '; var_dump($element->get_current_section()); 
                die();
            return;
        }
        
        if ($section == 'advanced') {

            $element->add_control(
                    'e_display_mode', [
                'label' => __('Display mode', 'elementor'),
                'type' => Controls_Manager::SELECT,
                'options' => [
                    '' => __('Default', 'elementor'),
                    'none' => __('Hide', 'elementor'),
                    'show' => __('Show if', 'elementor'),
                    'hide' => __('Hide if', 'elementor'),
                ],
                    ]
            );

            $_triggers = $this->get_triggers();
            $repeaters = $options = array();
            foreach ($_triggers as $ksection => $tsection) {
                $sub = $this->get_sub_controls($ksection);
                $options[$ksection] = $tsection->get_title();
                if (!empty($sub) && !empty($sub->get_controls())) {
                    foreach ($sub->get_controls() as $kcontrol => $acontrol) {
                        if ($kcontrol == '_id') { continue; }
                        $acontrol['condition']['e_display_trigger'] = $ksection;
                        $repeaters[$ksection][$kcontrol] = $acontrol;
                    }
                }
            }

            $repeater = new \Elementor\Repeater(array('id' => 'e_display_trigger_repeater'));
            $repeater->add_control(
                    'e_display_trigger', [
                //'label' => __('Type', 'elementor'),
                //'label' => var_export($args, true),
                //'label' => var_export($element->get_current_section(), true),
                'type' => Controls_Manager::SELECT,
                'options' => $options,
                'default' => 'post',
                'label_block' => true,
                'separator' => 'after',
                    ]
            );
            foreach ($repeaters as $ksection => $asection) {
                foreach ($asection as $kcontrol => $acontrol) {
                    $repeater->add_control($kcontrol, $acontrol);
                }
            }
            $element->add_control(
                    'e_display_repeater',
                    [
                        'label' => __('Triggers', 'e-addons'),
                        'type' => \Elementor\Controls_Manager::REPEATER,
                        'fields' => $repeater->get_controls(),
                        'title_field' => '{{{ e_display_trigger }}}',
                        /*'default' => array(
                            array(
                                'e_display_trigger' => '',
                            )
                        ),*/
                        'description' => __('If empty the Element will remain Hidden/Shown', 'e-addons'),
                        'condition' => [
                            'e_display_mode!' => ['', 'none'],
                        ],
                        'separator' => 'before',
                    ]
            );
            $element->add_control(
                    'e_display_logic', [
                'label' => __('Condition', 'elementor'),
                'type' => Controls_Manager::CHOOSE,
                'options' => array(
                    'and' => [
                        'title' => __('AND', 'elementor'),
                        'icon' => 'fa fa-minus-circle',
                    ],
                    'or' => [
                        'title' => __('OR', 'elementor'),
                        'icon' => 'fa fa-plus-circle',
                    ],
                ),
                'default' => 'and',
                'toggle' => false,
                'condition' => [
                    'e_display_mode!' => ['', 'none'],
                ],
                    ]
            );

            $element->add_control(
                    'e_display_dom', [
                'label' => __('<i class="fa fa-code" aria-hidden="true"></i> Keep HTML', 'elementor'),
                'type' => Controls_Manager::SWITCHER,
                'description' => __('Keep the element HTML in the DOM.', 'elementor') . '<br>' . __('Only hide this element via CSS.', 'elementor'),
                'condition' => [
                    'e_display_mode!' => [''],
                ],
                'separator' => 'before',
                    ]
            );

            Utils::add_help_control($this, $element);
        }

        if ($section == 'fallback') {
            $element->add_control(
                    'e_display_fallback', [
                'label' => __('Enable a Fallback Content', 'elementor'),
                'type' => Controls_Manager::SWITCHER,
                'description' => __("If you want to show something when the element is hidden", 'elementor'),
                    ]
            );
            $element->add_control(
                    'e_display_fallback_text', [
                'label' => __('Fallback', 'elementor'),
                'type' => Controls_Manager::WYSIWYG,
                'default' => "This element is currently hidden.",
                'description' => __("Insert here some content showed if the element is not visible", 'elementor'),
                'condition' => [
                    'e_display_fallback!' => '',
                ],
                    ]
            );
            if ($element_type == 'section') {
                $element->add_control(
                        'e_display_fallback_section', [
                    'label' => __('Use section wrapper', 'elementor'),
                    'type' => Controls_Manager::SWITCHER,
                    'default' => 'yes',
                    'description' => __('Mantain original section wrapper.', 'elementor'),
                    'condition' => [
                        'e_display_fallback!' => '',
                    ],
                        ]
                );
            }
        }
    }

    public function get_sub_controls($section) {

        $element = new \Elementor\Repeater();
        $triggers = $this->get_triggers();
        $triggers[$section]->get_trigger_controls($element);
        
        do_action('e_addons/display_trigger', $this);

        return $element;
    }

    public function set_element_view_counters($element, $hidden = false) {
        if (!\Elementor\Plugin::$instance->editor->is_edit_mode()) {
            $user_id = get_current_user_id();
            $settings = $element->get_settings_for_display();
            if ((!$hidden && $settings['e_display_mode'] == 'show') || ($hidden && $settings['e_display_mode'] == 'hide')) {
                //var_dump($settings);
                if (!empty($settings['e_display_max_user']) || !empty($settings['e_display_max_day']) || !empty($settings['e_display_max_total'])) {
                    $e_display_max = get_option('e_display_max', array());
                    // remove elements with no limits
                    foreach ($e_display_max as $ekey => $value) {
                        if ($ekey != $element->get_id()) {
                            $esettings = Utils::get_settings_by_element_id($ekey);
                            //var_dump($esettings);
                            if (empty($esettings['e_display_max_day']) && empty($esettings['e_display_max_total']) && empty($esettings['e_display_max_user'])) {
                                unset($e_display_max[$ekey]);
                            } else {
                                if (empty($esettings['e_display_max_day'])) {
                                    unset($e_display_max[$ekey]['day']);
                                }
                                if (empty($esettings['e_display_max_total'])) {
                                    unset($e_display_max[$ekey]['total']);
                                }
                                if (empty($esettings['e_display_max_user'])) {
                                    unset($e_display_max[$ekey]['user']);
                                }
                            }
                        }
                    }

                    //var_dump($e_display_max);
                    if (isset($e_display_max[$element->get_id()])) {
                        $today = date('Ymd');
                        /*
                          // save in cookie/usermeta
                          if (!empty($settings['e_display_max_user'])) {
                          $current_user_unique = get_current_user_id();
                          if (!$current_user_unique) {
                          $current_user_unique = wp_get_session_token();
                          }
                          $e_display_max_user = intval($e_display_max['user'][]) + 1;
                          } else {
                          $e_display_max_user = array();
                          }
                         */

                        if (!empty($settings['e_display_max_day'])) {
                            if (!empty($e_display_max[$element->get_id()]['day'][$today])) {
                                $e_display_max_day = $e_display_max[$element->get_id()]['day'];
                                $e_display_max_day[$today] = intval($e_display_max_day[$today]) + 1;
                            } else {
                                $e_display_max_day = array();
                                $e_display_max_day[$today] = 1;
                            }
                        } else {
                            $e_display_max_day = array();
                        }
                        if (!empty($settings['e_display_max_total'])) {
                            if (isset($e_display_max[$element->get_id()]['total'])) {
                                $e_display_max_total = intval($e_display_max[$element->get_id()]['total']) + 1;
                            } else {
                                $e_display_max_total = 1;
                            }
                        } else {
                            $e_display_max_total = 0;
                        }
                        if ($user_id && !empty($settings['e_display_max_user'])) {
                            if (!empty($e_display_max[$element->get_id()]['user'])) {
                                $e_display_max_user = $e_display_max[$element->get_id()]['user'];
                            } else {
                                $e_display_max_user = array();
                            }
                            $e_display_max_user[$user_id] = $user_id;
                        } else {
                            $e_display_max_user = array($user_id => $user_id);
                        }
                    } else {
                        $e_display_max_user = array($user_id => $user_id);
                        $e_display_max_day = array();
                        $e_display_max_total = 1;
                    }
                    $e_display_max[$element->get_id()] = array(
                        'day' => $e_display_max_day,
                        'total' => $e_display_max_total,
                        'user' => $e_display_max_user,
                    );
                    //var_dump($e_display_max);
                    update_option('e_display_max', $e_display_max);
                }
            }
            if ($settings['e_display_mode']) {
                if ($user_id && !empty($settings['e_display_max_user'])) {
                    $e_display_max_user = get_user_meta($user_id, 'e_display_max_user', true);
                    if (empty($e_display_max_user[$element->get_id()])) {
                        if (empty($e_display_max_user)) {
                            $e_display_max_user = array();
                        }
                        $e_display_max_user[$element->get_id()] = 2;
                    } else {
                        $e_display_max_user[$element->get_id()]++;
                    }
                    update_user_meta($user_id, 'e_display_max_user', $e_display_max_user);
                }
            }
        }
    }

    public function get_fallback($settings, $element = null) {

        if (!empty($settings['e_display_fallback'])) {

            $fallback_content = Utils::get_dynamic_data($settings['e_display_fallback_text']);

            if ($fallback_content && (!isset($settings['e_display_fallback_section']) || $settings['e_display_fallback_section'] == 'yes')) { // BUG - Fix it
                //var_dump($element->get_type());
                
                switch ($element->get_type()) {
                    case 'widget':                
                        $fallback_content = '<div class="elementor-widget-container">'. $fallback_content .'</div>';
                        break;
                    case 'section':                
                        $fallback_content = '
                            <div class="elementor-element elementor-column elementor-col-100 elementor-top-column" data-element_type="column">
                                <div class="elementor-column-wrap elementor-element-populated">
                                <div class="elementor-widget-wrap">
                                    <div class="elementor-element elementor-widget">
                                        <div class="elementor-widget-container e-display-fallback">'
                                        . $fallback_content .
                                        '</div>
                                    </div>
                                </div>
                                </div>
                            </div>';
                        break;
                }
                    
                ob_start();
                $element->_add_render_attributes();
                $element->before_render();
                echo $fallback_content;
                $element->after_render();
                $fallback_content = ob_get_clean();
            }

            return $fallback_content;
        }
        return '';
    }
    
    public function should_render($should_render, $element) {        
        if (!$element->get_settings('e_display_dom')) {
            if ($this->is_hidden()) {
                return false;
            }
        }
        return $should_render;
    }

    public function is_hidden($element = null, $why = false) {
        $e_settings = $element->get_settings_for_display();

        $hidden = $hidden_and = FALSE;
        $this->conditions = $conditions_and = array();
        $this->triggered = array();

        if (empty($e_settings['e_display_mode'])) {
            return false;
        } else {
            
            // FORCED HIDDEN
            if ($e_settings['e_display_mode'] == 'none') {
                $this->triggered['e_display_mode'] = $this->conditions['e_display_mode'] = __('Display', 'elementor');
                $hidden = TRUE;
            } else {

                if (!empty($e_settings['e_display_repeater'])) {
                    $triggers = $this->get_triggers();
                    foreach ($e_settings['e_display_repeater'] as $settings) {

                        $tkey = $settings['e_display_trigger'];
                        if (isset($triggers[$tkey])) {
                            $triggers[$tkey]->is_triggered($element, $settings);
                        }

                        if ($e_settings['e_display_logic'] == 'and') {
                            if (empty($this->conditions)) {
                                $hidden_and = true;
                            } else {
                                foreach ($this->conditions as $akey => $condition) {
                                    $conditions_and[$akey] = $condition;
                                }
                            }
                            $this->conditions = array();
                        }
                    }

                    if ($e_settings['e_display_logic'] == 'and') {
                        if (!$hidden_and) {
                            $this->conditions = $conditions_and;
                        }
                    }                    
                    if (\Elementor\Plugin::$instance->editor->is_edit_mode()) {
                        $this->conditions = $this->triggered;
                        $hidden = true;
                    }
                }
            }
        }

        $is_triggered = !empty($this->conditions);
        
        $mode = $e_settings['e_display_mode'] == 'show';
        if (self::display_condition($is_triggered, $mode)) {
            $hidden = TRUE;
        }
    
        if ($why) {
            return $this->conditions;
        }

        if ($hidden) {
            if($this->has_inspector()) {
                \EAddonsInspector\Modules\Inspector\Globals\Frontend_Inspector::$elements_hidden[$element->get_id()]['triggers'] = $this->triggered;
                \EAddonsInspector\Modules\Inspector\Globals\Frontend_Inspector::$elements_hidden[$element->get_id()]['conditions'] = $this->conditions;
                \EAddonsInspector\Modules\Inspector\Globals\Frontend_Inspector::$elements_hidden[$element->get_id()]['fallback'] = !empty($e_settings['e_display_fallback']); //$this->get_fallback($e_settings, $element);
            }
        }

        return $hidden;
    }

    static public function display_condition($condition, $display) {
        $ret = $condition;
        if ($display) {
            if ($condition) {
                $ret = false; // mostro il widget
            } else {
                $ret = true; // nascondo il widget
            }
        } else {
            if ($condition) {
                $ret = true; // nascondo il widget
            } else {
                $ret = false; // mostro il widget
            }
        }
        return $ret;
    }

    public function print_scripts($element, $settings = null) {
        if (!\Elementor\Plugin::$instance->editor->is_edit_mode()) {
            if (empty($settings)) {
                $settings = $element->get_settings_for_display();
            }
            if (!empty($this->triggered)) {
                $triggers_controls = array();
                $triggers = $this->get_triggers();
                foreach ($triggers as $tkey => $atrigger) {
                    $sub_controls = $this->get_sub_controls($tkey);
                    //var_dump($sub_controls); die();
                    $triggers_controls[$tkey] = $sub_controls->get_controls();                    
                }
                foreach ($triggers as $tkey => $atrigger) {
                    foreach ($this->triggered as $dkey => $atriggered) {                        
                        if (isset($triggers_controls[$tkey][$dkey])) {
                            $atrigger->print_trigger_scripts($element, $settings);
                        } 
                    }
                }
            }
        }
    }

}
